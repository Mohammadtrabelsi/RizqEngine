# Triangle POS — Front-end handoff

A production-oriented point-of-sale & inventory platform. This package hands off
**three finished screens** — Landing, Sign in, Dashboard — as a design reference
plus Livewire 3 / Tailwind starting code. Build the rest of the app on top of
these tokens and components so every future screen looks like it belongs.

- **Stack:** Laravel + Livewire 3, Tailwind CSS, Vite.
- **Fonts:** Space Grotesk (display), Manrope (body/UI), JetBrains Mono (numbers, labels, refs).
- **Currency:** Tunisian Dinar, rendered as `1 234.56DT` (space thousands, `DT` suffix in muted, smaller type).
- **Locales:** English + French, URL-prefixed (`/en`, `/fr`).

> The canonical visual is `design/Triangle POS.dc.html`. Open it in a browser and
> use the bottom pill to switch between the three screens. When code and prose
> disagree, **the HTML wins.**

---

## 1. Design tokens

All colors are authored in **OKLCH** and transcribed verbatim into
`code/tailwind.config.js`. Use the Tailwind names below — never re-pick values.

### Neutrals
| Token | OKLCH | Use |
|---|---|---|
| `ink` | `0.21 0.02 277` | Headings, sidebar, dark CTAs |
| `ink-2` | `0.28 0.02 277` | Hover on dark surfaces |
| `ink-3` | `0.30 0.02 277` | Dark chips, logo tile, count pills |
| `body` | `0.42 0.02 277` | Body copy |
| `muted` | `0.52 0.02 277` | Secondary copy |
| `faint` | `0.55 0.02 277` | Uppercase labels |
| `line` | `0.91 0.008 280` | Hairline borders |
| `line-2` | `0.93 0.008 280` | Lighter hairlines / table rows |
| `paper` | `0.985 0.004 280` | App / guest background |
| `paper-2` | `0.975 0.004 280` | Dashboard background |

### Brand (violet) & status
| Token | OKLCH | Use |
|---|---|---|
| `brand` | `0.52 0.19 277` | Primary buttons, active nav, links, accents |
| `brand-600` | `0.45 0.19 277` | Primary hover |
| `brand-400` | `0.62 0.19 277` | Triangle mark, dark-panel CTA |
| `brand-300` | `0.68 0.19 277` | Triangle on ink, CTA hover on ink |
| `brand-tint` | `0.96 0.02 277` | Soft pill backgrounds |
| `success` / `success-bg` / `success-fg` | `0.60 0.15 155` / `0.95 0.05 155` / `0.42 0.13 155` | Online, "Paid", positive deltas |
| `warn` / `warn-bg` | `0.45 0.12 55` / `0.97 0.04 75` | Low-stock, "Partial", amber tiles |
| `danger` / `danger-bg` / `danger-fg` | `0.50 0.16 20` / `0.96 0.03 20` / `0.48 0.16 20` | Returns, negative totals, "9+" badge |

### Type scale (as used)
- Display H1: Space Grotesk 700, 60–76px, `tracking-[-0.035em]`, `leading` ~1.
- Section H2: Space Grotesk 700, 34–40px, `tracking-[-0.03em]`.
- Card/section H3: Space Grotesk, 16–20px.
- Body: Manrope 400–500, 14–17.5px, `leading-[1.6]`.
- Labels: JetBrains Mono 600, 10.5–12px, uppercase, `tracking-[0.08–0.14em]`, in `faint`/`muted`.
- Numbers (money, KPIs, refs): JetBrains Mono 600, `tracking-[-0.03em]`.

### Shape & elevation
- Radii: inputs/buttons `12px` (`rounded-xl`), cards `16px` (`xl2`), hero mock `20px` (`xl3`), pills `999px`.
- `shadow-card` for floating panels, `shadow-brand` for the primary sign-in button.
- Focus ring on inputs: `border-brand` + `0 0 0 4px oklch(0.52 0.19 277 / 0.14)`.

---

## 2. Screens

### 2.1 Landing (`/`)
Sticky blurred header (logo · Features/Access/Reporting nav · language switch · Sign in).
Two-column hero (`1.05fr / 0.95fr`): eyebrow pill, 76px headline, lede, two CTAs, a
3-stat strip; on the right a **till/register mock card** with a floating "Stock synced"
chip. A 3×2 **feature grid** (1px gridlines over `line`, cells reveal white on hover),
each cell numbered `/ 01…/ 06`. Dark **CTA band** + minimal footer.
Feature copy is data-driven — see `Landing::modules()`.

