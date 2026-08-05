---
version: "1.1"
name: "ASOG TBI Navy & Gold"
project: "ASOG TBI Dataset Repository (public + user-facing website)"
stack: "CodeIgniter 4 + MySQL"
description: "A dark-navy-dominant hero with a warm off-white body, anchored by a gold accent system, a sky-blue call-to-action, and a signature graph-paper grid texture on light sections. DM Serif Display carries editorial headings, DM Sans carries everything else — producing a credible, institutional-but-modern tone suited to an academic dataset repository. Every page opens on a navy band (full hero on Home, a shorter Page Header Band elsewhere) so the navbar can stay transparent at the very top and shift to a glassmorphism state the moment the page scrolls — identical behavior everywhere."
colors:
  gold-dark: "#d69a2a"
  off-white: "#f8f6f2"
  sky-blue: "#43a7db"
  white: "#ffffff"
  deep-dark: "#020d18"
  gold: "#f8af21"
  navy: "#03558c"
  dark-navy-mid: "#0f3f62"
  warm-border: "#e5e2dc"
typography:
  hero-heading:
    fontFamily: "DM Serif Display"
    fontSize: "35.84px"
    fontWeight: "400"
    lineHeight: "42.29px"
  section-heading:
    fontFamily: "DM Serif Display"
    fontSize: "33.6px"
    fontWeight: "400"
    lineHeight: "37.63px"
  subheading-serif:
    fontFamily: "DM Serif Display"
    fontSize: "16.8px"
    fontWeight: "400"
    lineHeight: "23.1px"
  body:
    fontFamily: "DM Sans"
    fontSize: "16px"
    fontWeight: "400"
    lineHeight: "24px"
  category-label:
    fontFamily: "DM Sans"
    fontSize: "9.92px"
    fontWeight: "700"
    lineHeight: "14.88px"
    letterSpacing: "1.984px"
  nav-link:
    fontFamily: "DM Sans"
    fontSize: "10.88px"
    fontWeight: "500"
    lineHeight: "16.32px"
    letterSpacing: "0.9792px"
  small-label:
    fontFamily: "DM Sans"
    fontSize: "9.28px"
    fontWeight: "600"
    lineHeight: "13.92px"
    letterSpacing: "1.856px"
  body-light:
    fontFamily: "DM Sans"
    fontSize: "12.8px"
    fontWeight: "300"
    lineHeight: "21.76px"
  dropdown-item:
    fontFamily: "DM Sans"
    fontSize: "9.92px"
    fontWeight: "400"
    lineHeight: "14.88px"
    letterSpacing: "0.992px"
rounded:
  sm: "4px"
  md: "8px"
  lg: "16px"
  pill: "999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  base: "16px"
  lg: "20px"
  xl: "28px"
  2xl: "40px"
  3xl: "48px"
  4xl: "56px"
  5xl: "96px"
---

## Overview

This is the visual design system for the **ASOG TBI Dataset Repository public + user-facing website** — a CodeIgniter 4 + MySQL app that lets students, faculty, and incubatees at CSPC browse, cite, download, and contribute datasets. The full functional scope lives in `Database-Repo-SRS.md`; the condensed, build-ready version — current release scope, roles, and **what is explicitly not part of this repo** — lives in `CONTEXT.md`. **This document governs the UI layer only** and should be read alongside `CONTEXT.md`, not instead of it.

> **Scope note:** the integrated application includes public, contributor, reviewer, and administrator workspaces. Governance screens use the same tokens with a dedicated portal shell; backup and reporting screens remain deferred.

**Signature traits:**
- **Dual typeface system** — DM Serif Display for editorial/heading moments, DM Sans for everything functional (nav, labels, body, forms).
- **Navy-to-mist gradient banding** — every page opens on a navy band and settles into off-white; never a flat single-color page.
- **Adaptive navbar** — transparent with zero background at the very top of the page, shifting to a soft glassmorphism panel the instant the page scrolls, on every page, not just Home.
- **Graph-paper grid texture** — a faint hairline grid grounds the off-white content sections, giving the "data / research" feel without adding color.
- **Gold as the only decorative accent** — labels, rules, and badge rings, never used for large fills.
- **Soft, generous rounding** — up to a full pill (999px) on buttons and badges, 16px on panels and cards.
- **Restrained elevation** — depth comes from exactly two shadow tokens, never ad-hoc box-shadows.

