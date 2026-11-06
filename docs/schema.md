# Schéma de données

```mermaid
erDiagram
    users ||--o{ tickets : owns
    users ||--o{ tickets : assigned
    categories ||--o{ tickets : categorizes
    tickets ||--o{ messages : contains
    users ||--o{ messages : authors
    messages ||--o{ attachments : includes
    tickets }o--o{ tags : tagged
    users ||--o{ personal_access_tokens : authenticates
```

Les tickets utilisent la suppression logique et sont indexés par statut, priorité, assignation et dates de réponse/résolution. Les pièces jointes restent privées sur le disque local et leur accès est autorisé via la policy des participants du ticket.
