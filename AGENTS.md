# DevSprendimai project instructions

## Deployment preference

The owner explicitly requested on 2026-10-15 that completed, tested changes for this project be committed, pushed to GitHub and deployed to the online website, including necessary server commands. Do not leave requested implementation only in the local checkout. This permission applies to this site's requested work; it does not authorize unrelated changes, paid purchases, advertising campaigns, outbound marketing, or other accounts.

## Production environment

- Repository: `Lost2257/devsprendimai`, branch `main`.
- SSH alias: `devsprendimai-prod`. Use the configured SSH key; never print or commit private keys or credentials.
- Application root: `/home/u262161004/domains/devsprendimai.lt/public_html`.
- The shared host serves the application root. Public assets are served from root `build/`, while Vite builds into `public/build/`.
- Preserve production `.env`, uploaded media, root `index.php` and hosting configuration. Inspect before changing `.htaccess`; the reviewed template is `deployment/public-html.htaccess`.
- Use `umask 022` during checkout and asset copying; public files require readable modes. Private backups use `0700` directories and `0600` files outside `public_html`.

## Release checks

1. Inspect local/remote Git state and preserve unrelated changes. Never overwrite unexplained production edits.
2. Run relevant tests, `php artisan test`, `node --test tests/js/*.test.js`, and `npm run build`. The build exports shared content for server-rendered SEO.
3. Before database migrations or dependency updates, create a verified private backup with `php deployment/backup.php /home/u262161004/deploy-backups`.
4. Commit and push tested code and built assets. Fast-forward production from `origin main`; install the locked PHP dependencies with production flags, not `composer update`.
5. Run needed migrations with `--force`, refresh application caches, copy `public/build/assets/.` into root `build/assets/`, then copy the manifest. Do not remove old root assets while visitors may still have old pages open.
6. Verify live HTTP responses, static assets, LT/EN pages, mobile layouts and relevant workflows. Report deployment revision and any unresolved external setting. Do not send real enquiry emails in browser smoke tests.

## Privacy and evidence

Read `deployment/analytics.md` and `deployment/privacy-security.md` for consent, retention and verification. Preserve equal accept/refuse options and do not load optional analytics before consent. Never promise Google rankings, indexing, revenue or legal certification. Search Console access and Analytics account settings require their actual account access; server access is not a substitute.