## Colors

| Token | Hex | Role | Usage |
|---|---|---|---|
| `deep-dark` | `#020d18` | background | Darkest stop of the hero/header-band gradient, footer base |
| `navy` | `#03558c` | background / text | Primary brand color — header/footer fills, dark panels, headings on light surfaces, glass-navbar text |
| `dark-navy-mid` | `#0f3f62` | border | Hairline borders/dividers on dark surfaces |
| `gold` | `#f8af21` | accent | Italic serif heading accent, decorative rules, badge/domain-circle borders, footer column headers |
| `gold-dark` | `#d69a2a` | accent (hover) | Hover/active state of any gold element |
| `sky-blue` | `#43a7db` | background | Primary CTA fill (e.g. "Log In", "Upload Dataset"), interactive link highlight |
| `off-white` | `#f8f6f2` | background | Base surface for all content sections, hero body copy, glass-navbar tint |
| `white` | `#ffffff` | background | Card fills, search-bar fill, icon fills, domain-badge fills |
| `warm-border` | `#e5e2dc` | border | Hairline borders/dividers on off-white surfaces, glass-navbar bottom edge |

**Rule of thumb:** navy and deep-dark carry the institutional weight (hero/header bands, footer, panels); off-white and white carry the working content (cards, forms, tables); gold never fills a surface larger than a rule, ring, or label; sky-blue is reserved for the single most important action on a screen.

## Typography

| Role | Font | Size / Line-height | Weight | Tracking | Used for |
|---|---|---|---|---|---|
| Hero heading | DM Serif Display | 35.84 / 42.29px | 400 | normal | Home hero H1 only ("Institutional / *Dataset Repository*") |
| Section heading | DM Serif Display | 33.6 / 37.63px | 400 | normal | "Domains", Page Header Band titles ("Log In", "Upload a Dataset", "My Datasets") |
| Subheading (serif) | DM Serif Display | 16.8 / 23.1px | 400 | normal | Card titles, dataset titles on detail pages |
| Body | DM Sans | 16 / 24px | 400 | normal | Paragraph copy, form labels, table cells |
| Category label | DM Sans | 9.92 / 14.88px | 700 | 1.984px | Uppercase tags — data-type chips, status chips |
| Nav link | DM Sans | 10.88 / 16.32px | 500 | 0.98px | Header nav items, footer link items |
| Small label | DM Sans | 9.28 / 13.92px | 600 | 1.856px | Footer column headers, filter-group headers, micro-meta |
| Body light | DM Sans | 12.8 / 21.76px | 300 | normal | Captions, dataset descriptions, helper text |
| Dropdown item | DM Sans | 9.92 / 14.88px | 400 | 0.99px | Menu/dropdown entries, filter option rows |

Only `hero-heading` and `section-heading` ever use the italic cut of DM Serif Display, and only for the single word/phrase meant to carry the gold accent. Don't italicize body or label text.

## Layout & Spacing

- **Grid base:** 4px, scaling through 2, 4, 6, 8, 10, 12, 16, 20, 24, 28, 40, 48, 56, 80, 96.
- **Content container:** centered, max-width ~1200–1280px, horizontal padding `base` (16px) on mobile up to `2xl` (40px) on desktop.
- **Section rhythm:** vertical spacing between major sections uses `4xl`–`5xl` (56–96px); spacing inside a card or form uses `sm`–`lg` (8–20px).

| Token | Value |
|---|---|
| xs | 4px |
| sm | 8px |
| md | 12px |
| base | 16px |
| lg | 20px |
| xl | 28px |
| 2xl | 40px |
| 3xl | 48px |
| 4xl | 56px |
| 5xl | 96px |

### Breakpoints
| Name | Width | Behavior |
|---|---|---|
| Mobile | ≤ 640px | Single column, hamburger/overlay nav, filters collapse into a bottom sheet |
| Tablet | ≥ 768px | 2–3 column grids, nav links appear inline |
| Desktop | ≥ 1024px | Full 4-column badge/domain rows, sticky filter sidebar, max layout density |

## Texture — the grid motif

The off-white sections carry a faint graph-paper grid — this is the app's one piece of "texture" and it's what gives the repository its research-lab, spec-sheet feel without touching the color palette.

