# EdeHealth Sentinel

EdeHealth Sentinel is an installable Progressive Web Application for longitudinal health monitoring and clinician-supervised disease-risk support at the Medical Centre, Federal Polytechnic Ede.

> The application is under active development. It is not yet approved for real clinical use, emergency care or autonomous diagnosis.

## Current Implementation

- Laravel 12 application foundation.
- Secure registration, login, password reset and profile management.
- Patient-only public registration with protected clinical and administrator roles.
- Active/inactive account enforcement.
- Patient and staff profile records with least-privilege authorization.
- Patient self-service health-profile form with validated clinical identifiers.
- Patient-managed structured medical history with clinician/nurse read access.
- Versioned, append-only consent preferences with explicit withdrawal records.
- Role-protected clinical encounters with patient visit history, staff search, assignment and immutable completion.
- Append-only structured symptoms and verified vital-sign snapshots with explicit units, provenance and timestamps.
- Verified numeric and qualitative clinical results with source reference ranges, reported flags and specimen context.
- Longitudinal patient timeline with on-demand Chart.js trends built only from verified vital-sign snapshots.
- Original responsive landing page and authenticated dashboard shell.
- Reload-free Turbo Drive navigation with delayed, accessible app-bar loading feedback.
- Clinical-page snapshot caching and hover prefetching disabled to avoid retaining sensitive rendered pages.
- PWA manifest, original icons, service worker and offline fallback.
- Safe caching limited to versioned static assets; private pages are not precached.
- Automated tests covering authentication, accounts, clinical-profile permissions, consent history, PWA metadata and caching boundaries.

See [ROADMAP.md](ROADMAP.md) for the full scope and implementation status.

## Technology

- PHP 8.2+
- Laravel 12
- Blade, Alpine.js and Tailwind CSS
- Vite
- SQLite for the zero-configuration local default
- MySQL for the planned XAMPP/staging deployment
- PHPUnit

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

For active frontend development, run `npm run dev` in a second terminal.

## MySQL Configuration

Create a dedicated MySQL database and account, then update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edehealth_sentinel
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Then run:

```bash
php artisan migrate
```

Do not use the MySQL root account or an empty password outside a local development environment.

## Local Demo Accounts

Demo data is disabled by default in `.env.example`. To create local demonstration accounts, add a password of at least 12 characters to your ignored `.env`:

```dotenv
DEMO_SEED_ENABLED=true
DEMO_SEED_PASSWORD="choose-a-local-demo-password"
```

Run:

```bash
php artisan db:seed
```

The idempotent seeder creates these fictional local accounts:

| Role | Email |
|---|---|
| Administrator | `admin@edehealth.test` |
| Clinician | `clinician@edehealth.test` |
| Nurse | `nurse@edehealth.test` |
| Patient | `patient@edehealth.test` |

It also creates one fictional patient profile, its medical history, four consent preferences, one completed demonstration encounter, a symptom, three vital-sign snapshots for trend demonstration and a clinical result, plus three staff profiles. The seeder refuses to run outside `local` and `testing` environments. Never reuse the local demo password for staging or production accounts.

## XAMPP Notes

The web server's document root or virtual host must point to this project's `public/` directory, not the repository root. Laravel requires write access to `storage/` and `bootstrap/cache/`.

PWA installation and service workers require HTTPS in deployed environments. Browsers permit HTTP service workers only on local development hosts such as `localhost` and `127.0.0.1`.

## Quality Checks

```bash
vendor/bin/pint
npm run build
php artisan test
```

## PWA Safety Model

The service worker caches only the offline page, manifest, icons and safe static build assets. Navigation requests use the network and fall back to the offline page when unavailable. Authenticated HTML and clinical API responses must not be indiscriminately cached.

Offline clinical drafts and synchronization are planned but not implemented yet. They require encryption, conflict handling, authentication revalidation and server confirmation before they can be considered safe.

## AI Boundary

The disease-risk model is intentionally not implemented until the target disease, intended population, reference outcome, authorized dataset and evaluation protocol are approved. A public dataset may support a research prototype, but it cannot by itself establish clinical validity for the Federal Polytechnic Ede population.

## License and Use

Licensing and institutional ownership are to be confirmed before distribution. Until then, treat the repository as an academic research project with restricted clinical use.
