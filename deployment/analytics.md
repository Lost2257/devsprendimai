# Google Analytics 4

- Set `GOOGLE_ANALYTICS_ID=G-LYW20LSJG9` in the production `.env` and run `php artisan config:cache`.
- The public Inertia props expose the ID only in production, for signed-out visitors on public routes.
- `resources/js/lib/analytics.js` loads gtag.js only after affirmative analytics consent (basic consent mode). Ads storage, ads user data, ads personalisation and Google Signals stay disabled.
- LT/EN consent choices expire after 180 days and are stored in localStorage. Cookie settings in the footer (or in the full-screen project viewer) allow withdrawal. Withdrawal removes GA cookies and reloads to unload the Google library. Changes propagate across tabs.
- Inertia navigation sends one explicit `page_view` after updating the page title. Keep GA4 Enhanced Measurement's **Page changes based on browser history events** disabled for this web stream. Do not install a second tag for this stream.
- The initial `config` call uses `send_page_view: false`; initial consent and completed public navigation each record one view. Consecutive identical URLs are deduplicated, unrelated query parameters and fragments are excluded, and private navigation pauses collection.
- Google Analytics and the existing server-side dashboard statistics are separate data sources. Visitors refusing consent, administrators and ad blockers are not included in GA4.
- Google Analytics event retention and reporting settings belong to the Google account; the application does not change them. Browser GA cookies are limited to 180 days without renewal on each hit.

## Verification

Run `node --test tests/js/analytics.test.js` and `php artisan test --filter=AnalyticsConfigurationTest`, then `npm run build`.

In a clean browser, verify no `googletagmanager.com/gtag/js` or Google Analytics requests before consent or after refusal. Accept analytics, inspect the measurement ID and one page-view request per Inertia navigation, then withdraw consent and confirm cookies and further collection stop. Check both `/` and `/en`, and verify `/login` never loads a tag. Tag Assistant needs consent to detect the loaded Google tag.

On this shared host, copy the built `public/build/assets` to the root `build/assets`, then copy `public/build/manifest.json` to `build/manifest.json`. Preserve the hosting `.htaccess`, root `index.php` and uploaded media.

Use `umask 022` for deployment. If a private backup is created with `umask 077`, restore `022` before Git checkout and asset copying. Public build directories need mode `755` and files `644` so the host's static file server can read them.

The optional `tests/browser/analytics.smoke.cjs` runs against `GA_SMOKE_URL` with Playwright. It sends a few real test views and verifies consent gating, page counts, revocation, LT/EN mobile layout and login exclusion.
