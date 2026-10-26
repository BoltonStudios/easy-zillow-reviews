# Bridge signup and Zillow Reviews access: what users go through

Goal: know every screen and question a plugin user faces between "install" and "working token", so the setup
guide, README, and error messages can match it.

**Status:** walked through end to end on 2026-10-26 with a real registration (screenshots in
`.private/screenshots/bridge-sign-up-test/`, 7 screens; the earlier blank form is in `bridge-signup/`). The request
was **approved automatically, instantly**, and the token is under **API Access → Server Token**. Screenshots aren't
committed (they show personal details); this file describes them in words.

## Known from public sources

From Bridge's [Zillow Group Data FAQ](https://www.bridgeinteractive.com/developers/zillow-group-data/) (checked
2026-10-26):

- Registration is open to the public.
- Access is an application: a person reviews the use case against the terms and "will be in contact". **Not what
  happened for Zillow Agent Reviews:** it was approved automatically (see "Test registration" below).
- Quota stated as "a maximum of 1,000 calls/day per data set". That's the Public Records limit; Reviews is
  5,000 calls/hour (§31.3).
- `bridgedataoutput.com/zgdata` and the Bridge docs are JavaScript apps; they can't be read with a plain fetch.
  Use a real browser.

## Known from the terms

See [zillow-terms-notes.md](zillow-terms-notes.md) for the full reading.

- **Account, then application** (§3); Zillow approves or denies at its discretion. In practice it's one form, and Reviews was approved automatically.
- **Applicant confirms** (§3): 18+, accurate information, never denied or revoked before, and authority to bind
  their organization if applying for one.
- **The application names the website(s)** (§31.1b), and reviews may appear only there. Tell users to list every
  domain they'll use, and to update the application when they move or add a site.
- **Own or affiliated reviews only** (§31.1a): the applicant shows their own reviews or those of a professional
  they're affiliated with (e.g., a team lead or brokerage).
- **No applying for someone else** (§5.1): web designers shouldn't apply for a client; the agent (or their
  brokerage) should hold the account. Whether a designer may paste the client's token is open (support log Q7).
- **US hosting** (§25) and **possible future fees** (§8): consider mentioning both, pending Bridge's answer on §25.

## The registration form (seen 2026-10-26)

`bridgedataoutput.com/zgdata` opens **"Bridge Registration: Request Zillow Group Data Access"**. For a new user
it's **one two-page form that creates the account and submits the dataset request together**. There's no separate
"request access" step.

Intro text: "Please complete the form below to register on the Bridge platform and request access to Zillow Group
data. To help determine if you will be eligible for data access, please review the Terms of Use." Then:
**"Please note that access requests may take 10+ days to process."**

**Page 1** (`…Page-1.png`): First Name, Last Name, Job Title, Email, Create Password, Re-enter Password → **Next**.

**Page 2** (`…Page-2.png`):

| Field | Form's hint | Recommend for an agent |
|---|---|---|
| Company / Institution | | Brokerage or team name |
| Support Email | Pre-filled from page 1 | Leave as is |
| Website | (one field only) | **The site where the reviews will appear**, e.g. `https://janesmithhomes.com`. This is the website "indicated in your application" (§31.1b). Not a developer's or brokerage's site unless that's where the reviews go |
| Phone, Address Line 1–2, City, State / Province (dropdown), Zip / Postal Code | | Business contact details |
| Product Name | "The product or project this API will be used for" | The business name, e.g. "Jane Smith Homes" (or "Jane Smith Homes website") |
| Product Type | "E.g. Research, IDX, VOW, Back office software" | "Website". Details go in Usage Description |
| Usage Description (Optional) | "Additional information to help data providers understand your use-case" | Fill it in anyway; see the template below |
| "What datasets would you like to request access to?" | Checkboxes: **Economic Data**, **Zillow Agent Reviews** | **Zillow Agent Reviews** only |
| "I agree to the Zillow Data Terms of Use" | Checkbox, links to the terms | Required |

