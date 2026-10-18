# Easy Zillow Reviews — Roadmap & Working Notes

Working list of upgrades and context for future sessions. **Not part of the plugin** — leave this file
(and `node_modules/`, `.git*`, `.DS_Store`) out of release zips.

_Last updated: 2026-10-18_

## Current state

| Version | Status | Highlights |
|---|---|---|
| 1.6.5 | Committed, not released | Security/escaping fixes, newest-first reviews (`$orderby`/`$top`), `wp_remote_get` with 8s timeout, admin-only errors, fixed "More" button |
| 1.7.0 | Committed, not released | Setup guide + "Test connection", "Reviews on Zillow" label, fixed Zillow logo, block apiVersion 3 + live preview, Premium team reviews + carousel, WP 6.3 / PHP 7.4 minimums |

Before releasing 1.7.0:
- [ ] Test the **free** zip that Freemius generates (Premium code in `premium/` and `__premium_only` blocks is stripped). The free build was never tested directly.
- [ ] Package without `node_modules/`; keep `gutenberg/src/` (readable source) and `gutenberg/build/`.
- [ ] Local test site's screenname "Laura AVL" isn't found by Bridge; update it (the "Test connection" button confirms).

Sibling plugin: Read More WP 1.1.8 and 1.2.0 (+ a duplicate-markup fix for Elementor/sliders) are also committed and unreleased in `read-more-wp/`.

## Zillow/Bridge rules that constrain ideas

Source: https://bridgedataoutput.com/zillowterms (my reading — not legal advice)

- **No caching or storing data** (§5.18b). Every display fetches live; only in-request de-duplication.
- **No shared API key** (§5.1, §31.1, non-sublicensable license). Each site owner needs their own Bridge token. A partner exception would need Zillow's written approval (contact: api@bridgeinteractive.com).
- **Chronology is the only allowed selection** when showing a subset (§31.2). No filtering by rating, city, or service type.
- **Required display:** the label "Reviews on Zillow", and a link to the professional's profile using the URL from the API (§31.2).
- **Rate limit:** 5,000 Reviews API calls/hour per token (§31.3). Each display uses 2 calls (3 with team reviews).
- Keep Zillow data clearly attributed to Zillow (§5.5): don't mix it with other review sources in one feed.

## Upgrade backlog

### Next: 1.8.0 (recommended)

1. **"Review me on Zillow" button** — Bridge's reviewee record includes `ReviewRequestURL` (direct link to Zillow's write-a-review page). No extra API calls.
   - Free: button under the reviews + standalone block/shortcode.
   - Premium idea: printable **QR code** of the link (closing packets, open houses, business cards).
2. **Compact rating badge** — e.g. "★ 4.9 · 56 Reviews on Zillow", linking to the profile. Block + shortcode for headers, footers, and sidebars. One API call (the reviewee lookup).
3. **Load reviews after the page** — fetch in the browser through a REST endpoint (no storage). Removes the 300–500 ms API wait from page load (speed/SEO), and page caches no longer hold a copy of Zillow data. Keep a server-rendered option for sites that want it.

### Later

4. **Elementor and Divi modules** — realtor sites are mostly built with these; native modules with style controls. (Bolton Studios already makes Divi plugins.)
5. **Branding/style presets** — card styles, brand colors, fonts, dark mode.
6. **Lender compliance helpers** — show NMLS# and company name on the lender card; optional Equal Housing Opportunity line. (Lenders confirm their own requirements.)
7. **Dashboard widget** — current rating, review count, newest review; fetched when viewed, never stored.
8. **Brokerage roster** — page listing affiliated agents with rating, count, and review link (§31.1 allows affiliated professionals). Watch API usage per page.
9. **Own testimonials** — simple testimonials post type shown in the same style, as a *separate* section from Zillow reviews.

### Ruled out (terms)

- Filtering by rating, city, or service type (buyer/seller/rental).
- New-review email alerts (needs stored history; ask Zillow first).
- One feed mixing Zillow and other review sources.
- Review schema / rich snippets (also ineligible on Google for self-serving reviews).

### Smaller cleanup

- Premium settings callbacks are still nested functions in `add_premium_settings()` (same pattern Read More WP fixed); convert to methods.
- Remaining Plugin Check warnings: naming-prefix and nonce-recommendation warnings in admin partials.

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
