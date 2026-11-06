# Déploiement

## Cible

Le workflow de release publie la cible `app` dans GitHub Container Registry lors des tags de version. Déployez cette image sur une plateforme supportant un processus web, MySQL persistant, un worker de queue long-lived et un scheduler. L’image expose PHP-FPM derrière Nginx ; ne lancez pas `php artisan serve` en production.

## Secrets requis

- `APP_KEY`
- `APP_URL`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, and provider credentials when using external SMTP
- `SANCTUM_STATEFUL_DOMAINS` if browser-based Sanctum clients are added

Gardez `.env` hors du contrôle de version. Générez `APP_KEY` une fois par environnement et sauvegardez les identifiants de base de données de manière chiffrée chez l’hébergeur.

## Procédure de release

1. Construire et scanner l’image immuable.
2. Exécuter `php artisan migrate --force` sur la base de release.
3. Démarrer l’application et Nginx.
4. Démarrer la queue et le scheduler depuis la même image.
5. Vérifier `/up`, le healthcheck, les logs de queue et scheduler, ainsi qu’une requête utilisant la base.

## Rollback

Commencez par revenir au tag d’image précédent. Les migrations doivent rester rétrocompatibles ; n’exécutez `php artisan migrate:rollback --step=1` qu’après avoir vérifié que la version précédente supporte le schéma obtenu. Pour une régression destructive, restaurez la base depuis la sauvegarde du fournisseur.

## Vérifications runtime

Le healthcheck du service Compose `app` exécute `php artisan about`. Nginx expose l’application sur le port 80. MySQL utilise `mysqladmin ping`; la queue et le scheduler sont des services séparés et redémarrables.
