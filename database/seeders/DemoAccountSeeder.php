<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Demo accounts may only be seeded in local or testing environments.');
        }

        $password = config('demo.seed_password');

        if (! is_string($password) || mb_strlen($password) < 12) {
            throw new LogicException('Set DEMO_SEED_PASSWORD to at least 12 characters before seeding demo accounts.');
        }

        $accounts = [
            ['name' => 'EdeHealth Administrator', 'email' => 'admin@edehealth.test', 'role' => UserRole::Administrator],
            ['name' => 'Dr Tola Adebayo', 'email' => 'clinician@edehealth.test', 'role' => UserRole::Clinician],
            ['name' => 'Nurse Kemi Balogun', 'email' => 'nurse@edehealth.test', 'role' => UserRole::Nurse],
            ['name' => 'Amina Adeyemi', 'email' => 'patient@edehealth.test', 'role' => UserRole::Patient],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'role' => $account['role'],
                    'is_active' => true,
                ],
            );
        }
    }
}
