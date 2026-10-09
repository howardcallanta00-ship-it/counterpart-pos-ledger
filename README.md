# Counterpart POS

Counterpart is a server-rendered CodeIgniter 4.7 application for protected customer and staff account management. It uses MySQL or MariaDB, session authentication, CSRF protection, migrations, seeders, secure image re-encoding, responsive views, and a small dependency surface.

This is an educational demonstration, not a payment processor. Free and evaluation hosting has capacity, persistence, cold-start, and availability limits.

## Local setup

Requirements: PHP 8.2 or newer with `intl`, `mbstring`, `mysqli`, and `gd`; Composer 2; MySQL 8 or MariaDB 10.6 or newer.

```sh
cp .env.example .env
composer install
php spark migrate --all
DEMO_SEED_ENABLED=true php spark db:seed DemoSeeder
php spark serve
```

On Windows PowerShell, set `$env:DEMO_SEED_ENABLED='true'` before the seed command. Edit `.env` first and use a unique local database password. The default documented demo login is `admin` / `Counterpart!2026`; change `DEMO_ADMIN_USERNAME` and `DEMO_ADMIN_PASSWORD` before seeding to override it. Seed execution is refused outside the testing environment unless `DEMO_SEED_ENABLED=true` is explicitly set.

The local avatar directory is `public/uploads/avatars`. Its contents are ignored by Git and Apache denies executable PHP-like files. Ensure the directory is writable by the web process.

The Docker alternative is:

```sh
docker compose up --build -d
docker compose exec app php spark db:seed DemoSeeder
```

Open `http://localhost:8080`. Stop it with `docker compose down`; add `-v` only when you intentionally want to remove the local database and avatar volumes.

## Tests and checks

Tests use the isolated `tests` database group, which defaults to in-memory SQLite. They never select the deployment database while `CI_ENVIRONMENT=testing`.

```sh
composer test
composer audit
php spark routes
```

Feature coverage includes public pages, authentication, protected redirects, logout, customer validation and updates, case-insensitive username uniqueness, password hashing and replacement, malformed and oversized avatars, missing-record 404 responses, and CSRF rejection.

## Configuration

Copy `.env.example`; never commit `.env`. CodeIgniter reads dotted keys or underscore aliases:

- `app.baseURL`, `CI_ENVIRONMENT`
- `database.default.hostname`, `.database`, `.username`, `.password`, `.DBDriver`, `.port`
- `session.cookieSecure=true` behind HTTPS, plus HTTP-only and `SameSite=Lax`
- `avatar.driver=local` for a persistent writable filesystem
- `avatar.driver=object` for an ephemeral host

The object driver expects a deployer-managed HTTPS upload gateway with `avatar.object.putUrlTemplate`, optional delete template, public base URL, and optional bearer token. The `{key}` placeholder receives a random `.png` filename. This keeps cloud SDK dependencies and provider credentials out of the application. If the gateway is missing, avatar mutation is disabled and the local placeholder remains visible. Existing customer and account workflows continue to work.

## Render Free

Create a Blueprint from `render.yaml`, attach a remote MySQL or MariaDB service, and set all environment values marked `sync: false`. Set `app_baseURL` to the final HTTPS URL. Do not use local avatar storage: Render Free has an ephemeral filesystem, so configure the object gateway or accept disabled avatar changes. The container installs production dependencies, serves only `public/`, binds to `PORT`, runs idempotent migrations before Apache starts, and exposes `/health`.

Free Render services currently spin down after 15 idle minutes and can take about a minute to wake. Local changes are lost on restart, redeploy, or spin-down, and free PostgreSQL expires after 30 days; PostgreSQL is not used by this MySQL-targeted project. External databases and object storage consume outbound bandwidth. Free-plan terms, capacity, and pricing can change, so check Render's current documentation before deployment.

## Clever Cloud

Create a native PHP application from this Git repository and link a MySQL add-on. Configure:

```text
CC_WEBROOT=/public
CC_PHP_VERSION=8.3
CC_COMPOSER_VERSION=2
CC_PHP_DEV_DEPENDENCIES=ignore
CI_ENVIRONMENT=production
app_baseURL=https://your-app.example/
session_cookieSecure=true
```

Linked MySQL variables (`MYSQL_ADDON_HOST`, `PORT`, `DB`, `USER`, and `PASSWORD`) are mapped by `Config\\Database`; no credentials belong in source. Run `php spark migrate --all` from an authorized deployment job or console, then run the seeder only for a deliberate demo environment with `DEMO_SEED_ENABLED=true`. Git and GitHub deployments both work with Composer's committed lock file.

For avatars, use `avatar.driver=local` only when a suitable persistent FS Bucket is mounted at `public/uploads/avatars`; otherwise use the object gateway. Clever Cloud signup credits and MySQL DEV plans are evaluation resources with tight limits and no production SLA. Application hosting is not guaranteed to remain permanently free. Confirm current plans and prices before creating resources.

## Security and operations

All mutations use POST, CSRF is global, auto-routing is disabled, protected routes use the authentication filter, identifiers are numeric route segments, and return destinations are allow-listed. Passwords are hashed with `PASSWORD_DEFAULT`, login errors are generic, attempts are session-throttled, sessions regenerate on login, and responses receive CSP, frame, content-type, permissions, and referrer headers.

For production, keep `CI_ENVIRONMENT=production`, use HTTPS, set secure cookies, rotate database and object gateway credentials, back up the database, monitor `/health`, and do not expose `writable/` or the project root through the web server.

## Assets and license

The placeholder avatar and interface styles were created for this repository and have no third-party asset dependency. Application source is provided under the MIT License in `LICENSE`. Framework and development dependencies retain their own licenses as recorded by Composer.
