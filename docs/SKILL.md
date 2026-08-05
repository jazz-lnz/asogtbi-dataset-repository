---
name: asog-navy-gold
description: Apply the ASOG TBI Navy & Gold design language across the integrated public, contributor, reviewer, and administrator repository workspaces. Public pages use the expressive navbar and grid texture; governance pages use the focused navy portal shell with the same typography and tokens.
---

# asog-navy-gold design language

An institutional, research-grade aesthetic for the ASOG TBI Dataset Repository's public and user-facing website: deep navy bands up top on every page, a warm off-white working surface below, gold used only as a thin accent, and one signature texture — a faint graph-paper grid — that gives the app its "spec sheet" feel. It should read as credible and academic, not flashy.

This spec is technology-agnostic in principle but the project itself is a CodeIgniter 4 + MySQL app — implement with plain CSS custom properties in a shared layout partial; don't introduce a CSS framework the project doesn't already use. Full token tables and app-specific component writeups live in `DESIGN.md` — treat that as the source of truth for exact values. Functional scope, roles, dataset lifecycle, and the integrated role-gated portal live in `CONTEXT.md` — treat that as the source of truth for what a screen is allowed to do. This file is the "how to apply the visuals" companion to both.

## Philosophy — read this first

1. **Gold is a highlight, never a fill.** It marks the one important word in a heading, a badge ring, a footer label, a rule. If you're tempted to fill a button or a large surface with gold, use `sky-blue` or `navy` instead.
2. **Every page opens on navy, settles into off-white.** Home gets a full hero; every other page gets a shorter Page Header Band with the same gradient. Don't flatten this into a single background color for the whole page, and don't let any page open directly on off-white.
3. **The navbar reads scroll position, not the page.** Transparent, no fill, white text at `scrollY ≈ 0`. Past that, it's a glass panel — blurred, translucent off-white, navy text. Same threshold, same transition, on every page — this is what makes the whole site feel like one system.
4. **One accent action per screen.** `sky-blue` is reserved for the single most important thing the user can do (log in, upload, download). Everything else is navy, white, or ghost/outline.
5. **Serif is for moments, sans is for work.** DM Serif Display only touches headings and the occasional emphasized word. Every functional element — nav, labels, forms, buttons, tables — is DM Sans.
6. **Match the workspace.** Public and contributor screens use the public shell; review and administration use the role-gated portal shell. Check `CONTEXT.md` before expanding governance scope.

## Color

| Role | Token | Hex |
|---|---|---|
| Darkest / hero top | deep-dark | `#020d18` |
| Primary brand / panels | navy | `#03558c` |
| Dark-surface border | dark-navy-mid | `#0f3f62` |
| Accent (labels, rules, rings) | gold | `#f8af21` |
| Accent hover | gold-dark | `#d69a2a` |
| Primary CTA | sky-blue | `#43a7db` |
| Base light surface | off-white | `#f8f6f2` |
| Card / icon fill | white | `#ffffff` |
| Light-surface border | warm-border | `#e5e2dc` |

Never invent a new hue. If a state needs distinguishing (error, warning, success) and none of the above reads clearly, default to typography and iconography changes (bold text, an icon, an outline) before reaching for a new color.

## Typography

Two families, nine roles — see `DESIGN.md` for the exact size/weight/tracking table. The short version:

- **DM Serif Display** — the Home hero heading (35.84px), Page Header Band section headings (33.6px) on every other page, and card/dataset titles (16.8px). Italicize only the emphasized phrase in a heading, and only in `gold`.
- **DM Sans** — everything else, from 16px body copy down to 9.28px uppercase micro-labels. Uppercase + wide letter-spacing (≈1–2px) is the signature register for tags, footer headers, and filter-group labels.

## Layout & spacing

- 4px base grid; container max-width ~1200–1280px, centered, horizontal padding scaling from 16px (mobile) to 40px (desktop).
- Section rhythm is generous — 56–96px between major bands, 8–20px inside a component.
- Breakpoints: mobile ≤640px (stacked, hamburger nav, filters in a bottom sheet), tablet ≥768px (2–3 col grids), desktop ≥1024px (full density, sticky filter sidebar).

## Navbar — transparent → glass on scroll

The signature interaction of this system, and it must behave identically on every page:

```css
/* top of page: scrollY <= 8px */
.navbar { background: transparent; border: none; box-shadow: none; color: white; }

/* scrolled: scrollY > 8px */
.navbar--scrolled {
  background: rgba(248, 246, 242, 0.78);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(229, 226, 220, 0.8);
  box-shadow: 0px 12px 24px -20px rgba(12, 36, 58, 0.35);
  color: var(--navy);
}
```

- Binary state, one 8px threshold, ~250–300ms ease-out transition on background/text/shadow together — no partial/intermediate state.
- The `sky-blue` primary button in the navbar never changes — only the transparent background and text color swap.
- This only works if the page has a navy backdrop at scroll-zero — which is why every page needs a Hero (Home) or Page Header Band (everywhere else). Never ship a page that opens directly on off-white.

## Texture — the grid motif

The one visual flourish in this system: a faint hairline graph-paper grid behind off-white sections.

```css
.bg-grid {
  background-image:
    linear-gradient(to right, rgba(3, 85, 140, 0.06) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(3, 85, 140, 0.06) 1px, transparent 1px);
  background-size: 40px 40px;
  mask-image: radial-gradient(ellipse at center, black 60%, transparent 100%);
}
```

Rules:
- Navy hairlines at ~6% alpha on off-white, 40px cells.
- Always mask/fade the edges — it should dissolve, not cut off abruptly.
- One grid field per page, placed on the light band right under the hero/header. Never on the hero/header band itself, never on the footer, never behind dense data (results grid with an open filter sidebar) where it would fight the content.

