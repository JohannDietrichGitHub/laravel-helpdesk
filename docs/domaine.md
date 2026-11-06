# Domaine métier

## Rôles

| Rôle | Visibilité principale | Actions métier |
|---|---|---|
| Client | Ses propres tickets | Créer un ticket, consulter et commenter ses tickets |
| Agent | Tickets qui lui sont assignés | Consulter, répondre et changer le statut de ses tickets |
| Administrateur | Tous les tickets | Consulter, répondre, changer le statut et assigner les tickets |

La source de vérité est [app/Enums/Role.php](../app/Enums/Role.php) et la policy correspondante. La visibilité de l’interface ne remplace jamais la policy.

## Ticket

Un ticket appartient à un utilisateur, peut être assigné à un agent et appartient à une catégorie. Il possède des messages, des tags et des dates de suivi (`last_reply_at`, `resolved_at`). Les tickets sont supprimables logiquement.

## Statuts

Définis dans [app/Enums/TicketStatus.php](../app/Enums/TicketStatus.php) :

`open` → `in_progress` → `waiting_on_client` → `resolved` → `closed`

Le code n’impose pas une seule transition linéaire, mais le changement de statut reste réservé à un administrateur ou à l’agent assigné. Un passage à `resolved` renseigne `resolved_at` ; un autre statut l’efface.

## Priorités

`low`, `medium`, `high`, `urgent`. Elles servent au filtrage et aux métriques du dashboard.

## Relations

```text
User 1 ── N Ticket
User 1 ── N Message
Ticket 1 ── N Message
Message 1 ── N Attachment
Ticket N ── N Tag
Category 1 ── N Ticket
```

Voir le diagramme complet dans [schema.md](schema.md).