- **Construction:** a repeating grid of 1px hairlines on a 40px cell, drawn as two linear gradients (horizontal + vertical) rather than an image, so it scales cleanly:
  ```css
  background-image:
    linear-gradient(to right, rgba(3, 85, 140, 0.06) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(3, 85, 140, 0.06) 1px, transparent 1px);
  background-size: 40px 40px;
  ```
- **Color:** `navy` at very low alpha (~6%) on `off-white` sections. On dark/navy sections, invert to `white` at ~4–5% alpha if a grid is ever needed there — keep it barely-there.
- **Fade:** always mask the grid so it dissolves rather than cuts off — a soft radial or top/bottom linear fade-to-transparent at the section edges, so it reads as a field, not a hard-edged tile.
- **Placement:** the light band directly under the hero/header (behind the "What you can find here" panel on Home, behind the results grid on Browse, behind the form card on Login/Upload) is the primary home for this texture. Never on the hero/header band or footer — those stay clean navy.
- **Restraint:** one grid field per page. It's ambient texture, not a repeating wallpaper pattern behind every section.

## Elevation & Shadows

| Token | Value | Use |
|---|---|---|
| `ring-white` | `0px 0px 0px 0.75px rgba(255,255,255,0.2)` | Focus/definition ring on elements sitting over dark or photographic backgrounds |
| `card-lift` | `0px 26px 40px -30px rgba(12,36,58,0.45)` | Resting/hover elevation on white cards (dataset cards, dropdowns, modals, the glass navbar) |

Keep everything else flat. Don't introduce a third shadow recipe.

## Shapes

| Token | Value | Role |
|---|---|---|
| `sm` | 4px | Inputs, tags, subtle corners |
| `md` | 8px | Buttons, small controls |
| `lg` | 16px | Cards, panels, the dark "info panel" |
| `pill` | 999px | Buttons, search bar, domain badges, chips |

Don't mix sharp and rounded corners in the same view — if a screen uses `lg` cards, its buttons should be `md` or `pill`, not square.

## Components

### Navbar — transparent → glass on scroll

The navbar has exactly two states, and the transition between them is the same on every page (Home, Login, Browse, Detail, Upload, My Datasets):

**1. Top state** (`scrollY` at or near `0`, roughly `≤ 8px`): fully transparent, no fill, no border, no shadow. Logo + `nav-link` items render in **white**, sitting directly on the navy hero (Home) or Page Header Band (every other page).

**2. Scrolled state** (`scrollY > 8px`): the navbar becomes a glass panel —
```css
.navbar--scrolled {
  background: rgba(248, 246, 242, 0.78); /* off-white @ 78% */
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(229, 226, 220, 0.8); /* warm-border */
  box-shadow: 0px 12px 24px -20px rgba(12, 36, 58, 0.35); /* softened card-lift */
}
```
Logo and `nav-link` items switch from white to **navy** in the same transition so they stay legible once the blurred, lighter content shows through. The "Log In" / "Upload Dataset" pill button keeps its `sky-blue` fill in both states — it never needs to change.

**Transition:** background, text color, and shadow all animate together over ~250–300ms ease-out. Don't snap between states, and don't add a middle/partial state — it's binary (top vs. scrolled), driven off a single scroll listener with the 8px threshold, throttled to animation frames.

This is the same interaction pattern already validated for this system's dropdown/overlay panels (`backdrop-filter: blur(16px)`) — reuse it exactly, don't invent a different blur radius for the navbar.

### Hero (Home only)

Full-bleed gradient from `deep-dark` (top) to `navy`/`sky-blue`-tinted (bottom), centered `hero-heading` in white with the emphasized phrase in italic gold, `body` subtext in a muted white, and a white `pill`-radius search input (placeholder in `body`, a small square/circle navy button with a search icon docked to the right edge). This is the only full hero in the system — everywhere else uses the shorter Page Header Band below.

### Page Header Band (every other page)

A shorter version of the hero — same `deep-dark` → `navy` gradient, roughly 240–320px tall instead of a full hero — used at the top of **every non-Home page** so the navbar always has a navy backdrop to be transparent over at scroll position zero. Contents scale to the page:

| Page | Page Header Band contents |
|---|---|
| Login / Register | `section-heading` ("Log In" / "Create an Account") + short `body-light` subtext, centered |
| Browse / Search Results | `section-heading` ("Browse Datasets") + a compact version of the search bar (same white pill, shorter height) |
| Dataset Detail | Dataset title in `subheading-serif` white + status chip + access-type badge (see below) |
| Upload / Update Dataset | `section-heading` ("Upload a Dataset") + short instructional `body-light` subtext |
| My Datasets (user dashboard) | `section-heading` ("My Datasets") + an inline `sky-blue` "Upload Dataset" pill button |