### 2.2 Sign in (`/sign-in`)
Two-column full-height split (`1.1fr / 1fr`). Left: ink panel with logo, 60px headline,
lede, three mono capability tags, copyright. Right: paper column, language switch, form —
Email, Password (+ "Forgot password?"), "Keep me signed in on this register" checkbox,
primary Sign in button, `OR` divider, "Use a PIN code" secondary, "Contact your store
owner" footer. Validation is inline under each field.

### 2.3 Dashboard (`/dashboard`, auth)
Fixed **260px ink sidebar** (logo, four labelled nav groups, a "Shift open" card pinned to
the bottom) + main column. Sticky top bar: title + date line, language switch, "Open point
of sale", a notifications button with a `9+` badge, and a user chip with an online dot.
Body: a **date-range toolbar** (FROM/TO, Apply/Reset, Today/7-days/Month chips), four **KPI
tiles** (one amber "Low stock" variant), a **Revenue vs COGS** stacked-bar chart (14 days)
beside Gross-profit / Receivables / Supplier-debt stats, and a row of **Recent transactions**
(status badges) + **Restock queue** (color-coded quantities, "Create purchase order").

---

## 3. Interactions & state

| Screen | Trigger | Behaviour |
|---|---|---|
| Landing | Sign in / Get started / CTA | Navigate to `sign-in` (locale-preserving). |
| Landing | Feature cell hover | Background `paper → white`. |
| All | Language switch | Swap `{locale}` on the current route (`en ↔ fr`). |
| Sign in | Submit | `SignIn::authenticate()` validates, `Auth::attempt(..., $remember)`, regenerates session, redirects to `dashboard`. Failure → inline error under Email. |
| Dashboard | Today / 7 days / Month chip | `Dashboard::range()` sets `$from`/`$to`. |
| Dashboard | Apply / Reset | `$refresh` / `resetRange()`. |
| Dashboard | Notification, user chip, nav (non-dashboard) | Placeholders (`#…`) — wire as screens are built. |

**Livewire classes**
- `Landing` — static marketing; `modules()` feeds the feature grid from lang files.
- `SignIn` — `email`, `password`, `remember`; `#[Validate]` attributes; `authenticate()`.
- `Dashboard` — `from`, `to`; `range()`, `resetRange()`, and `stats()` / `chart()` data
  providers whose **shapes are the contract** for the Blade view. Replace the hard-coded
  figures with aggregate queries scoped to the date range; keep the array shapes.

---

## 4. What's in `code/`

```
code/
├── tailwind.config.js                     # design tokens (source of truth)
├── routes/web.php                          # locale-prefixed public + auth routes
├── app/Livewire/                           # Landing, SignIn, Dashboard
├── resources/views/
│   ├── layouts/{guest,app}.blade.php       # guest shell + authed shell (with sidebar)
│   ├── livewire/{landing,sign-in,dashboard}.blade.php
│   └── components/                         # logo, lang-switcher, nav-link, status-badge
└── lang/{en,fr}/pos.php                     # every visible string
```

**Components**
- `<x-logo :size tone="light|dark" :showText />` — triangle mark + wordmark.
- `<x-lang-switcher />` — EN/FR toggle that preserves the route.
- `<x-nav-link :href :active :badge :dot />` — sidebar item (active = brand fill).
- `<x-status-badge :status="paid|partial|return|pending" />` — transaction pill.

---

## 5. Integration notes (not yet wired)

These are intentionally left as stubs so the real app decides them:

1. **`set-locale` middleware** — read `{locale}`, call `app()->setLocale()`, persist choice.
2. **Auth** — the package assumes Laravel's `auth` middleware and a `users` table; adjust
   `authenticate()` to your guard, add PIN login behind "Use a PIN code".
3. **Data** — swap `Dashboard::stats()/chart()` and the inline table/restock rows for
   models & queries. All money should flow through one `money()` formatter (`1 234.56DT`).
4. **Vite** — `resources/css/app.css` must `@tailwind base/components/utilities`; the
   layouts already `@vite([...])` and `@livewireScripts`.
5. **RBAC** — the "Roles & permissions" module implies per-screen/per-action gates; gate
   nav items and route groups accordingly.
