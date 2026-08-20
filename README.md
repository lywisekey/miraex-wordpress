# Miraex website — Coolify deployment

Deploys with no shell access: Coolify pulls this repository, builds the image, and starts.
The database imports itself. Nothing is copied by hand.

## Deploying

1. Coolify → **New Resource → Docker Compose** → this repository, branch `coolify`.
2. Set the **domain** on the `wordpress` service to `miraex.toolgo.app`.
   Point the DNS record at the Coolify host **first**, with Cloudflare set to
   *DNS only* (grey cloud) until the certificate is issued — behind an orange cloud
   Let's Encrypt cannot complete its challenge and you get a 525.
3. Add four environment variables (Coolify can generate the passwords):

   ```
   MYSQL_DATABASE=miraex
   MYSQL_USER=miraex
   MYSQL_PASSWORD=...
   MYSQL_ROOT_PASSWORD=...
   ```

4. Deploy. First start takes a few minutes: MariaDB initialises and imports the dump
   while WordPress waits on its health check.

That is the whole procedure. No import step, no file copying, no terminal.

## What is where, and why

**Themes and plugins are baked into the image.** They are part of the release: to change
one you change this repository and redeploy. A redeploy therefore always lands on a known
state instead of whatever was left behind.

**Media lives on a volume** (`miraex-uploads`), because the client adds more of it through
wp-admin. A fresh volume would hide the media that ships with the site, so the shipped copy
travels in the image at `/usr/src/miraex-uploads` and the entrypoint seeds the volume once,
only when it is empty. Verified: a file created through the running site survives
`--force-recreate --build`.

**The database imports itself.** `database.sql.gz` is mounted into
`/docker-entrypoint-initdb.d/`, which MariaDB runs the first time it initialises an empty
data directory — and only then. Redeploying does not re-import and cannot overwrite live
data.

**`WORDPRESS_CONFIG_EXTRA` carries the wp-config settings.** The WordPress image rewrites
`wp-config.php` on every start, so editing that file directly does not survive a restart.
Two of the settings are not optional behind Coolify's proxy: without the
`HTTP_X_FORWARDED_PROTO` line every page redirects in a loop, and without
`MIRAEX_BEHIND_PROXY` the contact form's rate limit treats the whole internet as one
visitor.

## Notes

- The dump already points at `https://miraex.toolgo.app`. Do not search-and-replace it;
  the page data is PHP-serialized with length-prefixed strings. To move it again, use
  `wp-content/themes/betheme-child/docs/build/search_replace.php`.
- Its collation was converted to `utf8mb4_unicode_ci`, because MariaDB does not have the
  `utf8mb4_unicode_520_ci` that MySQL 8 dumps with.
- **MariaDB 11 has no `mysql` command** — the client is `mariadb`. Worth knowing if you
  ever do open a shell.
- Full runbook: `wp-content/themes/betheme-child/docs/DEPLOY.md` inside the image.
- `/privacy/` and `/terms-of-service/` are drafts with 14 `[TO CONFIRM]` markers in the
  visible text. Fine for a preview domain, not for a public launch.
