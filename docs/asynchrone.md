# Queue, notifications et scheduler

## Notifications

Deux événements métier sont déclenchés par les workflows : `TicketReplyAdded` et `TicketStatusChanged`. Leurs listeners implémentent `ShouldQueue`, donc la requête web ne contacte pas directement le système mail.

```text
Contrôleur
  → événement
  → listener en queue
  → notification Laravel
  → mailer
  → Mailpit en local / SMTP en production
```

Les notifications ciblent le propriétaire et l’agent assigné, dédupliquent les utilisateurs et excluent l’auteur de l’action.

## Worker

Le service Docker `queue` consomme les jobs Laravel. En local, Mailpit reçoit les emails et les affiche sur <http://localhost:8025>. En production, configurez un mailer SMTP réel et surveillez les logs du worker.

## Scheduler

Le scheduler déclare une exécution quotidienne de `tickets:close-resolved`. Le service `scheduler` doit rester actif ; il ne remplace pas le worker de queue.

## Cache du dashboard

`DashboardMetrics` met en cache les agrégats cinq minutes avec des clés par rôle et utilisateur. Les changements de ticket invalident immédiatement les clés touchées ; l’expiration de cinq minutes fournit un filet de sécurité.
