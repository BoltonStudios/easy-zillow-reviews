# Easy Zillow Reviews — Roadmap & Working Notes

Working list of upgrades and context for future sessions. **Not part of the plugin** — leave this file
(and `node_modules/`, `.git*`, `.DS_Store`) out of release zips.

_Last updated: 2026-10-26_ (terms, Bridge API notes, and support answers moved to `docs/`; start with [AGENTS.md](AGENTS.md))

## Current state

| Version | Status | Highlights |
|---|---|---|
| 1.6.5 | Committed, not released | Security/escaping fixes, newest-first reviews (`$orderby`/`$top`), `wp_remote_get` with 8s timeout, admin-only errors, fixed "More" button |
| 1.7.0 | Released 2026-10-23 | Setup guide + "Test connection", "Reviews on Zillow" label, fixed Zillow logo, block apiVersion 3 + live preview, Premium team reviews + carousel, WP 6.3 / PHP 7.4 minimums |
| 1.7.1 | Released 2026-10-25 | Removed the "Reviews on Zillow" heading (fine print only when the profile card and "View all" link are both hidden) |
| 1.8.0 | Released 2026-10-26 (README changelog) | Elementor widget (free) with per-widget show/hide, tablet/phone columns, style controls, Premium section for free users |
| 1.8.1 | Implemented 2026-10-26, not released | Onboarding rewrite from the verified Bridge signup flow; see "1.8.1: onboarding" below |

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
- [ ] **Packaging**: check the zip Freemius actually produces. It excludes `node_modules/`, `ROADMAP.md`, `AGENTS.md`, `docs/`, and `package*.json`; it keeps `gutenberg/src/` (readable source) and `gutenberg/build/`. (`.gitattributes` only covers `git archive`.)

Sibling plugin: Read More WP 1.1.8 and 1.2.0 (+ a duplicate-markup fix for Elementor/sliders) are also committed and unreleased in `read-more-wp/`.

## Zillow/Bridge rules that constrain ideas

Moved to [docs/zillow-terms-notes.md](docs/zillow-terms-notes.md) (terms, display rules, caching question) and
[docs/bridge-support-log.md](docs/bridge-support-log.md) (what Bridge has told us). Read them before designing a feature.

## Upgrade backlog

### Recommended sequence

1. **1.7.0** — finish the tests above and release.
2. **1.8.0** — load reviews after the page, compact rating badge, and "Review me on Zillow" button. Ship together: the
   badge's API cost depends on how loading works. Ask Bridge the caching questions first.
3. **1.9.0** — style presets.
4. **2.0.0** — page builder module (Divi or Elementor) built on the presets.
5. **Then** — brokerage roster, once the batch lookup is confirmed (see item 7).

Lender compliance helpers are small; fit them into any release with room.

### 1.8.1: onboarding (implemented 2026-10-26, not released)

Align setup with the Bridge flow verified on 2026-10-26 ([docs/bridge-onboarding.md](docs/bridge-onboarding.md)):
one form, email verification, instant auto-approval, **Server Token** under API Access. Decisions made with Aaron.

1. **Setup guide** (`ezrwp_section_for_zillow_professional_parameters_cb`): "about 5 minutes"; five steps: (1) fill
   out the form at `/zgdata` — Website = the site where reviews appear (show this site's `home_url()`), Product
   Name = business name, Product Type = Website, check Zillow Agent Reviews; ignore the "10+ days" note, it's usually
   approved right away; (2) verify email (check spam), log in; (3) API Access › Server Token › Show Token (not the
   Client ID, Client Secret, or Browser Token); (4) screenname; (5) Test connection, Save. Plus "Already have a
   Bridge account? Create New Application, then request Zillow Agent Reviews under Data Access" (unverified past
   the form; ship anyway). No Usage Description template.
2. **Token field:** label "Bridge Server Token" (option key unchanged); help text points to API Access › Server
   Token instead of repeating signup links.
3. **Screenname field:** accept a full Zillow profile URL and extract the screenname (settings save + Test
   connection).
4. **`test_connection()`:** `Invalid access_token format` → "That isn't your Server Token…"; other 401/403 →
   point to API Access and Data Access › Approved Datasets (drop "approved your access" emphasis); add 429 message;
   update "Enter your … token first."
