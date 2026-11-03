# Helpdesk

[![CI](https://github.com/OWNER/REPOSITORY/actions/workflows/tests.yml/badge.svg)](https://github.com/OWNER/REPOSITORY/actions/workflows/tests.yml)

Laravel 13, Inertia 3, React 19, TypeScript, Tailwind 4, MySQL, and Docker Compose helpdesk application.

## Local setup

```sh
cp .env.example .env
docker compose up --build --detach --wait
docker compose exec app php artisan migrate:fresh --seed --force
```

Application: `http://localhost:8000`  
Mailpit: `http://localhost:8025`

The stack contains PHP-FPM, Nginx, MySQL, a queue worker, a scheduler, and Mailpit. Do not run host-only PHP or database services; Docker Compose is the canonical runtime.

## Demo roles

Demo users are created by `DatabaseSeeder`. Inspect the seeder output or configured demo credentials before sharing an environment; never commit production credentials.

## Quality gates

```sh
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
npm run types:check
npm run check
docker compose build
```

CI additionally runs dependency audits, MySQL migrations, feature/unit tests, frontend checks, image scanning, and Docker E2E tests.

## Architecture

- Policies and Form Requests enforce authorization and validation on the server.
- Inertia shares authenticated user, role, locale, translations, and flash state.
- Queue workers deliver status/reply notifications through Mailpit locally.
- The scheduler closes resolved tickets after seven days.
- `/api/v1` exposes Sanctum-authenticated, policy-scoped ticket resources; see [docs/api.md](docs/api.md).

## Production release

See [docs/deployment.md](docs/deployment.md) for the Docker runtime topology, secrets, health checks, migration/rollback procedure, and deployment checklist.

## Data model

See [docs/schema.md](docs/schema.md).
