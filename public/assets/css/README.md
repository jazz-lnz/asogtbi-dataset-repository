# Application CSS

`app.css` is the only stylesheet entry point used by the application layouts.
It imports the modules below in cascade order. Later modules intentionally
override shared declarations from earlier ones — no `!important` needed.

- **`foundation.css`** — Design tokens (`:root` / `[data-theme="dark"]`),
  reset rules, site shell + navigation, buttons, toasts, notices, theme toggle,
  and global `prefers-reduced-motion` accessibility.

- **`public-ui.css`** — Shared public-facing panels, cards, forms, preview
  modals, citations, dataset-detail layout (hero card, accordion, sidebar),
  and submission states.

- **`contributor-library.css`** — Contributor dataset library and contributor-
  facing management views.

- **`upload.css`** — File upload zone, drag-and-drop controls, and upload-
  specific form elements.

- **`auth.css`** — Authentication, password recovery, error pages, and their
  responsive states.

- **`portal.css`** — Administration, moderation, review, and governance
  portal layouts.

- **`catalog.css`** — Browse hero variant (light gradient with grid overlay),
  catalog layout (250px filter / 1fr results), search toolbar, filter sidebar,
  compact result rows (flex-order layout, sky left-border, shadow cards),
  row-pill variants, pagination, and responsive breakpoints. Authoritative
  source for all browse/dataset-listing styles.

- **`home.css`** — Homepage hero composition (full-viewport, grid overlay,
  serif title, features), dashboard overview + stats, featured datasets grid,
  live feed, platform features, and CTA section. Consolidates the former
  `prototype-*` classes and old landing styles.

- **`public-discovery.css`** — Discovery home hero and sections, featured
  dataset cards, CTA band. Public page hero (shared with about pages).
  No catalog or dataset-detail overrides — those belong to `catalog.css`
  and `public-ui.css` respectively.

- **`about.css`** — About page compositions: shared hero layout,
  stat grid, intro, feature band, steps, partner cards, thesis origin,
  and developer grid.

## Maintenance rule

Add a rule to the narrowest relevant module. Shared tokens and reusable
controls belong in `foundation.css`. When two modules need different values
for the same selector, place the more specific value in the module loaded
later — the import order guarantees it wins by natural cascade without
`!important`.

## Restructuring history

The CSS was restructured in 5 phases:

1. **Consolidation** — Merged orphaned `src/input.css` (Tailwind source)
   into the app module system. Moved `scroll-behavior: smooth` to
   `foundation.css`. Removed `src/input.css`.

2. **Prototype rename** — Renamed 40+ `prototype-*` CSS classes to `home-*`
   names (e.g., `.prototype-home-hero` → `.home-hero-section`) and updated
   all references in views.

3. **Homepage consolidation** — Moved homepage composition styles from
   `public-discovery.css` into `home.css`. Removed ~370 lines of dead code
   (`.landing-hero`, `.floating-feature-card`, `.partner-logo`, `.cta-band`,
   etc.) from `home.css`.

4. **`!important` refactoring** — Migrated all `!important` overrides from
   `public-fidelity.css` into their proper modules (`catalog.css`,
   `public-ui.css`, `public-discovery.css`, `foundation.css`) without
   `!important`. Relied on import-order cascade instead of specificity wars.
   Removed `public-fidelity.css`.

5. **Module extraction** — Extracted about-page styles and dataset detail-page
   band from `public-fidelity.css` into a dedicated `about.css` module and
   `public-ui.css` respectively.

### Cascade design

Each module has a clear ownership boundary. No module should use `!important`
to fight another module — if a later module needs a different value, its rule
wins by import order alone. The only remaining `!important` occurrences are
self-contained `[hidden]` attribute overrides and error-state borders,
which are standard unavoidable patterns.