Never drop this band — it's what keeps "the styling on every other page identical to Home."

### Info panel (dark card)

`navy` fill, `lg` radius, generous internal padding (`3xl`), sits on the gridded off-white section. Holds a centered `section-heading` in white and a 3–4 column row of circular icon badges.

### Icon badge (placeholder)

A plain filled circle (~96–112px), light neutral tint until real iconography/illustration is ready, label below in `body` (bold) + `body-light` caption. The five canonical data types (per SRS FR-030) are **Text, Image, Audio, Video, Tabular** — swap the flat tint for a matching line icon per type as they're produced; don't invent a sixth "Other" bucket unless the SRS is updated to add one.

### Domain badge

White-filled circle (~100–120px) with a `gold` 2px ring border, centered inside a gradient transition band (`navy` → light mist). Label below in bold `body`, wraps to two lines if needed. This is a Home marketing-page pattern only, separate from the data-type chips used inside the app.

### Pill button

`pill` radius, two variants: **primary** (`sky-blue` or `navy` fill, white text, used for "Log In", "See more", "Upload Dataset") and **secondary/ghost** (transparent, 1px `warm-border` or `dark-navy-mid`, used for lower-emphasis actions like "Cancel" or "Clear filters"). Hover darkens the fill toward `gold-dark`/`navy` by one step; never changes shape.

### Footer

Solid `navy`/`deep-dark` fill, 4-column layout: brand block (logo, `body-light` description, circular outline social icons) + three link columns, each headed by a `small-label` in `gold` with a short gold rule beneath. Contact column pairs a small icon with `body-light` text. A hairline `dark-navy-mid` divider separates the columns from a copyright bar in `body-light` at reduced opacity. Identical on every page — never swap it out.

## App-specific components (current release scope)

**Dataset card** (browse/search results grid, and the "recommended datasets" row on a detail page) — white fill, `lg` radius, `warm-border` hairline, `card-lift` shadow on hover only (flat at rest). Contents: a `category-label` data-type chip (gold outline, off-white fill — Text/Image/Audio/Video/Tabular) top-left, `subheading-serif` dataset title, `body-light` two-line description, and a `small-label` meta row (disclosed formats inside the ZIP, package, size, date uploaded). Clicking anywhere on the card opens the **Dataset Detail Page**. The recommended-datasets row reuses this exact card at a smaller fixed width, in a horizontal scroll or 5-up grid — never a different card shape.

**Status chip** — a small `category-label`-styled pill mapped 1:1 to the `datasets.status` field. Contributors see status read-only; authorized reviewer and administrator actions change it through the moderated workflow. Keep labels mapped to the state machine in `CONTEXT.md`.

| Status | Style |
|---|---|
| Pending Review | gold outline, off-white fill |
| Revision Requested | `gold-dark` outline, off-white fill |
| Published | `sky-blue` fill, white text |
| Archived | `warm-border` outline, muted `body-light` gray text |
| Rejected | `deep-dark` outline and text on a plain outline fill — no red anywhere in the system |

Status chips only ever appear where a user is looking at *their own* datasets (detail page, My Datasets list) — public browse/search results only ever show Published datasets, so a status chip there would be redundant.

**Access-type badge** — a smaller, quieter chip (`dropdown-item` size) next to the status chip, one of **Public / Institutional / Restricted / Private**. Public gets no icon; Institutional gets a small building glyph; Restricted and Private both get a small lock glyph in `navy` to signal "gated" at a glance, even before the access-request flow exists.

**Dataset Detail Page** — Page Header Band carries the dataset title, status chip (owner view only), and access-type badge; body drops to `off-white`. Layout: main column (description, research info, file list) + a sticky sidebar card (contributor, category/tags, version, a `sky-blue` "Download" button, and a "Cite" ghost button beside it). A **"Recommended datasets"** row of dataset cards closes out the page — this is the primary "view dataset details" flow for every visitor.

