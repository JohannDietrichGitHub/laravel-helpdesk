# Frontend React et Inertia

## Démarrage

[resources/js/app.tsx](../resources/js/app.tsx) initialise Inertia, choisit le layout selon le nom de page et monte les composants globaux de tooltip et de toast. Le layout applicatif contient la navigation ; les pages sont sous `resources/js/pages`.

## Flux de données

- Les données serveur arrivent dans les props Inertia de la page.
- Les mutations utilisent `useForm` pour conserver erreurs, progression et état d’envoi.
- Les filtres et la pagination des tickets vivent dans l’URL, pas dans un état local isolé.
- Les types métier partagés sont dans `resources/js/types`.
- Les composants réutilisables sont dans `resources/js/components`.

## Pages principales

- `dashboard.tsx` : métriques et tickets récents.
- `tickets/index.tsx` : liste, recherche, filtres et pagination.
- `tickets/create.tsx` : création avec validation.
- `tickets/show.tsx` : détail, timeline, réponse, fichiers et actions de statut.
- `auth/` : connexion, inscription, vérification, reset et 2FA.
- `settings/` : profil, sécurité et apparence.

## Wayfinder

Les helpers générés sous `resources/js/routes` et `resources/js/actions` évitent de recopier les URLs et méthodes HTTP dans les composants. Après une modification de routes, la génération Wayfinder doit rester cohérente avec le code source.

## Règle de sécurité

Un bouton masqué pour un rôle améliore l’UX, mais ne protège aucune donnée. Le backend doit toujours refuser l’action via middleware, Form Request ou policy.
