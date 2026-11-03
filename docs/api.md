# REST API

The versioned API is available under `/api/v1` and uses Laravel Sanctum personal access tokens.

## Authentication

```http
POST /api/v1/auth/tokens
Content-Type: application/json

{"email":"client@example.test","password":"password","device_name":"cli"}
```

Use the returned token as `Authorization: Bearer <token>`.

## Tickets

Authenticated requests support:

- `GET /api/v1/tickets?page=1&status=open&priority=high&search=export`
- `GET /api/v1/tickets/{ticket}`
- `POST /api/v1/tickets` with `title`, `description`, `category_id`, and `priority`

Ticket list results are paginated and filtered through the same role scope as the Inertia application. Clients see owned tickets, agents see assigned tickets, and administrators see all tickets.

Requests are limited to 60 per minute per authenticated user or source IP.

## Revocation

```http
DELETE /api/v1/auth/tokens/current
Authorization: Bearer <token>
```