Then **Back** / **Register Account**. Only Usage Description is marked optional, so assume the rest are required
(not tested).

**Usage Description template** (optional; approval is automatic, so this is a record of intended use rather than a
pitch. A candidate for a "copy this" box in the setup guide):

> I'm a real estate agent with [Brokerage]. I'll display my own Zillow reviews (Zillow screenname: [screenname]) on
> my website, [https://example.com], using the Easy Zillow Reviews WordPress plugin. Reviews are requested live from
> the Reviews API when a page loads, aren't stored, and are shown with the "Reviews on Zillow" label and a link to my
> Zillow profile.

For a team lead or broker, say whose reviews (§31.1a: "a real estate professional you are affiliated with").

## Test registration, 2026-10-26 (`bridge-sign-up-test/`)

Aaron registered as a web developer (Company "Bolton Studios", Website `https://boltonstudios.com/`) with
Product Name "Paul Stalker Real Estate", Product Type "Website", Usage Description "Display reviews for Paul Stalker,
a realtor serving the Baltimore, MD and Tampa, FL metro areas.", and only **Zillow Agent Reviews** ticked.

| # | Screen | What happens |
|---|---|---|
| 1–2 | Registration form (`Page-1`, `Page-2`) | As described above. **Register Account** turns green once required fields are filled |
| 3 | "Registration Successful" (`Page-3`) | "Please check your inbox (and your spam folder) and click on the link we've sent to validate your email address." |
| 4 | Verification email (`Page-4`) | From `noreply@sg.maildelivery.bridgeinteractive.com`, subject **"Bridge E-mail Verification"**, arrived within a minute. "Verify E-mail" link to `bridgedataoutput.com/verify?code=…` |
| 5 | "YOUR E-MAIL HAS BEEN VERIFIED" (`Page-5`) | Blue **Login** button |
| 6 | Sign-in page (`Page-6`) | `bridgedataoutput.com/login`: Email Address, Password, "Stay signed in", Reset Password, Platform Documentation |
| 7 | Dashboard (`Page-7`) | Top menu: **Data Access, API Access, API Usage, Agreements**. Account menu shows the **Product Name** ("Paul Stalker Real Estate") next to the user's name. "Application Overview" lists the product with a green dot by **Zillow Agent Reviews**. "Recent Activity": "1:15:02pm, October 26th 2026 — Data Access Approved — Paul Stalker Real Estate **was automatically approved** for Zillow Agent Reviews" (same minute as the verification email) |
| 8 | **API Access** (`Page-8`) | Heading "API Access for [Product Name]". Left menu: **API Access Tokens**, Application Settings, Application Profile, Messages. The tokens panel lists **Client ID** (shown), **Client Secret**, **Server Token**, and **Browser Token** (each behind a **Show Token** button), then **Referrer Domains** and **IP Allowlist** (both "None specified") |
| 9 | **Agreements** (`Page-9`) | "Existing Agreements: Agreements you have completed or are waiting on your approval." Empty: "There are no current agreements." A banner about merging Bridge Agreement Management accounts. This is Bridge's separate data-license (e.g., MLS) agreement tool; the Zillow terms were accepted by the registration checkbox and don't appear here. **Nothing for plugin users to do** |
| 10 | **Data Access → Approved Datasets → MLS Datasets** (`Bridge-Data-Access-Tab_Page-1`) | "There are currently no approved MLS datasets on this application." Irrelevant to the plugin |
| 11 | **Data Access → Approved Datasets → Zillow Group Datasets** (`…Data-Access-Tab_Page-2`) | "Data access approved by Zillow Group": **Zillow Agent Reviews** (resource `reviews`), with empty Feed Type / Replication / Expiry columns. Where to confirm approval |
| 12 | **Data Access → Request Data Access → Zillow Group Datasets** (`…Data-Access-Tab_Page-3`) | Lists only **ZG Economic Data** (Available, Apply). Zillow Agent Reviews isn't offered, presumably because this application already has it. Sidebar: "please allow at least 10+ business days" |
| 13 | **Data Access → Virtual Datasets** (`…Data-Access-Tab_Page-4`) | For combining MLS datasets. Irrelevant |
| 14 | **API Access → Application Settings**, with the account menu open (`Bridge-API-Access-Tab_Page-1`) | Fields: **HTTP Referrer Domains** (comma separated, e.g. `https://example.com, http://localhost`), **IP Address Allowlist** (comma separated), **OAuth Redirect URL**; "Save application settings". Below: **Reset API Access Tokens → Regenerate Tokens** ("Regenerate this application's Client Secret and Tokens"). The account menu lists the application ("Active") and **Create New Application** |
| 15 | **API Access → Application Profile** (`…API-Access-Tab_Page-2`) | Product Name ("This application name will be used by data providers"), Product Type, Usage Description (all pre-filled from registration), **Website URL** ("Your application's website URL"): **empty**. Then Dashboard Icon, Application Screenshots, and four blank questions: scope of data; how the application will use the data; "Will any data be stored on servers other than Bridge? If yes, please specify"; intended target market. "Save application profile" |
| 16 | **API Access → Messages** (`…API-Access-Tab_Page-3`) | "Send a message to **Zillow Agent Reviews**": a message box to the data provider for this dataset |

What this tells us:
- **No wait.** The form's "10+ days" warning is generic; Zillow Agent Reviews was auto-approved immediately.
  One test, but a strong sign. Users should expect to finish in one sitting.
- **Email verification is required** before logging in; the email may land in spam.
- **The Product Name becomes the application's name** in the dashboard. The account menu suggests one account can
  hold several applications (unconfirmed).
- **Auto-approval means no one reviewed the use case,** so the terms still decide what's allowed: §5.1 (no access
  for someone else) and §31.1(b) (only the websites in the application). In this test the Website field held the
  developer's site, not the agent's. Our guidance should say to enter **the site where the reviews will appear**.
