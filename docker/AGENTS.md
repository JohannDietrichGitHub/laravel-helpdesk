# Docker instructions

- `docker-compose.yml` is the canonical local and deployment composition.
- Keep PHP, queue, and scheduler on the same application image to prevent runtime drift.
- Do not copy `.env` or generated Laravel caches into images.
- Keep startup idempotent: migrations and storage setup may run on every container restart.
- Healthchecks must describe actual readiness, not merely process existence.
