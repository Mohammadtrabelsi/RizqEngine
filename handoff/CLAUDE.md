# Brief for Claude Code — Triangle POS

You are implementing **Triangle POS**, a Laravel + Livewire 3 point-of-sale &
inventory app. This package gives you a finished visual design and matching
starter code. Your job is to wire it into a running app and extend it, without
drifting from the established look.

## Start here
1. Open `design/Triangle POS.dc.html` in a browser. Use the bottom pill to view
   **Landing**, **Sign in**, **Dashboard**. This is the source of truth for layout,
   spacing and color — when in doubt, match the HTML pixel-for-pixel.
2. Read `README.md` for the full spec: design tokens, per-screen breakdown,
   interactions, and the Livewire state contract.
3. Drop `code/` into a fresh Laravel app (Livewire 3, Tailwind, Vite already
   assumed). File locations mirror a standard Laravel layout.

## Ground rules
- **Tokens only.** Every color lives in `code/tailwind.config.js` (OKLCH). Use the
  Tailwind names (`brand`, `ink`, `line`, `success`, …). Never introduce a raw hex
  or an off-palette value.
- **Fonts:** Space Grotesk (display), Manrope (UI), JetBrains Mono (all numbers,
  labels, and reference codes). Money renders as `1 234.56DT`.
- **Strings are translated.** No hard-coded copy in Blade — add keys to
  `lang/en/pos.php` **and** `lang/fr/pos.php`. The app is `/en` and `/fr`.
- **Reuse the components:** `<x-logo>`, `<x-lang-switcher>`, `<x-nav-link>`,
  `<x-status-badge>`. Build new shared UI as components in the same spirit.
- **Keep the Livewire data shapes.** `Dashboard::stats()` / `chart()` return arrays
  the Blade view depends on — replace the mock data with real queries but preserve
  the shape.

## Build order (suggested)
1. Make the three screens run: `set-locale` middleware, `resources/css/app.css`
   with the Tailwind directives, real auth behind `SignIn::authenticate()`.
2. Replace dashboard mock data with models + date-range-scoped aggregates.
3. Build out the sidebar destinations (Products, Transactions, Quotes, Orders,
   Expenses, Customers, Suppliers, Staff & roles, Reports, Settings) — each on the
   `layouts.app` shell, reusing tokens and components.
4. Add RBAC gates (per-screen / per-action) as implied by "Roles & permissions".

## Definition of done for any new screen
Uses `layouts.app`; all copy in both lang files; only palette tokens; numbers in
JetBrains Mono; matches the density and radii of the dashboard; no console/Livewire
errors. If it looks foreign next to the dashboard, it's not done.
