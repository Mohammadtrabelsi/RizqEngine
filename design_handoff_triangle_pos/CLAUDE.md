# CLAUDE.md — implementation brief for this handoff

Read `README.md` first, then work in this order:

1. **Tokens.** Merge `code/tailwind.config.js` into the app's Tailwind config. Add the Google Fonts
   `<link>` from `code/resources/views/layouts/app.blade.php` to the real layout. Do not add colors
   beyond the token table — one indigo accent only.
2. **Shared components.** Create `x-logo-mark`, `x-kpi-card`, `x-status-pill`, `x-app-sidebar`
   from `code/resources/views/components/`. Adapt `@can` gates to the app's real permission names.
3. **Sign in.** Replace the existing login view with `code/resources/views/livewire/auth/login.blade.php`
   and reconcile `app/Livewire/Auth/Login.php` with the app's existing auth flow (keep whatever
   rate-limiting / two-factor logic already exists — take only the fields, validation and view).
4. **Dashboard.** Replace the dashboard view; wire the `#[Computed]` methods in
   `app/Livewire/Dashboard.php` to the real models/scopes (`Transaction::sales()`,
   `Product::lowStock()`, `Transaction::dailyTotals()` are assumed — implement or rename them).
   The chart is pure CSS: the component must return **pixel heights**, not raw money.
5. **Landing.** Public page, no auth. Static illustration data stays in the component.
6. **i18n.** Copy `code/lang/en.json` and `code/lang/fr.json` into `lang/`. Every string in the
   views must resolve through `__()`; add missing keys rather than hardcoding text.

Constraints:
- Livewire 3 syntax (`wire:model`, `wire:submit`, `#[Computed]`, `#[Url]`). No Vue/React.
- No new JS dependencies. No chart library — the bar chart is divs.
- Do not implement the screen-switcher pill from the HTML prototype; each screen is a route.
- Keep numerals in JetBrains Mono with `tracking-num`, thousands separated by a thin space.
- Mobile: hero / sign-in / dashboard grids collapse to one column below 1100px; sidebar becomes an
  off-canvas drawer below 900px (Alpine `x-data` toggle is fine).

Definition of done: the three routes render, match the HTML reference closely at 1440px wide,
pass `php artisan test`, and contain no hardcoded user-facing strings.
