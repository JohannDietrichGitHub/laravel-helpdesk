# Backend instructions

- Controllers coordinate HTTP concerns only.
- Form Requests own input validation and authorization hooks.
- Policies own resource access rules; do not duplicate role checks in React.
- Models declare casts, relationships, and query scopes close to the data they describe.
- Use transactions for multi-record ticket workflows.
- Prefer typed DTOs/value objects or Enums over unstructured arrays for domain state.
