# API REST

L’API versionnée est disponible sous `/api/v1` et utilise des tokens personnels Laravel Sanctum.

## Authentification

```http
POST /api/v1/auth/tokens
Content-Type: application/json

{"email":"client@example.test","password":"password","device_name":"cli"}
```

Envoyez le token retourné dans `Authorization: Bearer <token>`.

## Tickets

Les requêtes authentifiées disponibles sont :

- `GET /api/v1/tickets?page=1&status=open&priority=high&search=export`
- `GET /api/v1/tickets/{ticket}`
- `POST /api/v1/tickets` with `title`, `description`, `category_id`, and `priority`

Les listes sont paginées et utilisent le même périmètre de rôle que l’application Inertia. Les clients voient leurs tickets, les agents leurs tickets assignés et les administrateurs tous les tickets.

Les requêtes sont limitées à 60 par minute et par utilisateur authentifié ou adresse IP source.

## Révocation

```http
DELETE /api/v1/auth/tokens/current
Authorization: Bearer <token>
```