**Citation / BibTeX modal** — triggered from the "Cite" button on the dataset detail page. Modal panel: white fill, `lg` radius, `card-lift` shadow, ink-at-low-alpha backdrop with the same `blur(16px)` used on the glass navbar. Two stacked blocks — **plain-text citation** in `body`, and **BibTeX** in the system monospace stack (`ui-monospace, SFMono-Regular, Menlo, Consolas, monospace`) inside a `dark-navy-mid`-bordered box on `off-white`. Each block gets its own small "Copy Citation" / "Copy BibTeX" pill button in `sky-blue`; this is the only place in the app a monospace font appears.

**Search & filter panel** — this is the primary discovery tool and deserves the most polish in the system:

- The Page Header Band search bar (white `pill`, docked navy search button) stays pinned as the top-level query field.
- Below the band, results render as a **left sidebar (desktop, ≥1024px) + results grid** layout; the sidebar is a `white`, `lg`-radius, `warm-border` card with a soft `card-lift` shadow — it's a distinct surface floating on the gridded off-white background, not bare list items directly on the page.
- **Sidebar header:** `small-label` "FILTERS" on the left, a small `gold`-filled circular count badge showing the number of active filters, and a `body-light` "Clear all" text link on the right.
- **Filter groups** are collapsible accordions, each headed by a `small-label` row with a chevron that rotates 180° on toggle (`~180ms ease`). **Data Type** and **Category** default open; **Date Uploaded** defaults collapsed.
- **Data Type / Category options** render as **toggle chips**, not checkboxes — `sm`-radius pill-ish tags in a wrapped row. Unselected: white fill, `warm-border`, `dropdown-item` text. Selected: `navy` fill, white text, no border. This is the modern, tactile pattern — tapping a chip should feel immediate, no separate "Apply" needed for these two groups.
- Do not add a package-format filter. Repository uploads are protected ZIP packages; searchable content-format disclosure lives in metadata and card/detail text.
- **Date Uploaded** renders as four preset pills — Today / This Week / This Month / This Year — same chip styling as Data Type, single-select.
- **Active filters** surface as a wrapped row of removable `gold`-outline chips directly above the results grid (not inside the sidebar), each with a small "×"; clicking one removes just that filter. This row only renders when at least one filter is active.
- **Mobile (< 1024px):** the sidebar collapses entirely; a sticky `navy` "Filters" pill button (with the same gold count badge) sits above the results grid and opens the full accordion content in a bottom sheet — off-white fill, `lg` radius on the top corners only, the same `blur(16px)` glass backdrop behind it, with a primary "Apply Filters" pill pinned to the bottom of the sheet.

**Pagination** — centered row of `sm`-radius number controls under the results grid; current page is a solid `navy` fill with white text, other pages are ghost/outline, prev/next are chevron icons in the same control size. Keep it simple — no "jump to page" input in the current release.

**Login Page** — Page Header Band ("Log In" in `section-heading`, short `body-light` subtext like "Access your ASOG TBI datasets") over the gridded off-white body. Centered card: white fill, `lg` radius, `card-lift` shadow, ~400px wide. Contents top to bottom: email field, password field (both `sm`-radius, `warm-border`, focus state swaps border to `sky-blue` with a soft `ring-white`-style glow), a right-aligned `body-light` "Forgot password?" link in `sky-blue`, a full-width primary pill "Log In" button in `sky-blue`, and a centered `body-light` line below the card — "Don't have an account? **Register**" with Register as a `sky-blue` link. Same footer as every other page.

**Upload / Update Dataset form** — same card language as Login but wider, and multi-section: dataset info (title, description, tags, category, data type, formats inside ZIP), research info (research title, project head, members), source info (source type, link), then the file zone. Group fields under `small-label` section headers; a drag-and-drop file zone accepts ZIP only, uses a dashed `warm-border` at `lg` radius that solidifies to `sky-blue` on drag-over. On submit the dataset is created with a **Pending Technical Review** status chip — make that state visible immediately so contributors know package verification happens before ethics review. The Update form is identical but pre-filled from the existing record.

**My Datasets (user dashboard)** — Page Header Band with an inline "Upload Dataset" button, body is a grid of the user's own **dataset cards**, each showing its status chip and review notifications. Governance actions do not render here; they remain in the dedicated portal shell.

**Reviewer queues** — Use a compact operational workspace rather than public dataset cards. Start with workload metrics, `Needs action / Completed / All records` tabs, and a thin filter row. Queue records show cover, assignment age, contributor, version, access request, protected package name, checklist progress, and one stage-appropriate action.

