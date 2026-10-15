# Google Analytics 4

- Set `GOOGLE_ANALYTICS_ID=G-LYW20LSJG9` in the production `.env` and run `php artisan config:cache`.
- The public Inertia props expose the ID only in production, for signed-out visitors on public routes.
- `resources/js/lib/analytics.js` loads gtag.js only after affirmative analytics consent (basic consent mode). Ads storage, ads user data, ads personalisation and Google Signals stay disabled.
- LT/EN consent choices (version 2) expire after 180 days and are stored in localStorage, with an explicit analytics-preference cookie for internal statistics. Both optional analytics systems require consent. Cookie settings in the footer (or in the full-screen project viewer) allow withdrawal. Withdrawal removes GA cookies and reloads to unload the Google library. Changes propagate across tabs.
- Inertia navigation sends one explicit `page_view` after updating the page title. Keep GA4 Enhanced Measurement's **Page changes based on browser history events** disabled for this web stream. Do not install a second tag for this stream.
- The initial `config` call uses `send_page_view: false`; initial consent and completed public navigation each record one view. Consecutive identical URLs are deduplicated, unrelated query parameters and fragments are excluded, and private navigation pauses collection.
- Google Analytics and server-side dashboard statistics are separate data sources. Both exclude visitors refusing consent and administrators; ad blockers may additionally block GA4. Initial opt-in is measured by GA, while internal counts begin on the next request carrying the granted preference.
- Google Analytics event retention and reporting settings belong to the Google account; the application does not change them. Browser GA cookies are limited to 180 days without renewal on each hit.

## Successful enquiry measurement

- The website sends Google's recommended `generate_lead` event only after a real enquiry has been saved, not on button clicks, validation errors or honeypot responses. Mail delivery is independent: a saved enquiry is still available to the owner if its notification fails.
- Both submission-time and completion-time analytics consent are required. Signed-in users, private pages and non-production requests are excluded. A one-use server response marker and client callback deduplication prevent replay; granting consent later does not backfill earlier submissions.
- The event contains only the fixed `form_id=project_enquiry`, allowlisted page `language=lt|en`, and the measurement ID. No form contents, names, email addresses, phone numbers, company names, database IDs, marker IDs or invented monetary values are sent.
- In the owner's GA4 property, mark `generate_lead` as a **key event**. This is an account setting, not completed by a deployment. Do not use generic `form_submit` or button clicks as an equivalent: they do not prove a valid enquiry was saved. Google guidance: https://support.google.com/analytics/answer/13128484 and https://developers.google.com/analytics/devguides/collection/ga4/reference/events#generate_lead
- Ad blockers and refused consent mean some enquiries will not appear in GA. The dashboard's saved enquiries remain the source for following up with customers; a lead is not a sale or guaranteed revenue.

## Required Google account setting

In **Admin → Data streams → this web stream → Enhanced measurement (gear) → Page views → Show advanced settings**, turn off **Page changes based on browser history events** and save. Keep the main Page views option enabled. The application records initial and subsequent public page views itself, using the updated Inertia page title.

`send_page_view: false` alone does not disable Enhanced Measurement's separate history listener. If left enabled, Back navigation can produce an extra view with the previous page's title/URL. See [Google's manual page-view documentation](https://developers.google.com/analytics/devguides/collection/ga4/views). This account-level setting cannot be changed by a website deployment.

## Verification

Run `node --test tests/js/analytics.test.js` and `php artisan test --filter=AnalyticsConfigurationTest`, then `npm run build`.

In a clean browser, verify no `googletagmanager.com/gtag/js` or Google Analytics requests before consent or after refusal. Accept analytics, inspect the measurement ID and one page-view request per Inertia navigation, then withdraw consent and confirm cookies and further collection stop. Check both `/` and `/en`, and verify `/login` never loads a tag. Tag Assistant needs consent to detect the loaded Google tag.

On this shared host, copy the built `public/build/assets` to the root `build/assets`, then copy `public/build/manifest.json` to `build/manifest.json`. Preserve the hosting `.htaccess`, root `index.php` and uploaded media.

Use `umask 022` for deployment. If a private backup is created with `umask 077`, restore `022` before Git checkout and asset copying. Public build directories need mode `755` and files `644` so the host's static file server can read them.

The optional `tests/browser/analytics.smoke.cjs` runs against `GA_SMOKE_URL` with Playwright. It sends a few real test views and verifies consent gating, page counts, revocation, LT/EN mobile layout and login exclusion.

For enquiry conversion checks, run `LEAD_SMOKE_URL=https://devsprendimai.lt node tests/browser/lead-conversion.smoke.cjs` with Playwright available. It intercepts all contact POSTs and Google requests: no real enquiries, notification emails or GA events are sent. It checks LT/EN desktop/mobile success, refused consent, marker deduplication, validation/honeypot exclusion, blocked analytics and history restoration. Backend persistence and one-use marker behavior are covered separately by `tests/Feature/LeadConversionTest.php`.

The smoke test completes independent privacy/layout checks before reporting a failing page-count assertion if the account's automatic history tracking is still enabled. Network checks confirm events were sent, not that Google has processed them into reports; confirm ingestion separately in the account's Realtime report after accepting analytics in a signed-out browser.
