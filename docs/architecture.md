# Architecture et interactions

## Vue d’ensemble

```text
Navigateur
   │ HTTP / Inertia ou JSON
   ▼
Nginx
   │ FastCGI
   ▼
PHP-FPM / Laravel
   ├── MySQL : utilisateurs, tickets, messages, pièces jointes, jobs
   ├── Cache : métriques du dashboard
   ├── Queue worker : notifications différées
   ├── Scheduler : fermeture automatique des tickets
   └── Mailpit ou SMTP : livraison des emails
```

## Services Docker

- **nginx** reçoit les requêtes HTTP et sert les assets compilés.
- **app** exécute PHP-FPM et Laravel.
- **db** fournit MySQL avec un volume persistant.
- **queue** exécute les jobs Laravel en attente.
- **scheduler** exécute le planificateur Laravel.
- **mailpit** intercepte les emails en développement.

Le navigateur ne parle jamais directement à MySQL, au worker ou au scheduler. Toutes les données passent par Laravel, qui applique validation et autorisation.

## Deux interfaces serveur

### Interface web

Les routes de [routes/web.php](../routes/web.php) retournent des pages Inertia ou des redirections. React reçoit des props typées et utilise `useForm` pour les mutations.

### API

Les routes de [routes/api.php](../routes/api.php) retournent du JSON. Elles utilisent Sanctum, des API Resources et la même logique de filtrage par rôle que l’interface web.

## Sécurité des frontières

Les contrôles d’affichage React ne sont pas une sécurité. Chaque endpoint sensible vérifie le droit côté serveur avec une policy. Les pièces jointes privées passent par Laravel avant d’être téléchargées.
