# Bridge Reviews API notes

Moved from ROADMAP.md on 2026-10-26. Quota planning lives in ROADMAP.md ("API quota notes").

- Reviewee lookup: `GET https://api.bridgedataoutput.com/api/v2/reviews/reviewee?RevieweeScreenName=…` → `AccountIdReviewee`, `RevieweeFullName`, `RevieweeProfileURL`, `ReviewCount`, `AverageReviewRating`, `ReviewRequestURL`, `RevieweeTitle`, `RevieweeBusinessName`.
- Reviews: `GET …/api/v2/OData/reviews/Review?$filter=AccountIdReviewee eq 'ID'&$orderby=ReviewDate desc&$top=N`. Default page is 10 in no particular order, so always send `$orderby` and `$top`.
- Team reviews: `$filter=AccountIdReviewee eq 'ID' or TeamLeadAccountId eq 'ID'`; batch name/profile lookup via `…/OData/reviews/Reviewee?$filter=AccountIdReviewee eq 'a' or …`.
- Auth: `Authorization: Bearer <token>` header works (keeps the token out of URLs).
- Data quirks: some records have shifted columns (service description in `FreeFormLocation`, year in `ServiceProviderDesc`); `FreeFormLocation` includes the street address (never display it).
- Public test screennames: `missypittman` (56 reviews), `jeffreysellyohome` (team lead with team reviews).
