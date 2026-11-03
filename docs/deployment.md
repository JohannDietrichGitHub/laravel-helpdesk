# Deployment

## Target

The release workflow publishes the `app` target to GitHub Container Registry on version tags. Deploy that image to a container platform that supports a web process, a persistent MySQL service, a long-running queue worker, and a scheduler process. The image exposes PHP-FPM behind Nginx; do not run `php artisan serve` in production.

## Required secrets

- `APP_KEY`
- `APP_URL`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, and provider credentials when using external SMTP
- `SANCTUM_STATEFUL_DOMAINS` if browser-based Sanctum clients are added

Keep `.env` out of source control. Generate `APP_KEY` once per environment and back up encrypted database credentials through the hosting provider.

## Release procedure

1. Build and scan the immutable image.
2. Run `php artisan migrate --force` against the release database.
3. Start the app and Nginx processes.
4. Start queue and scheduler processes from the same image.
5. Verify `/up`, the app healthcheck, queue logs, scheduler logs, and a database-backed request.

## Rollback

Roll back the image tag first. Database migrations must be backward-compatible; only run `php artisan migrate:rollback --step=1` after confirming the previous application version supports the resulting schema. Restore the database from the provider backup for destructive failures.

## Runtime checks

The Compose app healthcheck runs `php artisan about`. Nginx exposes the application on port 80. MySQL uses `mysqladmin ping`; queue and scheduler are separate restartable services.
