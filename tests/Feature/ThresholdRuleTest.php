<?php

namespace Tests\Feature;

use App\Enums\AlertSeverity;
use App\Enums\VitalMetric;
use App\Models\ThresholdRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThresholdRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_draft_and_clinician_can_approve_then_deactivate_it(): void
    {
        $administrator = User::factory()->administrator()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($administrator)
            ->post(route('threshold-rules.store'), $this->validPayload())
            ->assertRedirect(route('threshold-rules.index'))
            ->assertSessionHas('status', 'threshold-created');

        $rule = ThresholdRule::query()->sole();
        $this->assertFalse($rule->is_active);
        $this->assertNull($rule->approved_at);
        $this->assertSame($administrator->id, $rule->created_by_user_id);
        $this->assertSame(VitalMetric::Temperature->unit(), $rule->unit);

        $this->actingAs($clinician)
            ->post(route('threshold-rules.approve', $rule))
            ->assertRedirect(route('threshold-rules.index'));

        $rule->refresh();
        $this->assertTrue($rule->is_active);
        $this->assertSame($clinician->id, $rule->approved_by_user_id);
        $this->assertNotNull($rule->approved_at);

        $this->post(route('threshold-rules.deactivate', $rule))
            ->assertRedirect(route('threshold-rules.index'));

        $rule->refresh();
        $this->assertFalse($rule->is_active);
        $this->assertSame($clinician->id, $rule->deactivated_by_user_id);
        $this->assertNotNull($rule->deactivated_at);
    }

    public function test_threshold_rule_requires_a_valid_range(): void
    {
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($administrator)
            ->post(route('threshold-rules.store'), [
                ...$this->validPayload(),
                'minimum_value' => null,
                'maximum_value' => null,
            ])
            ->assertSessionHasErrors('threshold');

        $this->post(route('threshold-rules.store'), [
            ...$this->validPayload(),
            'minimum_value' => 40,
            'maximum_value' => 38,
        ])->assertSessionHasErrors('maximum_value');

        $this->assertDatabaseCount('threshold_rules', 0);
    }

    public function test_rule_governance_permissions_are_separated_by_role(): void
    {
        $rule = ThresholdRule::factory()->create();
        $administrator = User::factory()->administrator()->create();
        $clinician = User::factory()->clinician()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($administrator)->get(route('threshold-rules.index'))->assertOk();
        $this->actingAs($clinician)->get(route('threshold-rules.index'))->assertOk();
        $this->actingAs($nurse)->get(route('threshold-rules.index'))->assertForbidden();

        $this->actingAs($clinician)
            ->post(route('threshold-rules.store'), $this->validPayload())
            ->assertForbidden();

        $this->actingAs($administrator)
            ->post(route('threshold-rules.approve', $rule))
            ->assertForbidden();

        $this->assertFalse($administrator->can('update', $rule));
        $this->assertFalse($clinician->can('update', $rule));
        $this->assertFalse($clinician->can('delete', $rule));
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'name' => 'Temperature review protocol',
            'metric' => VitalMetric::Temperature->value,
            'minimum_value' => null,
            'maximum_value' => 38,
            'severity' => AlertSeverity::Urgent->value,
            'guidance' => 'Review the measurement and assess the patient under the approved local clinical protocol.',
            'version' => 1,
        ];
    }
}
