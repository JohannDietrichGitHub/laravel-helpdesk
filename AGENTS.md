# Helpdesk project instructions

## Stack

- Laravel 13, PHP 8.4+, Inertia 3, React 19, TypeScript, Tailwind 4.
- MySQL is the only supported application database.
- Docker Compose is the canonical runtime. Do not add host-only setup paths.
- Services: `app` (PHP-FPM), `nginx`, `db`, `queue`, `scheduler`, and `mailpit`.

## Runtime commands

```bash
cp .env.example .env
php artisan key:generate
docker compose up --build
```

Use `docker compose exec app ...` for Artisan and PHP commands when the stack is running.
Do not commit `.env`, generated assets, `vendor/`, `node_modules/`, or Wayfinder output.

## Backend conventions

- Keep controllers thin; put validation in Form Requests and authorization in Policies.
- Use PHP backed Enums for ticket status, priority, and user role.
- Use explicit relationships and eager loading to avoid N+1 queries.
- Use service/action classes only when a workflow crosses multiple models or side effects.
- Queue mail and notifications; never perform external or slow work in web requests.
- Every authorization-sensitive endpoint must have a policy test.

## Frontend conventions

- Pages are Inertia React TypeScript components under `resources/js/pages`.
- Use Inertia `useForm` for mutations and preserve server-side validation errors.
- Keep shared domain types in `resources/js/types`.
- Keep reusable UI primitives in `resources/js/components`.
- Synchronize filters and pagination with the URL, not local-only state.

## Quality gates

Before considering a feature complete:

```bash
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
npm run types:check
npm run check
docker compose build
```

Prefer focused tests first, then the full suite. Do not weaken tests or static analysis to make a build pass.

## Scope guides

- `app/AGENTS.md`: models, policies, requests, controllers, services.
- `database/AGENTS.md`: migrations, factories, seeders, indexes.
- `resources/AGENTS.md`: Inertia, React, TypeScript, UI.
- `routes/AGENTS.md`: web, console, and API route boundaries.
- `tests/AGENTS.md`: Pest organization and authorization coverage.
- `docker/AGENTS.md`: image, Compose, entrypoint, and runtime rules.

## Change discipline

- Make the smallest change that satisfies the feature.
- Preserve Docker-first behavior: `docker compose up --build` must remain the acceptance smoke test.
- Update the roadmap in `.omo/plans/helpdesk-roadmap.md` when a milestone changes state.