- **No approval email is sent** (confirmed 2026-10-26). Approval shows only in the dashboard's Recent Activity.
- **Use the Server Token.** Tested with "Test connection" on bstest.local, 2026-10-26:

  | Pasted | Result |
  |---|---|
  | Server Token | "Connected! Found Paul Stalker on Zillow with 79 reviews." |
  | Browser Token | Also connected (no Referrer Domains set) |
  | Client ID | Rejected: `AuthenticationError: Invalid access_token format` |
  | Client Secret | Rejected: `AuthenticationError: Invalid access_token format` |

  The Browser Token works today only because Referrer Domains is empty. It's meant for requests from a visitor's
  browser, so if the user later restricts it to their domain, server-side requests would likely start failing.
  The Server Token is the one built for the plugin's PHP requests. The guide should name **Server Token**
  explicitly; four similar-looking credentials make this the step users are most likely to get wrong.
- **IP Allowlist:** if a user fills it in, requests from any other IP are presumably refused. Hosts can change
  server IPs, so the guide should say to leave it empty (or include the host's outgoing IP).
- **Referrer Domains** apply to the Browser Token. Relevant only if reviews are ever fetched in the browser with
  that token; the 1.8.0 plan keeps the token on the server, which also keeps one quota per site.

- **The application's Website URL starts empty** (Application Profile). The registration form's Website didn't
  carry over. Not the user's problem: they indicated the website in the application they submitted, which is what
  §31.1(b) asks. Filling Application Profile → Website URL is **optional** tidiness, not a setup step. What matters
  is that the registration Website is the site showing the reviews. Note that a working token proves nothing here:
  Bridge doesn't enforce the website at the API level (Referrer Domains is empty).
- **Several sites:** the account menu's **Create New Application** gives each site its own application, tokens,
  and rate limit. Its form (`Bridge-Register-New-Application.png`, "Set up new API Application") asks only for
  Product name ("E.g. Public website"), Product type ("E.g. IDX, VOW, Back Office, Research"), and Usage description
  ("E.g. This product will replicate the dataset to run data analysis."), then **Register New Application**. No
  website or dataset fields; the dataset is presumably requested afterward under Data Access → Request Data Access.
- **Leaked or shared token:** Application Settings → **Regenerate Tokens**, then paste the new Server Token into
  the plugin. Worth one line in the FAQ.
- **Existing Bridge accounts:** Create New Application, then Data Access → Request Data Access → Zillow Group
  Datasets → Apply for Zillow Agent Reviews. Unverified past the new-application form; Request Data Access didn't
  list Reviews for the test application, which already had it.
- **Messages** (API Access → Messages) reaches the Zillow Agent Reviews data provider directly. A possible faster
  channel than api@bridgeinteractive.com for terms questions; untried.

### Still unknown

| # | Step | Notes |
|---|---|---|
| 1 | After **Register New Application**: does Request Data Access list Zillow Agent Reviews, and is it auto-approved? | Path for existing accounts and second sites. Edge case; doesn't block the guide |

## What the plugin tells users vs. the form

Before 1.8.1, the setup guide said "create an account" and "request access" as separate steps, "Approval can take
a few business days", and "copy your API access token", with nothing about email verification or the form's
fields. 1.8.1 (implemented 2026-10-26, unreleased) replaced that with:

| Claim (1.8.1) | Where | Status |
|---|---|---|
| Fill out one form at `/zgdata`; Website = the site where reviews appear; Product Name = business name; Product Type = Website; check Zillow Agent Reviews | Setup guide step 1 (shows `home_url()`), README FAQ "Where do I get a Bridge token?" | Verified (test registration) |
| "Usually approved right away" despite the form's "10+ days" | Setup guide, README FAQ | Verified once (auto-approved) |
| Verify email (check spam), then log in | Setup guide step 2, README FAQ | Verified |
| API Access › Show Token › **Server Token**; not Client ID/Secret/Browser Token | Setup guide step 3, token field help, README FAQs | Verified with Test connection |
| Screenname, or paste the profile address | Setup guide step 4, screenname help, README FAQ | Verified (`normalize_screenname()`) |
| Existing account: Log in → Create New Application → request Zillow Agent Reviews under Data Access | Setup guide footer, README "more than one website" | **Unverified** past the new-application form |
| Leaked token → Application Settings › Regenerate Tokens | README FAQ | Seen in dashboard; not exercised |
| About 5 minutes | Guide heading, dashboard notice, README | Matches the test |

## Error responses to capture

So `test_connection()` can tell users what's wrong. Record HTTP code and response body (token removed) in
`.private/api-samples/`.

- [ ] Garbage token
- [ ] Valid token, dataset request still pending (unlikely now that approval is automatic)
- [ ] Valid token, approved, unknown screenname (currently: 200 with no reviewee)
- [x] Browser Token: **works** (with no Referrer Domains set)
- [x] Client ID or Client Secret: `AuthenticationError: Invalid access_token format` (2026-10-26)
- [ ] Rate limited (429?)

**Improve `test_connection()`:** for both Client ID and Client Secret the message currently says "make sure ... Bridge
has approved your access", which sends users looking for an approval problem that doesn't exist. When the response
contains `Invalid access_token format`, say instead: "That isn't your Server Token. In Bridge, open API Access and
click Show Token next to **Server Token**." Keep the current message for other 401/403 errors.

## Then update

Setup guide and token field help (`admin/class-easy-zillow-reviews-admin.php`), setup notice (`setup_notice()`),
`test_connection()` messages (`includes/class-easy-zillow-reviews-professional.php`), README "How it works",
Installation, and FAQ.
