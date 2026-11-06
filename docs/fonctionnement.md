# Cycle d’une requête

## Visite d’une page

1. Le navigateur demande une URL à Nginx.
2. Nginx transmet PHP à PHP-FPM.
3. Laravel applique les middlewares web : apparence, locale, Inertia et headers d’assets.
4. La route sélectionne un contrôleur.
5. Le contrôleur charge les modèles nécessaires avec eager loading.
6. `Inertia::render()` renvoie le nom de page et ses props.
7. Le bootstrap React sélectionne le layout puis rend la page.

Lors d’une navigation Inertia, le navigateur reste sur la même page HTML : seules les données et le composant de page sont échangés.

## Mutation d’un formulaire

```text
Formulaire React
  → useForm Inertia
  → route POST/PATCH
  → middleware auth/verified
  → Form Request
  → Policy
  → contrôleur
  → transaction et modèles
  → événement éventuel
  → redirection Inertia + flash
  → toast côté React
```

Les erreurs de validation reviennent dans l’état du formulaire Inertia. Les messages flash `success` et `error` sont partagés globalement par `HandleInertiaRequests`.

## Exemple : réponse à un ticket

Le formulaire appelle `POST /tickets/{ticket}/messages`. `StoreMessageRequest` vérifie le contenu et les fichiers, puis la policy `reply` vérifie l’accès au ticket. Le contrôleur crée le message et ses pièces jointes dans une transaction, met à jour `last_reply_at`, déclenche `TicketReplyAdded`, puis redirige. Le listener en queue notifie le propriétaire et l’agent, sauf l’auteur de la réponse.
