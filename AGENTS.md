# Easy Zillow Reviews — agent notes

WordPress plugin that shows Zillow reviews for agents, teams (Premium), and lenders (Premium) via a block,
Elementor widget, classic widget, and shortcode. Free on wordpress.org; Premium via Freemius.
Public repo: https://github.com/BoltonStudios/easy-zillow-reviews — **anything committed is public.**

## Read before working

| Need | File |
|---|---|
| Status, release plan, backlog | [ROADMAP.md](ROADMAP.md) |
| Release steps (version bump, zips, SVN, Freemius) | [RELEASING.md](RELEASING.md) |
| Zillow/Bridge terms that limit features | [docs/zillow-terms-notes.md](docs/zillow-terms-notes.md); full text in `.private/terms/` |
| What Bridge support has told us; open questions | [docs/bridge-support-log.md](docs/bridge-support-log.md) |
| Bridge signup / dataset request as users see it | [docs/bridge-onboarding.md](docs/bridge-onboarding.md) |
| Bridge Reviews API endpoints, quirks, test screennames | [docs/bridge-api.md](docs/bridge-api.md) |
| Raw emails, screenshots, API samples (local only) | `.private/` (gitignored; may not exist on other machines) |

## Hard rules (Zillow/Bridge terms — details in docs/zillow-terms-notes.md)

**Before answering any question about the terms, read `docs/zillow-terms-notes.md`, and check exact wording in
`.private/terms/zillow-api-terms-*.md` (the full text).** https://bridgedataoutput.com/zillowterms is a JavaScript
page and a plain fetch returns nothing. Don't guess at section numbers or limits.

- Never store or cache review data (no transients/options/post meta). Fetch live; de-duplicate only within a request.
- Each site owner uses their own Bridge token. No shared or bundled key; no applying on someone else's behalf (§5.1).
- Reviews only for the applicant or affiliated professionals, only on the websites named in their application (§31.1).
- Show the newest reviews only. Never filter or sort by rating, city, or service type.
- Always show "Reviews on Zillow" and the profile link from the API. Don't mix Zillow reviews with other sources.
- Never imply the plugin is official, endorsed by, or partnered with Zillow (§25).
- Never display `FreeFormLocation` (contains street addresses).
- Rate limit: 5,000 Reviews API calls/hour per token (§31.3).
- Zillow logo: primary logo > 24px tall (plugin uses 115x25).

## Keeping these notes current

- New fact from Bridge/Zillow → dated entry in `docs/bridge-support-log.md`; raw email in `.private/emails/`.
- Don't commit tokens, customer emails, or screenshots showing account details; put them in `.private/`.
- `AGENTS.md`, `docs/`, `ROADMAP.md`, and `RELEASING.md` are `export-ignore`d in `.gitattributes`, so they stay
  out of the release zips.

## Local testing

- Site: http://bstest.local (Local by Flywheel). MySQL socket: `~/Library/Application Support/Local/run/8aQ1bxoAV/mysql/mysqld.sock`.
- WP-CLI: `php -d mysqli.default_socket=<socket> wp-cli.phar --path="/Users/aaronbolton/Local Sites/bstest/app/public" …`
- Build the block: `npm install && npm run build` (outputs to `gutenberg/build/`).
- The "Test connection" button on the settings page checks Bridge and lender credentials without saving.
