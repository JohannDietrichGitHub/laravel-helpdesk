# Schema

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

Tickets are soft-deletable and indexed by status, priority, assignment, and reply/resolution timestamps. Attachments remain private on the local disk and are authorized through the ticket participant policy.