**Review workspace** — Keep evidence in the main column and a sticky checklist/decision panel on desktop. Checklist rows use three explicit states: Confirmed, Issue found, and Not reviewed. Issue found reveals an item-level note. Save Draft is secondary; revision is warning; rejection is danger; approval is the single primary completion action and remains disabled until every item is confirmed.

**Administrator moderation board** — Organize records by workflow-stage tabs instead of one mixed list. Assignment controls reveal reviewer workload. Reassignment is a separate action requiring a reason. Publication displays both approval summaries, current version, final access classification, and an explicit confirmation before the primary Publish action.

**Administrator inspection** — Use a workflow stepper and anchored Summary, Files and versions, Review timeline, Publication, and Audit activity sections. Archive and restore remain secondary lifecycle actions and never compete visually with the valid stage action.

## Motion

Keep it minimal and functional — this is an institutional tool, not a marketing site:
- Navbar top ↔ glass transition: ~250–300ms ease-out on background, text color, and shadow together.
- Hover on cards: lift 2px, fade in `card-lift` shadow, ~250ms ease-out.
- Buttons: fill/color shift only, ~150ms.
- Filter accordion chevrons: ~180ms rotate; chip selection: instant color swap, no delay.
- Dropdowns/menus/modals: fade + 4px slide, ~180ms, `blur(16px)` backdrop.
- Respect `prefers-reduced-motion` — disable lifts/slides/transitions, keep instant color changes only.

## Do's and Don'ts

| Do | Don't |
|---|---|
| Use `sky-blue` for exactly one primary action per screen | Don't use gold as a large fill color — it's an accent, not a surface |
| Keep the grid texture to one field per page | Don't tile the grid texture behind every section |
| Pair DM Serif Display italics only with the gold accent word | Don't italicize body copy, labels, or nav items |
| Maintain WCAG AA contrast (4.5:1 for body text) | Don't set `body-light` (300 weight) below 12px |
| Keep corner radii consistent within a single view | Don't mix `sm` and `pill` on sibling elements of the same type |
| Use the system monospace stack for citation/BibTeX only | Don't introduce a monospace font anywhere else in the UI |
| Keep status to the five defined chip states | Don't add a red/error hue for "Rejected" — use outline + `deep-dark` text instead |
| Give every non-Home page a Page Header Band | Don't let a page open directly on off-white — the navbar needs a navy backdrop at scroll-zero |
| Switch the navbar to glass only once `scrollY > 8px` | Don't apply the glass treatment at the very top of the page, and don't leave the top navbar with any background fill |
| Reuse the same institutional tokens across every workspace | Don't expose governance actions outside the dedicated role-gated portal shell |

## Implementation Notes (CodeIgniter 4)

- Define all tokens above as CSS custom properties in a single base stylesheet (e.g. `public/assets/css/tokens.css`), included from the main layout so every view (`app/Views/layouts/*`) inherits them.
- Load DM Serif Display and DM Sans from Google Fonts with `font-display: swap`.
- Keep the navbar and footer as CI4 view partials (`app/Views/partials/navbar.php`, `footer.php`) so the design — and the scroll behavior — stays identical across the public site and the authenticated user screens. The Page Header Band is its own partial (`partials/page_header_band.php`) that takes a heading/content slot, reused by every non-Home view.
- The scroll-state toggle is a small shared JS module (e.g. `public/assets/js/navbar-scroll.js`) attached once in the base layout — don't reimplement it per page.
- The grid-texture CSS block above is cheap (no images) — put it in a reusable `.bg-grid` utility class rather than inlining it per page.

## Agent / Build Guide

1. Start every new screen from the token tables above — colors, type, spacing, radius — before adding anything bespoke.
2. Confirm the screen and role are in scope per `CONTEXT.md`; keep reviewer and administrator controls inside the role-gated portal shell.
3. Give the screen a Page Header Band (or the full Hero, Home only) before anything else — this is what makes the navbar's transparent-then-glass behavior work.
4. Block out layout with the container/breakpoint rules first, content second.
5. Apply the grid texture to at most one light section per screen; skip it entirely on dense data screens (search results with an open filter sidebar) where it would compete with content.
6. Build components from the patterns in this doc — don't invent new card, button, badge, or filter shapes.
7. Cross-check new screens against `CONTEXT.md` for current functional scope and against **Do's and Don'ts** above for visual correctness, before calling a screen done.
