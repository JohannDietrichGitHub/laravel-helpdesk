# Helpdesk Design System

## 0. Research Log

- Existing UI audit: extracted Instrument Sans, Tailwind 4 semantic tokens, shadcn primitives, and light/dark surface rules from `resources/css/app.css`.
- Reference direction: operational support console; preserved the starter's restrained neutral system and added an amber signal accent for ticket urgency.
- Skipped external visual references: this is a targeted replacement of starter branding, not a full visual redesign.

## 1. Atmosphere & Identity

Helpdesk is a calm support command center: clear enough for a customer to ask for help and dense enough for an agent to act quickly. Its signature is a small amber signal mark and layered neutral surfaces that make ticket state feel legible without visual noise.

## 2. Color

Use the existing semantic tokens from `resources/css/app.css`.

| Role               | Token                                    | Usage                          |
| ------------------ | ---------------------------------------- | ------------------------------ |
| Primary surface    | `bg-background`                          | Page backgrounds               |
| Elevated surface   | `bg-card`                                | Cards and ticket panels        |
| Primary text       | `text-foreground`                        | Headings and body              |
| Secondary text     | `text-muted-foreground`                  | Descriptions and metadata      |
| Interactive accent | `bg-primary` / `text-primary-foreground` | Primary actions                |
| Signal accent      | `text-amber-600` / `bg-amber-500`        | Helpdesk mark and urgency      |
| Success            | `text-emerald-600`                       | Resolved states                |
| Warning            | `text-amber-600`                         | Waiting and attention states   |
| Error              | `text-destructive`                       | Errors and destructive actions |

Accent colors are reserved for state and interaction, not decoration.

## 3. Typography

- Primary: Instrument Sans.
- Display: Instrument Sans, semibold/bold, tight tracking.
- Body: Instrument Sans, 14–18px with readable line height.
- Metadata: 12–14px, muted foreground, tabular numbers where useful.

## 4. Spacing & Layout

- Base unit: 4px, using Tailwind spacing tokens.
- Landing max width: `max-w-6xl`.
- Application shell: sidebar plus an independently scrolling content region.
- Mobile: stack hero content and ticket preview; preserve 24px page gutters.

## 5. Components

### Helpdesk mark

- **Structure**: compact rounded square containing a ticket/check signal.
- **States**: default and dark-mode contrast.
- **Accessibility**: decorative SVG is hidden from assistive technology when paired with text.

### Landing CTA

- **Structure**: semantic link styled as primary or secondary action.
- **States**: default, hover, active, focus-visible.
- **Motion**: transform/opacity only, 150–200ms.

### Ticket preview panel

- **Structure**: elevated card with queue rows and status indicators.
- **States**: default and dark mode; rows remain readable without color alone.
- **Accessibility**: status includes visible text, not color alone.

## 6. Motion & Interaction

- Interactive transitions use 150–200ms ease-out.
- Only transform and opacity are animated.
- Focus-visible rings remain visible in both themes.
- Respect `prefers-reduced-motion` through existing Tailwind motion utilities.

## 7. Depth & Surface

Use mixed depth: semantic card surfaces with subtle borders and restrained shadows. Do not add generic black shadows or raw one-off colors when an existing semantic token is available.

## 8. Accessibility Constraints & Accepted Debt

- All navigation and actions use real links or buttons.
- Heading order remains sequential.
- Text must remain readable in light and dark themes.
- The ticket preview is illustrative until the real ticket list exists; it must not imply interactive behavior.