## Components

Every surface component follows the same recipe: solid fill (navy or white), one border weight, one of four radii, and — for white cards only — the `card-lift` shadow on hover.

- **Radii:** `sm` 4px (inputs, tags), `md` 8px (buttons, controls), `lg` 16px (cards, panels), `pill` 999px (buttons, badges, search bar). Don't mix sharp and pill corners in the same view.
- **Shadows:** exactly two tokens — `ring-white` (`0 0 0 0.75px rgba(255,255,255,0.2)`) for definition on dark/photo backgrounds, and `card-lift` (`0 26px 40px -30px rgba(12,36,58,0.45)`) for white-card elevation, softened for the glass navbar. Nothing else.
- **Page Header Band:** every non-Home page opens with this shorter hero variant (same gradient, ~240–320px tall) so the navbar has something to be transparent over. Content inside scales per page — see `DESIGN.md` for the exact per-page table.
- **Buttons:** `pill` radius. Primary = `sky-blue` or `navy` fill, white text. Secondary/ghost = transparent, 1px border, used for lower-emphasis actions. Hover shifts fill one step darker — never changes shape.
- **Cards (dataset cards, panels):** white fill, `warm-border` hairline, `lg` radius, flat at rest, `card-lift` on hover only. The "recommended datasets" row reuses this same card, never a bespoke shape. Clicking a card opens the Dataset Detail Page.
- **Marketing badges/chips:** gold-outline pill for category tags; solid gold-ringed white circles for the Home "Domains" iconography — this is a marketing-page pattern, separate from the app's data-type chips below.
- **Data-type chip:** gold-outline pill on dataset cards for one of the five canonical types — **Text, Image, Audio, Video, Tabular**. Don't invent a sixth type.
- **Status chip:** map every state from `DatasetModel::statusLabels()` consistently. Contributors receive read-only chips; workflow actions appear only in the appropriate reviewer or administrator portal.
- **Access-type badge:** quiet chip for Public / Institutional / Restricted / Private — lock glyph on Restricted and Private.
- **Search & filter:** the Page Header Band search bar is `pill`-radius white; below it, a sticky white `lg`-radius filter sidebar (desktop) or bottom sheet (mobile), with collapsible `small-label` accordion groups, filters for Data Type/Category/Date Uploaded, and a removable-chip row of active filters above the results grid. Package format is not filterable because repository uploads are always protected ZIP files; use disclosed content formats as searchable metadata. See `DESIGN.md` for the full spec — this is the most detailed component in the system, treat it accordingly.
- **Pagination:** centered number row, current page solid navy, others ghost — no page-jump input in the current release.
- **Citation/BibTeX modal:** the single exception to "no monospace" — plain-text citation in `body`, BibTeX in the system mono stack (`ui-monospace, SFMono-Regular, Menlo, Consolas, monospace`) inside a `dark-navy-mid`-bordered box on off-white, each with its own "Copy" pill, backdrop uses the same `blur(16px)` as the navbar.
- **Login card:** centered white `lg`-radius card (~400px) on the gridded off-white body, under a Page Header Band. Email + password + "Forgot password?" + primary pill submit + a "Register" link below. See `DESIGN.md` for the full layout.

## Motion

Institutional, not playful — motion should feel confirming, not decorative:

- Navbar top ↔ glass: ~250–300ms ease-out, background/text/shadow together, no snapping.
- Card hover: lift 2px + fade in shadow, ~250ms ease-out.
- Buttons: color/fill transition only, ~150ms.
- Filter accordions: ~180ms chevron rotate; chip selection is an instant color swap.
- Dropdowns/menus/modals: fade + 4px slide, ~180ms, `blur(16px)` backdrop.
- Always honor `prefers-reduced-motion` — cut lifts, slides, and the navbar transition; keep instant color swaps.

## Accessibility & quality bar

- WCAG AA contrast (4.5:1) for all body text — check gold-on-navy and gold-on-white pairings specifically, since gold is mid-brightness. Also check navy-text-on-glass at the 78% off-white opacity used by the scrolled navbar.
- Never drop `body-light` (300 weight) below 12px.
- Every overlay (dropdown, modal, mobile filter sheet) needs a keyboard escape route.
- Form inputs get a visible focus state — border shifts to `sky-blue` with a soft glow, not just an outline.

## Applying this to ASOG TBI app screens

Work in this order for any new or restyled screen:

1. **Check scope and role first.** Confirm the screen is public, contributor, ethics, technical, or administrator functionality listed in `CONTEXT.md`; deferred backup, reporting, antivirus, and access-request flows still require a scope decision.
2. **Give it a Hero (Home) or Page Header Band (everywhere else).** No page opens directly on off-white — the navbar's transparent-at-top state needs a navy backdrop to sit on.
3. **Wire up the navbar's scroll behavior** using the shared partial/script — never reimplement it per page, and never skip it "just for this screen."
4. **Pull tokens from `DESIGN.md`** — colors, type roles, spacing, radius — before styling anything by eye.
5. **Reuse an existing component pattern** (card, pill button, chip, filter sidebar, form field) rather than inventing a new shape.
6. **Add the grid texture once, if at all** — skip it entirely on dense/tabular screens (results grid with an open filter sidebar) where it would compete with content.
7. **Reserve `sky-blue`** for the one primary action on the screen (Log in, Upload Dataset, Download).
8. **Finish with the Do's and Don'ts table in `DESIGN.md`** as a pre-ship checklist.

If a screen feels visually "loud," it's almost always because gold or sky-blue got used more than once — pull one of them back to navy, white, or a ghost button.
