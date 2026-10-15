# Search readiness and ongoing SEO

## What the application does

- Serves public page titles, descriptions, canonical URLs, reciprocal LT/EN hreflang links and social preview metadata in the initial HTML.
- Serves readable service, project-story and full legal content before JavaScript. This is the same response for visitors and crawlers, not bot-specific cloaking. Vue replaces the initial content when the interactive application mounts.
- Exports shared service, FAQ and policy content with `npm run build`. The explicit exporter is part of the build command because this project intentionally has `ignore-scripts=true` for dependency-install safety. Commit `resources/data/seo-content.json` with source changes. Verify freshness with `node scripts/export-seo-content.mjs --check`.
- Home copy and factual provider introduction come from `resources/js/data/siteContent.js`. Public starting prices come from `resources/js/data/pricing.js`; both the home and service pages export the same prices, scopes and separate-cost note to initial HTML. Keep any manually overridden homepage SEO fields consistent with the visible offer.
- Each existing service page has its own LT/EN buyer guide and native, keyboard-accessible FAQ in `resources/js/data/serviceGuides.js`, plus localized links to complementary services and completed work. These are user-facing explanations, not hidden keyword text or duplicate city landing pages.
- The immersive project viewer includes a visible full-case-study reader. Actual project prose remains in the rendered DOM and can be read without slide-by-slide navigation; no additional project URLs or crawler-only text are created.
- Includes organization, website, page, service, project and breadcrumb structured data where relevant. There are no invented reviews or ratings. Structured data does not guarantee a rich result.
- Publishes `/sitemap.xml` with both languages, project update times and unique uploaded gallery images. It does not fabricate daily modification times, priority scores or unsupported result metrics.
- Sends `noindex` for private/authentication pages and non-production environments. `SEO_INDEXABLE` can explicitly override the environment default; use `false` for staging. Production needs `APP_ENV=production`, `APP_URL=https://devsprendimai.lt` and must not override `SEO_INDEXABLE=false`.
- Client metadata respects the server's `seoIndexable` flag, including after Inertia navigation. Organization and website structured data are defined once in the persistent global graph rather than repeated with conflicting homepage attributes.
- Shows a technical checklist in the dashboard, not a Google ranking score. Search Console HTML-tag or exact verification-file presence is informational: DNS verification may already exist, and a local file or tag alone cannot prove public availability or that Google accepted it.

## Verify after deployment

Copy `public/robots.txt` to `public_html/robots.txt` on the current shared-host layout, together with the normal root build assets and manifest deployment. Preserve uploads, `.env` and the hosting front controller.

```powershell
$env:SEO_SMOKE_URL='https://devsprendimai.lt'
node tests/browser/seo.smoke.cjs
```

This is read-only and does not execute analytics. It checks every sitemap page's HTTP status, initial text, canonical, language alternates, indexability and page JSON-LD, plus robots, login exclusion and a real 404. Also inspect rendered pages on desktop/mobile: a technical check cannot judge relevance, design or real visitor experience.

`tests/browser/search-content.smoke.cjs` checks shared initial/rendered copy, pricing, buyer guidance and the project reader with Playwright. Set `SEO_UI_SMOKE_URL` explicitly. All third-party requests and non-GET/HEAD requests are blocked; it does not submit enquiries or send analytics. `SEO_UI_SCREENSHOT_DIR` optionally saves screenshots for visual review.

## Account-side work still required

1. Open [Google Search Console](https://search.google.com/search-console), add the actual domain and verify ownership. Domain verification uses the exact DNS TXT value issued by Google. Alternatively, a URL-prefix property can use its HTML token in Dashboard → SEO settings. Never invent a verification token.
   The owner supplied `public/googlec4c5c24cccbf3309.html` for the URL-prefix property. On this shared-host layout, also copy it to the root `public_html/googlec4c5c24cccbf3309.html`, preserving any existing verification files. Its public response must be HTTP 200 with the exact Google verification line. Do not remove it after verification; serving the file alone does not prove the owner clicked Verify or Google accepted ownership.
2. Submit `https://devsprendimai.lt/sitemap.xml` in the verified property's Sitemaps report.
3. Inspect the homepage, a service and a project in URL Inspection. Test live availability and request indexing when appropriate. Google's index state cannot be inferred from a local checklist or an empty `site:` search alone.
4. Use the [Rich Results Test](https://search.google.com/test/rich-results) and Search Console reports to confirm how Google actually renders the deployment.
5. Review impressions, search queries, qualified enquiries and conversions over time. Improve real service explanations and verified project examples based on this evidence; don't generate thin city/keyword pages or fake testimonials.

There is no automatic setting that guarantees indexing, top positions or revenue. Search visibility also depends on useful original content, competition, real reputation and discovery. A Google Business Profile is appropriate only if the business actually meets Google's eligibility requirements; do not create a fictitious storefront for an online-only business.

### Owner-reported account progress, 15 October 2026

The owner confirmed successful URL-prefix ownership verification and sitemap submission. The public verification file was also checked independently for HTTP 200 and the exact issued content. These facts do not establish that Google has indexed any specific page, accepted every sitemap URL or assigned a ranking. No URL Inspection request or Search Console indexing report has been read through the owner's account by the deployment workflow.

## Primary guidance

- [Google: JavaScript SEO](https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics)
- [Google: sitemap generation and submission](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
- [Google: localized versions and hreflang](https://developers.google.com/search/docs/specialty/international/localized-versions)
- [Google: structured data quality rules](https://developers.google.com/search/docs/appearance/structured-data/sd-policies)
- [Google: Organization structured data](https://developers.google.com/search/docs/appearance/structured-data/organization)
