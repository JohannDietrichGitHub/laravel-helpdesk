# Frontend instructions

- Use strict TypeScript; do not introduce `any` to bypass a type error.
- Keep server state in Inertia page props and mutation state in `useForm`.
- Use accessible shadcn/ui primitives and preserve keyboard interaction.
- Keep role-based visibility aligned with backend authorization; UI checks are not security boundaries.
- Use URL query parameters for search, filters, sorting, and pagination.
