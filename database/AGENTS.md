# Database instructions

- Migrations must be forward-safe and use explicit foreign-key delete behavior.
- Add indexes for ticket filters, assignments, status, priority, and timestamps.
- Factories must create realistic relationship graphs without hidden global state.
- Seeders must be deterministic enough for demos and safe to run through `migrate:fresh --seed`.
- Never edit an existing migration after it may have been applied; add a new migration.
