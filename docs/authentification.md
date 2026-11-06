# Authentification et autorisation

## Session web

Fortify gère connexion, inscription, vérification d’email, reset de mot de passe, confirmation de mot de passe et authentification à deux facteurs. Les routes applicatives exigent `auth` et `verified`.

## Passkeys et 2FA

Les passkeys sont gérées par les contrôleurs Laravel Passkeys et les composants dédiés de `resources/js/components`. La 2FA utilise les écrans et endpoints Fortify configurés dans l’application. Les données de sécurité sont stockées sur l’utilisateur ; les secrets ne doivent jamais être ajoutés au dépôt.

## Rôles et policies

Le rôle est un enum (`client`, `agent`, `admin`) persisté sur `users`. `TicketPolicy` applique les règles :

- lecture : administrateur, propriétaire ou agent assigné ;
- création : tout utilisateur sauf administrateur ;
- réponse : toute personne pouvant lire le ticket ;
- statut : administrateur ou agent assigné ;
- assignation : administrateur uniquement.

## API Sanctum

L’API ne réutilise pas la session web comme contrat client : elle émet des tokens personnels via `/api/v1/auth/tokens`. Le client les envoie dans `Authorization: Bearer <token>`. Les tickets sont ensuite filtrés selon le rôle du compte associé au token.
