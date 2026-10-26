# Bridge / Zillow support log

What Bridge Interactive (api@bridgeinteractive.com) or Zillow has told us, newest first. One entry per answer:
date, who/where, the question, and the answer (quote the key sentence). Keep full emails in `.private/emails/`
(not committed). When an answer changes a rule, update [zillow-terms-notes.md](zillow-terms-notes.md) too.

## Answers

### 2026-10-26 — Test registration: Zillow Agent Reviews is auto-approved

Not a support answer, but first-hand: a new registration requesting only Zillow Agent Reviews was "automatically
approved" in the same minute the verification email arrived (dashboard Recent Activity). Details and screens:
[bridge-onboarding.md](bridge-onboarding.md#test-registration-2026-10-26-bridge-sign-up-test). Since no person
reviews the request, approval says nothing about whether a particular use fits the terms.

### 2026-10-26 — Bridge public FAQ (not support)

Source: https://www.bridgeinteractive.com/developers/zillow-group-data/ (checked 2026-10-26).

- Registration is open to the public, but each request is an application: "Once we are able to review your
  application, we will be in contact to verify if your use case meets our Terms of Use."
- Quota: "After your application and use case are reviewed, you will be issued a maximum of 1,000 calls/day per data
  set." **Not about Reviews:** the FAQ lists only Public Records, Zestimates, and Econ data, and 1,000/day is the
  Public Records limit (§28.3.2). Reviews is 5,000 calls/hour (§31.3), confirmed against the full terms 2026-10-26.

### 2026-10-22 — Asked: may we provision per-site access for site owners? (PENDING)

Ticket 15282909, "Request to provision per-site Reviews API access: Easy Zillow Reviews (WordPress plugin)". Full
email: `.private/emails/Request to provision per-site Reviews API access Easy Zillow Reviews (WordPress plugin).pdf`.

Asked whether Aaron may create a separate Bridge application and API key **on each site owner's behalf**, because the
separate application is the main setup barrier compared with scraping tools. Proposed safeguards: each owner accepts
Zillow's terms during setup; each site shows only the owner's or affiliated professionals' reviews (§31.1a); each
site calls Bridge directly with its own credentials, limits, and revocation. Only the auto-acknowledgement so far.

Context since sending: the 2026-10-26 test registration showed self-serve access takes minutes and is auto-approved,
so the "barrier" is smaller than the email suggests. The question still matters, because §5.1 bars accessing the
Services "on behalf of any other person" and §25 bars transferring credentials, and auto-approval doesn't settle
that. A yes would allow done-for-you setup (by us or by a client's web designer); see open question 7.

### 2026-02-16 — Bridge support: Zillow logo size (and implicit OK for the plugin)

Ticket 14423433, "Request to Resize or Convert Zillow Logo for Plugin Attribution". Full thread:
`.private/emails/Request to Resize or Convert Zillow Logo for Plugin Attribution.pdf`.

- **2025-06-17, Aaron:** "I maintain an open-source WordPress plugin called Easy Zillow Reviews that displays reviews
  via your official API. It complies with your Terms, including attribution." Asked whether the Media Room PNG
  (3124×656) could be converted to SVG or resized proportionally (e.g., 150px wide) and bundled with the plugin.
- **2025-06-17:** auto-acknowledgement. **2025-07-01:** "We are reviewing your request."
- **2026-02-16, Bridge Interactive Support (support manager):**

  > I was able to confirm; our guidelines require that the Zillow House logo must be at least 58px tall and the
  > Primary logo (house + name) must be >24px tall. If you can meet those requirements, you should be fine.

Takeaways:
- Resizing is fine as long as the logo stays above those minimums. The plugin uses the primary logo SVG at 115x25.
- Bridge knew it was an open-source WordPress plugin and gave display guidance without objecting. That's not formal
  written approval, but no pushback either.
- **Bridge support is slow:** 8 months for this answer. Send the open questions below well before the release that
  depends on them.

### Undated — Bridge support: star graphics / compliance

On drawing the numeric rating as plain stars (the API returns no star images): a reasonable best effort on
compliance "should be fine." _(Add the date and full email to `.private/emails/` if you find it.)_

### Undated — general stance

On past questions Bridge has leaned toward the "reasonable reading" of the terms. Caching has **not** been asked
about specifically.

## Open questions (not yet asked)

Send together to api@bridgeinteractive.com; log the answers above. Alternative channel, untried: Bridge dashboard
→ API Access → Messages → "Send a message to Zillow Agent Reviews" (goes to the dataset's provider).

**Caching** (details in [zillow-terms-notes.md](zillow-terms-notes.md#caching-literal-vs-reasonable-reading)):
1. May a site's page cache or CDN hold pages that display reviews? For how long?
2. May the plugin keep a short-lived server-side cache (e.g., 15–60 minutes) to stay within the rate limit?
3. If yes, what's the maximum duration?

**Onboarding** (see [bridge-onboarding.md](bridge-onboarding.md)):
4. ~~What does the Reviews application ask?~~ Answered by the test registration (bridge-onboarding.md).
5. ~~How is the applicant notified of approval?~~ Auto-approved instantly; shown only in the dashboard (no email).
6. ~~Which token should the plugin use?~~ The **Server Token**; confirmed with "Test connection" 2026-10-26 (the
   Browser Token also works while Referrer Domains is empty).
7. §5.1 bars accessing the API "on behalf of any other person." May we, or a client's web designer, register and
   hold the account for a site owner? **Asked 2026-10-22 (ticket 15282909), pending.** If no: may a designer paste
   the agent's own token into the agent's site?
8. ~~How does a user list several sites or change one?~~ Application Profile → Website URL is editable, and
   "Create New Application" gives each site its own application (dashboard, 2026-10-26).
9. ~~Is "Website" the right Product Type?~~ Accepted by auto-approval in the test.

**Terms interpretation** (see [zillow-terms-notes.md](zillow-terms-notes.md)):
10. §25 (US servers only): does this apply to an agent's site hosted outside the US, or to CDN edge caches?
