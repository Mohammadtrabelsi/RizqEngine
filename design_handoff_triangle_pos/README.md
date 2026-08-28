# Handoff: Triangle POS — modern redesign (landing, sign in, dashboard)

## Overview
A full visual redesign of the Triangle POS app: public landing page, sign-in screen, and the
operations dashboard. Target stack: **Laravel 11 + Livewire 3 + Tailwind CSS**.

## About the design files
`Triangle POS.dc.html` in this bundle is a **design reference created in HTML** — a prototype
showing the intended look and behavior, not production code to paste in. The task is to recreate
it inside the existing Laravel/Livewire app using its established conventions (layouts, Blade
components, Livewire components, translation files). The Blade/PHP files in `code/` are a
faithful, ready-to-adapt starting point, not a drop-in package: wire them to the real models.

The prototype uses a screen-switcher pill (bottom center) purely to demo three screens in one
file — **do not implement it**. Each screen is its own route.

## Fidelity
**High fidelity.** Colors, typography, spacing and states are final. Recreate pixel-close.

## Design tokens

Colors (authored in oklch; hex fallbacks are what Tailwind config uses):

| Token | oklch | hex |
|---|---|---|
| canvas | oklch(0.985 0.004 280) | #f7f7fb |
| canvas-2 | oklch(0.975 0.004 280) | #f2f2f8 |
| surface | #ffffff | #ffffff |
| ink (text, dark panels) | oklch(0.21 0.02 277) | #26263a |
| ink-2 (sidebar hover) | oklch(0.28 0.02 277) | #35354d |
| body text | oklch(0.47 0.02 277) | #6b6b85 |
| muted text | oklch(0.55 0.02 277) | #82829b |
| hairline border | oklch(0.92 0.008 280) | #e7e7ee |
| accent (indigo) | oklch(0.52 0.19 277) | #4f39e0 |
| accent-hover | oklch(0.45 0.19 277) | #4229c9 |
| accent-light bg | oklch(0.96 0.02 277) | #eeecfd |
| success | oklch(0.6 0.15 155) | #1fa36b |
| warn | oklch(0.45 0.12 55) | #97621b |
| danger | oklch(0.5 0.16 20) | #c0392b |

Type: **Space Grotesk** 600/700 (display + card titles), **Manrope** 400/500/600/700 (UI + body),
**JetBrains Mono** 500/600 (all numerals, micro-labels, refs, dates).
Micro-label pattern: 10.5px / weight 600 / letter-spacing 0.12em / uppercase / muted, mono.
Display H1: Space Grotesk 700, 76px landing / 60px sign-in, line-height 0.98–1.0, tracking -0.035em.

Radii: 10px controls, 12px inputs & primary buttons, 16px cards, 20–22px feature panels, 999px pills.
Spacing scale: 4 / 8 / 10 / 12 / 16 / 20 / 26 / 32 / 48 / 64 / 96 px.
Shadows: used sparingly — `0 30px 70px -30px rgba(48,40,90,.35)` on the hero mock,
`0 10px 26px -8px rgba(79,57,224,.6)` under the primary sign-in button. Cards use 1px hairlines, no shadow.

## Screens

### 1. Landing (`/`)
Sticky translucent header (blur 14px, 1px bottom hairline): logo left (28–30px ink square, radius 9px,
indigo CSS triangle inside), centered nav (Features / Access control / Reporting, 14px/500, muted → accent
on hover), right: EN language button + indigo "Sign in".
Hero: 2-col grid 1.05fr/0.95fr, gap 64px, padding 96px 48px 88px, max-width 1440px.
Left: eyebrow pill ("POINT OF SALE & INVENTORY", mono 11px, accent-light bg), H1 "Run your whole store
from one screen.", 17.5px lead paragraph (max-width 520px), two buttons (ink "Get started", outlined
"See features"), then a 3-stat row above a hairline: 1 002 SKUs tracked / <150ms till response / 14 role presets.
Right: till mock card (radius 20px, window chrome bar, cart total 34px mono, three line items — third one
highlighted in accent-light — and a 3-up Cash / Card / Charge row); floating "Stock synced · real-time" card
offset -28px left / -26px bottom.
Features: 3×2 grid rendered as a 1px-gap grid on the hairline color (cells canvas, hover white), each cell
mono "/ 0N" index, 20px Space Grotesk title, 14.5px body.
CTA band: ink panel radius 22px, 34px heading + lighter indigo button.
Footer row: © 2026 Triangle POS / "Laravel · Livewire" in mono.

### 2. Sign in (`/login`)
2-col grid 1.1fr/1fr, full viewport height.
Left: ink panel, padding 56px 64px, logo top, 60px H1 "Run your whole store from one place.", lead
paragraph in oklch(0.78 0.012 280), three outlined mono chips (MULTI-STORE / OFFLINE-SAFE TILL / AUDIT TRAIL),
copyright pinned bottom.
Right: centered form column max-width 420px on canvas. Language button top-right, "Sign in" 40px H2,
subhead, uppercase mono field labels, inputs 14px/16px padding, radius 12px, 1px border,
focus = accent border + `0 0 0 4px rgba(79,57,224,.14)` ring. "Forgot password?" sits inline right of the
Password label. Checkbox "Keep me signed in on this register". Full-width accent submit. OR divider,
secondary "Use a PIN code" button, footer "Need access? Contact your store owner".
Validation: email required/valid, password required min 8; errors render under the field, 12.5px, danger color,
input border switches to danger.

