# Workflows ticket

## Créer un ticket

1. Le client ou l’agent ouvre `/tickets/create`.
2. `TicketController::create` vérifie la capacité `create` et charge les catégories actives.
3. Le formulaire envoie `POST /tickets`.
4. `StoreTicketRequest` valide titre, description, catégorie et priorité.
5. Le contrôleur ouvre une transaction.
6. Il crée le ticket avec l’utilisateur courant comme propriétaire.
7. Il crée le premier message avec la description du ticket.
8. Il invalide les métriques concernées et redirige vers le détail.

## Répondre

Le serveur valide le corps et les pièces jointes, crée le message, enregistre les fichiers sur le disque `local`, met à jour `last_reply_at` et déclenche `TicketReplyAdded`. Le listener notifie le propriétaire et l’agent, en excluant l’auteur.

## Assigner

Seul un administrateur peut appeler `PATCH /tickets/{ticket}/assignment`. `AssignTicketRequest` valide l’agent cible. Le contrôleur met à jour `assigned_to` et invalide le cache de l’administrateur, du propriétaire et des agents concernés.

## Changer le statut

Un administrateur ou l’agent assigné peut appeler `PATCH /tickets/{ticket}/status`. Le contrôleur mémorise l’ancien statut, applique le nouveau, gère `resolved_at`, invalide les métriques et déclenche `TicketStatusChanged`.

## Télécharger une pièce jointe

Le navigateur demande `/attachments/{attachment}/download`. Laravel recharge le ticket parent, vérifie `AttachmentPolicy`, puis diffuse le fichier depuis le disque configuré avec son nom et son type MIME d’origine.

## Fermer automatiquement

Le scheduler exécute quotidiennement `tickets:close-resolved`. La commande transforme en `closed` les tickets `resolved` depuis au moins sept jours.
