# Easy Zillow Reviews — Roadmap & Working Notes

Working list of upgrades and context for future sessions. **Not part of the plugin** — leave this file
(and `node_modules/`, `.git*`, `.DS_Store`) out of release zips.

_Last updated: 2026-10-22_

## Current state

| Version | Status | Highlights |
|---|---|---|
| 1.6.5 | Committed, not released | Security/escaping fixes, newest-first reviews (`$orderby`/`$top`), `wp_remote_get` with 8s timeout, admin-only errors, fixed "More" button |
| 1.7.0 | Committed, not released | Setup guide + "Test connection", "Reviews on Zillow" label, fixed Zillow logo, block apiVersion 3 + live preview, Premium team reviews + carousel, WP 6.3 / PHP 7.4 minimums |

Before releasing 1.7.0 (1.6.5 was never released, so its fixes ship with 1.7.0; wordpress.org sites update
from 1.6.4 straight to 1.7.0, so test both versions' changes):
- [x] "Test connection" button (tested 2026-10-22).
- [ ] Local test site's screenname "Laura AVL" isn't found by Bridge; update it (the "Test connection" button confirms).
- [ ] **Free zip from Freemius** (never tested; `premium/` and `__premium_only` code is stripped):
  - Activates with no errors or warnings (`WP_DEBUG` on).
  - No carousel, team, or lender options in the block, settings, or widget.
  - `[ez-zillow-reviews layout="carousel" team="true"]` falls back to a normal layout without errors.
  - `[ez-zillow-lender-reviews]` fails quietly.
- [ ] **Upgrade from 1.6.4**: save a token, screenname, and layout, and place a block, widget, and shortcode on 1.6.4; then update.
  - Settings survive the new save sanitization.
  - Old blocks render and open in the editor without errors. Expected to pass: the block name and attributes are unchanged and the block is dynamic.
  - A site with only a ZWSID shows the switch-to-Bridge notice.
- [ ] **Displaying reviews (free)**:
  - Block: live preview, screenname field, list/grid, columns/count/excerpt.
  - Shortcode: all parameters, plus invalid values like `columns="two"` (1.6.5).
  - Widget title appears inside the widget (1.6.5).
  - Newest first (1.6.5): compare `missypittman` with the Zillow profile.
  - Hyphenated screenname (1.6.5).
  - "More" button: mouse, keyboard (Tab + Enter), and JavaScript off (full text shown).
  - "Reviews on Zillow" label, profile link, logo (not broken), updated disclaimer.
  - City and state only (no street address); no "who 2023".
  - Assets load only on pages with reviews; no console errors without jQuery.
  - Bad token: admins see the error; logged-out visitors see nothing (1.6.5).
  - Two review blocks on one page: "More" buttons don't conflict; identical requests made once (1.6.5).
- [ ] **Premium**:
  - Team reviews (`jeffreysellyohome`): each review links to that member's profile.
  - Carousel: columns set how many reviews show, autoplay on/off, keyboard navigation, two carousels on one page, phone widths.
  - Lender reviews still work (the asset-loading change affects them), including "Test connection" with lender credentials.
  - Activating Premium cleanly deactivates the free version.
- [ ] **Environment**: PHP 7.4 and 8.x; `debug.log` clean after a full pass. Optional: settings page on a subdirectory install.
- [ ] **Packaging**: check the zip Freemius actually produces. It excludes `node_modules/`, `ROADMAP.md`, and `package*.json`; it keeps `gutenberg/src/` (readable source) and `gutenberg/build/`. (`.gitattributes` only covers `git archive`.)

Sibling plugin: Read More WP 1.1.8 and 1.2.0 (+ a duplicate-markup fix for Elementor/sliders) are also committed and unreleased in `read-more-wp/`.

## Zillow/Bridge rules that constrain ideas

Source: https://bridgedataoutput.com/zillowterms (my reading — not legal advice)

- **No caching or storing data** (§5.18b). The plugin fetches live on every display and only de-duplicates within a
  request. How far this reaches (e.g., to a site's page cache) is an open question; see "Caching" below.
- **No shared API key** (§5.1, §31.1, non-sublicensable license). Each site owner needs their own Bridge token. A partner exception would need Zillow's written approval (contact: api@bridgeinteractive.com).
- **Chronology is the only allowed selection** when showing a subset (§31.2). No filtering by rating, city, or service type.
- **Required display:** the label "Reviews on Zillow", and a link to the professional's profile using the URL from the API (§31.2).
- **Rate limit:** 5,000 Reviews API calls/hour per token (§31.3). Each display uses 2 calls (3 with team reviews).
- Keep Zillow data clearly attributed to Zillow (§5.5): don't mix it with other review sources in one feed.

### Caching: literal vs. reasonable reading

- **Literal reading:** no copy of Zillow data may exist anywhere, including a site's page cache or CDN. Then every
  site with page caching is out of step today, and only loading reviews in the browser fixes it.
- **Reasonable reading (current working stance):** the plugin itself never stores or persists Zillow data. A page
  cache is general-purpose infrastructure the site owner controls; it holds rendered pages briefly and refreshes them.
  The FAQ already tells users that new reviews appear when the cache refreshes.
- On past questions, Bridge has leaned toward the reasonable reading, but **caching hasn't been asked about
  specifically.** Ask before designing 1.8.0, and get the answer in writing (api@bridgeinteractive.com):
  1. Is it acceptable for a site's page cache or CDN to hold pages that display reviews? For how long (e.g., 24 hours)?
  2. May the plugin keep a short-lived server-side cache (e.g., 15–60 minutes, cleared on expiry) to stay within the
     rate limit, especially for a badge shown on every page?
  3. If yes, what's the maximum duration?
- Until there's an answer: no plugin-side caching, and don't market browser loading as a compliance fix. Build so
  either answer works: one cache-duration setting or filter, defaulting to 0.
- How the answer changes the plan:
  - **Short-lived cache allowed:** the quota concerns below mostly go away. Browser loading is a speed option, and
    the badge and roster get cheap.
  - **Not allowed:** browser loading becomes the default, and quota protection matters more.

## Upgrade backlog

### Recommended sequence

1. **1.7.0** — finish the tests above and release.
2. **1.8.0** — load reviews after the page, compact rating badge, and "Review me on Zillow" button. Ship together: the
   badge's API cost depends on how loading works. Ask Bridge the caching questions first.
3. **1.9.0** — style presets.
4. **2.0.0** — page builder module (Divi or Elementor) built on the presets.
5. **Then** — brokerage roster, once the batch lookup is confirmed (see item 7).

Lender compliance helpers are small; fit them into any release with room.

### Premium positioning (proposal)

The published pitch (lender reviews + priority support) undersells Premium; 1.7.0 adds team reviews and the
carousel. Pitch Premium around business uses (teams, brokerages, getting more reviews), with presentation as a bonus
rather than the main reason to buy.

- **Free:** list/grid, basic badge, request button, browser loading.
- **Premium:** team reviews, carousel, lender reviews, builder module with the full preset set, brokerage roster,
  QR code.
- Both tiers keep the required attribution and display rules.

### 1.8.0

1. **Load reviews after the page** — fetch in the browser through a REST endpoint. Removes the 300–500 ms API wait from
   page load, and page caches no longer hold Zillow data. Both tiers. Keep a server-rendered option. Sell it on speed
   only; don't promise SEO gains (reviews loaded in the browser are *less* likely to be indexed).
   - The endpoint takes an instance ID (block/widget/shortcode) or signed parameters and reads saved settings. Never
     accept an arbitrary screenname, or anyone could use up the site owner's API quota.
   - Token stays server-side. Send `Cache-Control: no-store`, and check that host and CDN caches (e.g., Cloudflare APO)
     respect it.
   - Reserve space to avoid layout shift. Test the loading, empty, error, and rate-limited states.
   - Trade-off: without page caching, every view makes live API calls (see "API quota notes").
2. **Compact rating badge** — e.g. "★ 4.9 · 56 Reviews on Zillow", linking to the profile. Block + shortcode for headers,
   footers, and sidebars. One API call (the reviewee lookup). Free: basic badge; Premium can add styles. Must include
   the "Reviews on Zillow" label and profile link. A badge in the header or footer makes one call per page view
   across the whole site.
3. **"Review me on Zillow" button** — Bridge's reviewee record includes `ReviewRequestURL` (direct link to Zillow's
   write-a-review page). No extra API calls. Free: button under the reviews + standalone block/shortcode.
   - Premium idea: printable **QR code** of the link (closing packets, open houses, business cards). Admin-side only,
     but a weak reason to upgrade on its own (QR generators are free); low priority.

### API quota notes

- 5,000 calls/hour per token. Reviews display: 2 calls (3 with team reviews). Badge: 1. Roster: 1 per agent unless the
  batch lookup works.
- Page caches currently absorb most traffic. Browser loading and sitewide badges remove that buffer.
- On a 429 (rate limited), hide the section or badge for visitors; show admins why.
- A badge and reviews on the same page should share one reviewee lookup. Server-rendered pages already de-duplicate.
  With browser loading, combine them into one REST request.
- A short-lived cache, if Bridge allows it, solves most of this.

### Later

4. **Style presets (1.9.0)** — card styles, brand colors, fonts, dark mode. Build them as CSS custom properties / block
   styles so they work in the block, shortcode, and widget. Builder modules then only need to expose them.
5. **Page builder module (2.0.0)** — native module with live preview and style controls. Choose Divi or Elementor first
   based on data, e.g., active plugins reported by Freemius opt-ins. Bolton Studios already makes Divi plugins, so
   Divi may be cheaper to start. The shortcode already works in both builders; the module adds controls and preview.
   - **Elementor: built** on branch `feature/elementor-widget` (`elementor/`). Renders through the same
     `ezrwp_render_block` filter as the block, so Premium options appear automatically. Ships in the free version;
     to make it Premium-only, wrap `new Easy_Zillow_Reviews_Elementor()` in the Premium check. Style controls cover
     text, stars, reviewer/date, and slider buttons; card styles wait for the presets (item 4).
6. **Lender compliance helpers** — show NMLS# and company name on the lender card; optional Equal Housing Opportunity
   line. (Lenders confirm their own requirements.) Small, and lenders are existing paying customers who generally
   must show an NMLS# in ads; don't defer indefinitely.
7. **Brokerage roster** — page listing affiliated agents with rating, count, and review link (§31.1 allows affiliated
   professionals). Strongest later Premium feature. Before committing, confirm the batch Reviewee lookup returns
   `ReviewCount` and `AverageReviewRating`; otherwise it costs 1 call per agent per view.

### Deferred

- **Dashboard widget** — current rating, review count, newest review; fetched when viewed, never stored. Convenient,
  but little reason to install or upgrade.
- **Own testimonials** — simple testimonials post type shown in the same style, as a *separate* section from Zillow
  reviews. Adds maintenance, many sites already have this, and it risks blurring Zillow attribution (§5.5).

### Ruled out (terms)

- Filtering by rating, city, or service type (buyer/seller/rental).
- New-review email alerts (needs stored history; ask Zillow first).
- One feed mixing Zillow and other review sources.
- Review schema / rich snippets (also ineligible on Google for self-serving reviews).

### Smaller cleanup

- Premium settings callbacks are still nested functions in `add_premium_settings()` (same pattern Read More WP fixed); convert to methods.
- Remaining Plugin Check warnings: naming-prefix and nonce-recommendation warnings in admin partials.
- Grid layout: switch from floats to CSS Grid. At tablet widths a 3-column grid drops to 2 columns, but the plugin still clears after every third review, so rows read 2+1, 2+1. Remove the clearing `<div style="clear:both">` from `layout_reviews()` (professional and lender) when switching.

## Bridge API notes

- Reviewee lookup: `GET https://api.bridgedataoutput.com/api/v2/reviews/reviewee?RevieweeScreenName=…` → `AccountIdReviewee`, `RevieweeFullName`, `RevieweeProfileURL`, `ReviewCount`, `AverageReviewRating`, `ReviewRequestURL`, `RevieweeTitle`, `RevieweeBusinessName`.
- Reviews: `GET …/api/v2/OData/reviews/Review?$filter=AccountIdReviewee eq 'ID'&$orderby=ReviewDate desc&$top=N`. Default page is 10 in no particular order, so always send `$orderby` and `$top`.
- Team reviews: `$filter=AccountIdReviewee eq 'ID' or TeamLeadAccountId eq 'ID'`; batch name/profile lookup via `…/OData/reviews/Reviewee?$filter=AccountIdReviewee eq 'a' or …`.
- Auth: `Authorization: Bearer <token>` header works (keeps the token out of URLs).
- Data quirks: some records have shifted columns (service description in `FreeFormLocation`, year in `ServiceProviderDesc`); `FreeFormLocation` includes the street address (never display it).
- Public test screennames: `missypittman` (56 reviews), `jeffreysellyohome` (team lead with team reviews).

## Local testing tips

- Site: http://bstest.local (Local by Flywheel). MySQL socket: `~/Library/Application Support/Local/run/8aQ1bxoAV/mysql/mysqld.sock`.
- WP-CLI: `php -d mysqli.default_socket=<socket> wp-cli.phar --path="/Users/aaronbolton/Local Sites/bstest/app/public" …`
- Build the block: `npm install && npm run build` (outputs to `gutenberg/build/`).
- The "Test connection" button on the settings page checks Bridge and lender credentials without saving.
