# Guide de lecture du projet

Ce guide est le point de départ pour comprendre Helpdesk. Le projet est une application web monolithique Laravel : le backend, le frontend Inertia/React et les traitements asynchrones vivent dans le même dépôt et sont exécutés par Docker Compose.

## Ordre recommandé

1. **Architecture** explique les processus et leurs frontières : navigateur, Nginx, PHP, MySQL, queue et scheduler.
2. **Fonctionnement** suit une requête HTTP de bout en bout.
3. **Domaine** décrit les rôles, les tickets et les états persistés.
4. **Workflows** détaille les actions métier principales.
5. **Backend** et **Frontend** expliquent où se trouve chaque responsabilité dans le code.
6. **Authentification** et **Asynchrone** couvrent les préoccupations transverses.

## Règle générale de responsabilité

- React affiche l’état fourni par le serveur et collecte les actions utilisateur.
- Inertia transporte les pages et les formulaires sans imposer une API JSON pour l’interface web.
- Les routes choisissent le contrôleur.
- Les Form Requests valident les entrées et autorisent les actions.
- Les contrôleurs coordonnent HTTP, transactions et redirections.
- Les policies décident si un utilisateur peut agir sur une ressource.
- Les modèles portent les relations, casts et scopes de données.
- Les événements signalent les effets métier ; les listeners en queue exécutent les notifications.

## Démarrage de lecture dans le code

- Routes web : [routes/web.php](../routes/web.php)
- Routes API : [routes/api.php](../routes/api.php)
- Contrôleur des tickets : [app/Http/Controllers/TicketController.php](../app/Http/Controllers/TicketController.php)
- Page ticket : [resources/js/pages/tickets/show.tsx](../resources/js/pages/tickets/show.tsx)
- Modèle ticket : [app/Models/Ticket.php](../app/Models/Ticket.php)
- Autorisation ticket : [app/Policies/TicketPolicy.php](../app/Policies/TicketPolicy.php)