### 3. Dashboard (`/dashboard`)
Grid 260px sidebar + main.
Sidebar: ink, padding 22px 16px, logo, four labelled groups (OVERVIEW / TRANSACTIONS / PEOPLE / INSIGHT) with
mono group headings; items 10px/12px padding, radius 10px, 14px text, hover `ink-2`; active item = accent fill
with a white 6px dot; Orders item has a count pill; bottom "SHIFT OPEN" card (radius 14px, ink-2 bg,
mono time range, register + role line).
Topbar (sticky): H1 "Dashboard" 22px Space Grotesk + date/register subline; right: FR selector, accent
"Open point of sale", notification button with red "9+" badge, user pill (30px ink avatar with initials,
name, green "Online" dot).
Body padding 28px 32px 48px, 20px gaps:
1. Filter bar card: FROM / TO date inputs (mono), ink "Apply", outlined "Reset", right-aligned quick-range
   pills (Today active in accent-light / 7 days / Month).
2. 4 KPI cards: Sales today 2 480.90DT (+8.4% green chip), Transactions 148, **Low stock 1 002** (amber card:
   bg oklch(0.99 0.02 75), border oklch(0.88 0.05 60), "ACTION" chip), Expenses today 318.00DT.
   Numerals 32px mono, tracking -0.03em; currency suffix 15px muted.
3. Revenue chart card (1.6fr) + right column (1fr) of three cards: Gross profit (with 31% progress bar),
   Receivables 9 765.23DT, Supplier debt 4 120.00DT. Chart = 14 stacked bar pairs, height 190px,
   accent revenue on top (radius 5px 5px 0 0) + oklch(0.9 0.03 277) COGS below, 3px gap, legend top-right.
4. Recent transactions table (columns REF / CUSTOMER / STATUS / TOTAL, mono header row on canvas,
   status pills Paid green / Partial amber / Return red, totals right-aligned mono) +
   Restock queue list (item, "Reorder at N" subline, count in red under threshold / amber near it,
   footer "Create purchase order" button).

## Interactions & behavior
- Header nav anchors scroll to `#features` / `#access` / `#reporting`.
- Hover: nav links → accent; outlined buttons → border darkens or turns accent; feature cells → white bg;
  sidebar items → ink-2; primary buttons → accent-hover / ink lighter. Transitions 150ms ease.
- Sign in submit → Livewire `login()`, wire:loading disables the button and swaps the label to "Signing in…".
- Date filter: `wire:model.live` on both dates is optional; the canonical flow is Apply (`wire:click="apply"`)
  / Reset (`wire:click="reset"`), and the quick-range pills set the range then re-query.
- KPI + chart + tables all recompute from the selected range; wrap each block in its own Livewire component so
  polling / `wire:loading.class` skeletons apply per card.
- Responsive: below 1100px the hero, sign-in and dashboard 2-col grids collapse to one column;
  below 900px the sidebar becomes an off-canvas drawer toggled from the topbar.

## State
Sign in: `email`, `password`, `remember`. Dashboard: `from`, `to`, `preset` (today|7d|month),
plus computed `salesToday, txCount, lowStockCount, expensesToday, revenueSeries, cogsSeries, grossProfit,
marginPct, receivables, supplierDebt, recentTransactions, restockQueue`. All money formatted with a single
helper (`number_format($v, 2, '.', ' ') . 'DT'`).

## Assets
None external. The logo is a CSS triangle inside a rounded ink square — replace with the real SVG mark if
you have one. Fonts load from Google Fonts (Space Grotesk, Manrope, JetBrains Mono).

## Files in this bundle
- `Triangle POS.dc.html` — the HTML design reference (three screens, switcher pill for demo only)
- `code/tailwind.config.js` — tokens as Tailwind theme extensions
- `code/resources/views/layouts/app.blade.php` — fonts + shell
- `code/resources/views/livewire/landing-page.blade.php`
- `code/resources/views/livewire/auth/login.blade.php`
- `code/resources/views/livewire/dashboard.blade.php`
- `code/resources/views/components/*.blade.php` — kpi-card, stat-pill, sidebar
- `code/app/Livewire/LandingPage.php`, `Auth/Login.php`, `Dashboard.php`
- `code/routes/web.php`
- `code/lang/en.json`, `code/lang/fr.json` — the copy, ready for `__()`

## Notes for the implementer
- Keep every user-facing string in `__()`; the design ships EN and FR (the FR dashboard matches the client's
  current locale usage). The language selector switches `app()->setLocale()` via a session-backed route.
- Replace the placeholder numbers with real aggregates; keep the mono numeral treatment and the
  `1 002` thin-space grouping.
- Do not introduce extra colors. One accent only.
