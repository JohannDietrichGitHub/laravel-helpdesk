# Tests et qualité

## Commandes

```sh
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
npm run types:check
npm run check
```

Docker Compose reste obligatoire pour les tests qui dépendent de MySQL ou des services du projet.

## Organisation

- `tests/Feature/Auth` couvre les parcours Fortify.
- `tests/Feature/Tickets` couvre l’autorisation ticket.
- `tests/Feature/Api` couvre tokens, ressources et filtrage API.
- `tests/Feature/DashboardTest.php` couvre les métriques et périmètres de rôle.
- `tests/Feature/AsyncWorkflowTest.php` couvre événements, listeners et queue.
- `tests/Feature/DomainModelTest.php` couvre modèles et relations.
- `tests/Feature/LocaleTest.php` couvre la locale.

## Ce qu’un test d’autorisation doit vérifier

Pour chaque endpoint sensible, tester au minimum l’accès autorisé et le refus pour les autres rôles. Tester aussi le cas d’un ticket appartenant à un autre client ou assigné à un autre agent.

## CI

La CI installe les dépendances, démarre MySQL, exécute migrations et tests, lance Pint, PHPStan, les contrôles TypeScript/frontend, les audits et le build Docker.