5. **Other strings:** admin-only errors in `fetch_reviews_from_zillow()` (make translatable, "Server Token");
   `setup_notice()` ("…a free Bridge Server Token (about 5 minutes to get)…"); deactivation survey option.
6. **README:** How it works step 1; Installation mirrors the guide; rewrite "Where do I get a Bridge API access
   token?"; new FAQs "Which Bridge token do I use?", "I have more than one website" (one application per site),
   "I shared or leaked my token" (Regenerate Tokens). No web-designer FAQ until ticket 15282909 is answered.
   Changelog 1.8.1. Retake screenshot 4 (Settings) in SVN assets.
7. **Docs:** update the claims table in docs/bridge-onboarding.md.
8. **Verify:** Test connection with Server/Browser/Client ID/Client Secret tokens, an unknown screenname, and a
   pasted profile URL; settings page and dashboard notice; PHP lint and Plugin Check.
   - Done 2026-10-26 via WP-CLI on bstest.local: PHP lint; `normalize_screenname()` (plain, URL, `%20`, query
     string); Test connection: empty token, malformed token ("isn't your Server Token"), well-formed bad token
     ("Access token not found" branch), saved token + profile URL (connected), unknown screenname; sanitizer saves
     `pstalker` from a URL; settings section rendered and checked visually.
   - Plugin Check run: no findings in changed files (see "Smaller cleanup"). Still to do: dashboard notice on a site without a token; real
     Client ID/Secret/Browser Token in the browser; 429 message (can't trigger safely).
   - Also changed: classic widget help text ("Bridge Server Token", correct menu path); README External services
     wording. Screenname normalization also applies to block/shortcode/Elementor values (in
     `fetch_reviews_from_zillow()`). Release steps (version bump, changelog date, screenshot 4) per RELEASING.md.

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

- 5,000 calls/hour per token (§31.3; the FAQ's 1,000/day is the Public Records limit). Reviews display: 2 calls (3 with team reviews). Badge: 1. Roster: 1 per agent unless the
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
   - **Elementor: built for 1.8.0** on branch `feature/elementor-widget` (`elementor/`). Renders through the same
     `ezrwp_render_block` filter as the block, so Premium options appear automatically. Free, with a "Premium"
     panel section for free users (Elementor 4 no longer shows `get_upsale_data()` boxes). Per-widget show/hide
     (`display` arg → `Easy_Zillow_Reviews_Data::set_display_options()`), tablet/phone columns (CSS only when set),
     style controls for text, stars, reviewer/date, and slider buttons; card styles wait for the presets (item 4).
     Tested on Elementor 3.5.6, 3.35.9, and 4.3.5 (2026-10-25). Not yet: per-display show/hide in the block and
     shortcode (the `display` arg is ready for them), agent page templates with dynamic tags.
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

- **Plugin Check (2026-10-26, after the 1.8.1 changes):** no findings in files changed for 1.8.1. One ERROR,
  `OffloadedContent` in `public/class-easy-zillow-reviews-template-loader.php` (the Zillow logo `<img>` loaded from
  zillowstatic.com, since 1.8.0). Defensible as the service's own branding, already disclosed under External
  services, but bundling the SVG in the plugin would clear the error and the visitor-IP note. Bridge's 2026-02-16
  reply to the "bundle a smaller logo" email gave only size rules, which reads as OK. Other findings: the known
  prefix warnings, `load_plugin_textdomain()` discouraged, and the Elementor core hook
  `elementor/element/should_render_shortcode` (false positive). Hidden files, `.DS_Store`, `.private/`, and dev
  markdown are flagged only because Plugin Check scans the working folder; the release zip excludes them.
- Premium settings callbacks are still nested functions in `add_premium_settings()` (same pattern Read More WP fixed); convert to methods.
- Remaining Plugin Check warnings: naming-prefix and nonce-recommendation warnings in admin partials.
- Grid layout: switch from floats to CSS Grid. At tablet widths a 3-column grid drops to 2 columns, but the plugin still clears after every third review, so rows read 2+1, 2+1. Remove the clearing `<div style="clear:both">` from `layout_reviews()` (professional and lender) when switching.

## Bridge API notes and local testing

Moved to [docs/bridge-api.md](docs/bridge-api.md) and [AGENTS.md](AGENTS.md).
