# Backend Laravel

## Organisation

- `routes/` : frontières web, API et commandes.
- `app/Http/Controllers/` : coordination HTTP uniquement.
- `app/Http/Requests/` : validation et autorisation d’entrée.
- `app/Policies/` : autorisation par ressource.
- `app/Models/` : relations, casts et accès aux données.
- `app/Enums/` : états et rôles finis.
- `app/Events/`, `app/Listeners/`, `app/Notifications/` : effets asynchrones.
- `app/Services/` : calculs transverses, notamment les métriques.

## Route web ticket

| Méthode | URL | Responsabilité |
|---|---|---|
| GET | `/tickets` | Liste filtrée et paginée |
| GET | `/tickets/create` | Formulaire de création |
| POST | `/tickets` | Création ticket + premier message |
| GET | `/tickets/{ticket}` | Détail et timeline |
| POST | `/tickets/{ticket}/messages` | Réponse et fichiers |
| PATCH | `/tickets/{ticket}/status` | Changement de statut |
| PATCH | `/tickets/{ticket}/assignment` | Assignation admin |
| GET | `/attachments/{attachment}/download` | Téléchargement autorisé |

Les routes ticket sont derrière `auth` et `verified`.

## Données partagées Inertia

`HandleInertiaRequests` partage le nom de l’application, la locale, les traductions communes, l’utilisateur authentifié, les messages flash et l’état de la sidebar.

## Autorisation

Une Form Request peut refuser une action avant le contrôleur ; la policy protège ensuite la ressource concrète. Cette double couche sépare la validité de la requête du droit d’accès à l’objet.
