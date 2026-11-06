# Helpdesk

[![CI](https://github.com/OWNER/REPOSITORY/actions/workflows/tests.yml/badge.svg)](https://github.com/OWNER/REPOSITORY/actions/workflows/tests.yml)

Application de gestion de tickets construite avec Laravel 13, Inertia 3, React 19, TypeScript, Tailwind CSS 4, MySQL et Docker Compose.

## Fonctionnalités

- Gestion des tickets, catégories, tags, priorités, statuts et pièces jointes.
- Vues et actions adaptées aux rôles client, agent et administrateur.
- Recherche, filtres, tri et pagination côté serveur.
- Tableau de bord avec volumes par statut/priorité et temps moyen de résolution.
- Notifications asynchrones pour les réponses et changements de statut.
- Fermeture planifiée des tickets résolus après sept jours.
- API REST versionnée avec authentification Sanctum et limitation de débit.
- Interface Inertia/React responsive avec thème sombre et socle d’internationalisation.

## Démarrage local

Docker Compose est l’environnement d’exécution officiel. Ne lancez pas PHP, MySQL ou un serveur mail directement sur l’hôte.

```sh
cp .env.example .env
docker compose up --build --detach --wait
docker compose exec app php artisan migrate:fresh --seed --force
```

Services disponibles :

- Application : <http://localhost:8000>
- Mailpit : <http://localhost:8025>
- MySQL, PHP-FPM, Nginx, worker de queue et scheduler : services internes Docker

Pour arrêter l’environnement :

```sh
docker compose down
```

## Comptes de démonstration

Le seeder crée un administrateur, deux agents et cinq clients. Par défaut, l’administrateur est :

```text
Email    : admin@example.com
Mot de passe : password
```

Ces valeurs sont configurables avec `DEMO_ADMIN_NAME`, `DEMO_ADMIN_EMAIL` et `DEMO_ADMIN_PASSWORD` dans l’environnement. Changez-les avant d’exposer une instance et ne réutilisez jamais ces identifiants en production.

## Architecture

- `app/` contient les modèles, policies, Form Requests, contrôleurs et notifications Laravel.
- `resources/js/` contient les pages Inertia, composants React et types TypeScript.
- Les policies et Form Requests appliquent respectivement l’autorisation et la validation côté serveur.
- Inertia partage l’utilisateur authentifié, son rôle, la locale, les traductions et les messages flash.
- Le worker traite les notifications ; Mailpit les intercepte en local.
- Le scheduler ferme automatiquement les tickets résolus depuis sept jours.
- Les pièces jointes sont stockées en privé et protégées par l’autorisation liée au ticket.

Schéma relationnel : [docs/schema.md](docs/schema.md).

## Documentation technique

Pour comprendre le projet dans l’ordre :

1. [Guide de lecture](docs/guide.md)
2. [Architecture et interactions](docs/architecture.md)
3. [Cycle d’une requête](docs/fonctionnement.md)
4. [Domaine métier](docs/domaine.md)
5. [Workflows ticket](docs/workflows.md)
6. [Backend Laravel](docs/backend.md)
7. [Frontend React/Inertia](docs/frontend.md)
8. [Authentification et autorisation](docs/authentification.md)
9. [Traitements asynchrones](docs/asynchrone.md)
10. [Tests](docs/tests.md)
11. [Glossaire](docs/glossaire.md)

## Vérifications qualité

```sh
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
npm run types:check
npm run check
docker compose build
```

La CI exécute également les migrations MySQL, les tests PHP et API, les contrôles frontend, les audits de dépendances, l’analyse d’image et les vérifications Docker.

## API REST

L’API est disponible sous `/api/v1` et utilise des tokens personnels Laravel Sanctum.

```http
POST /api/v1/auth/tokens
Content-Type: application/json

{"email":"client@example.test","password":"password","device_name":"cli"}
```

Endpoints principaux :

- `GET /api/v1/tickets`
- `GET /api/v1/tickets/{ticket}`
- `POST /api/v1/tickets`
- `DELETE /api/v1/auth/tokens/current`

Les résultats sont paginés et filtrables (`status`, `priority`, `search`). Les clients voient leurs tickets, les agents leurs tickets assignés et les administrateurs l’ensemble des tickets. La limite est de 60 requêtes par minute et par utilisateur authentifié ou adresse IP.

Référence complète : [docs/api.md](docs/api.md).

## Déploiement

Le workflow de release publie l’image `app` dans GitHub Container Registry lors des tags de version. La production doit fournir un processus web derrière Nginx, MySQL persistant, worker de queue et scheduler.

La procédure complète — secrets, migrations, healthchecks et rollback — est décrite dans [docs/deployment.md](docs/deployment.md).

## Licence

Projet sous licence MIT.
