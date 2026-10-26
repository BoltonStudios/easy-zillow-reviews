# Zillow/Bridge terms: rules that constrain the plugin

Source: [Zillow API Terms of Use](https://bridgedataoutput.com/zillowterms), **read in full 2026-10-26**. The page is
a JavaScript app (a plain fetch returns only "Bridge"); the verbatim text is saved locally at
`.private/terms/zillow-api-terms-2026-10-26.md` (not committed: Zillow's copyright). **Check wording there before
citing a section.** The terms change without notice (§23); if the copy is missing or old, ask Aaron to paste the
current page. My reading, not legal advice. Bridge's answers: [bridge-support-log.md](bridge-support-log.md).

The terms cover seven APIs (§1). Only the general sections (§1–25) and **§31 Reviews Data** apply to agent/team
reviews. Lender reviews use the separate Mortgages API and the [Zillow Terms of Use](https://www.zillow.com/z/corp/terms/),
not this document.

## Rules for the plugin's design

- **No caching or storing data** (§5.18b; End Users too, §6). The plugin fetches live on every display and only
  de-duplicates within a request. How far this reaches (e.g., to a site's page cache) is an open question; see
  "Caching" below. On termination the user must delete all copies (§11); the plugin keeps none.
- **No shared API key** (§4 and §7 "non-sublicensable"; §5.1 no access "on behalf of any other person or otherwise
  share your access credentials"; §25 credentials not transferable). Each site owner needs their own Bridge token. A
  partner exception would need Zillow's written approval (contact: api@bridgeinteractive.com).
- **Whose reviews** (§31.1a): only "for yourself and/or a real estate professional you are affiliated with." Team
  reviews and the planned brokerage roster rely on "affiliated."
- **Where** (§31.1b): only "on the website(s) indicated in your application." A new or moved site needs the
  application updated. Matters for onboarding and for designers who reuse a token on a staging site. The website is
  "indicated" by the **Website** field on the registration form. (Application Profile → Website URL starts empty
  and is optional; see bridge-onboarding.md.) Second site: "Create New Application". Bridge doesn't enforce the
  website at the API level, so a working token isn't evidence either way.
- **Chronology is the only allowed selection** when showing a subset (§31.2). No filtering by rating, city, or
  service type, and no "positive only."
- **Required display** (§31.2): the label "Reviews on Zillow", and a link to the professional's profile using the URL
  from the API.
  Met by the profile card ("79 Reviews on Zillow") and the "View all … Reviews on Zillow" link; a fine-print label
  appears only when both are hidden (1.7.1 removed the heading 1.7.0 added above the reviews).
  - Placement: §31.2 sets no position for this label. Where the terms want placement they say so: "above any display"
    for the Zestimate, Economic Data, and Public Records labels (§26.1.1, §27.1.1, §28.2.1) and for "Provided by Zillow"
    on Directory API data (§31.2). The Reviews label has no such wording.
  - "Any use of the words 'ratings' or 'reviews'" must use the label. Team reviews show "Review of [member]"; the
    display as a whole still carries the label.
  - "Provided by Zillow" applies only to Directory API data, which the plugin doesn't use.
- **Star graphics** (§31.2): ratings must use "the 'star' graphics provided therein" and no other graphic. The Bridge
  Reviews API provides none (checked 2026-10-25 against a live response and the OData `$metadata`: the only rating
  fields are the numbers `Rating` and `AverageReviewRating`; no image fields on `Reviews` or `Reviewees`). The clause
  appears to date from Zillow's legacy API, which returned rating images. Best effort: draw the numeric rating as
  plain stars, with no other badge or graphic. Bridge support has said a reasonable best effort on compliance
  "should be fine." Builder style controls (star color/size) only restyle those stars.
- **Logo** (§7): the branding license covers the Zillow name, logos, and links "only and solely to the extent
  necessary to provide attribution." §31 doesn't require a logo, but Bridge gave size rules for it, knowing it was for
  this plugin (2026-02-16), so showing it at those sizes is fine: the house logo at least 58px tall; the primary logo
  (house + name) more than 24px tall. The plugin shows the primary
  logo SVG from zillowstatic.com at 115x25.
- **Rate limit: 5,000 Reviews API calls/hour** (§31.3), more only with Zillow's written approval. Each display uses
  2 calls (3 with team reviews). Bridge's public FAQ figure of 1,000 calls/day matches Public Records (§28.3.2), not
  Reviews.
- **Attribution** (§5.5): never make the data appear to come from anyone but Zillow; don't mix it with other review
  sources in one feed.
- **No claims about the data** (§5.2): no statements to visitors about its quality or accuracy.
- **No implied relationship** (§25): don't say or imply the plugin is official, endorsed, or a Zillow partner.
  "Uses Zillow's official APIs" describes the API, not us; keep it that way.
- **US only** (§25): no "Zillow Materials" provided to anyone outside the US or housed on servers outside the US.
  Read literally, a site hosted outside the US (or a CDN edge holding pages) is out of bounds. Not yet asked.
- **Apps** (§5.6): bars implementing or distributing the Data "on any app (mobile or otherwise)" without written
  permission. **Doesn't apply to the plugin:** it contains no Data. It's code each site owner installs; reviews are
  fetched with the owner's token and shown on the owner's website, the use §31.1(b) allows. Bridge knew it was a
  WordPress plugin and gave display guidance rather than objecting (support log, 2026-02-16). Would matter only for
  a real mobile app.
- **Secure transmission** (§14): "current best practices." The plugin calls the API over HTTPS; recommend HTTPS sites.
- **May become paid** (§8): Zillow can start charging with 30 days' notice. "Free token" is true today, not promised.

## Applicant obligations (relevant to onboarding)

- §3: register an account, then submit an application; Zillow approves or denies "in its sole discretion."
  §3 names `bridgedataoutput.com/zgdata` for Zestimate, Public Records, Economic, and Reporting data but doesn't
  list Reviews; §31.1 still refers to "your application ... for access to the Reviews Data."
- §3: by applying you confirm you are 18+, the information is accurate, and you haven't been denied or revoked
  before. Applying for an organization means you can bind it.
- §6: report unauthorized use of your account to api@bridgeinteractive.com.
- §11: cancel by emailing cancel@bridgeinteractive.com.

## Not applicable to Reviews (don't import these rules)

From §26–28 (Zestimate, Economic, Public Records) only: logo "directly adjacent" and "cannot be resized," alt text
"Real Estate on Zillow," "Provided via the … API" labels, the "© Zillow, Inc., 2006-2019" footer, and the "as is"
disclaimer requirement.

## Caching: literal vs. reasonable reading

- **Literal reading:** no copy of Zillow data may exist anywhere, including a site's page cache or CDN. Then every
  site with page caching is out of step today, and only loading reviews in the browser fixes it.
- **Reasonable reading (current working stance):** the plugin itself never stores or persists Zillow data. A page
  cache is general-purpose infrastructure the site owner controls; it holds rendered pages briefly and refreshes them.
  The FAQ already tells users that new reviews appear when the cache refreshes.
- §6 adds: "use all reasonable means to prevent End Users from caching, downloading, or otherwise retaining copies of
  the Data." "End Users" are the site's visitors, which points at browser/visitor copies rather than the site
  owner's page cache, but it's another reason to ask.
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
