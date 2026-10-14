# Routing instructions

- Keep web routes for Inertia HTML responses and API routes for versioned JSON responses.
- Group authenticated routes behind the appropriate middleware.
- Name routes consistently; consume them through Wayfinder-generated helpers where available.
- Keep route closures out of production code; use controllers or console command classes.
