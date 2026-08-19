# Deploying miraex.com

A runbook for standing this site up. Sections 1–4 are the ordered path — §2 is the deploy
itself, §2b covers Coolify and moving to a different domain, §3 covers AWS. Everything
after is the reasoning, which matters when something behaves oddly.

Checked against the running site on 2026-08-17. The numbers in here are measured, not
estimated.

## Read this first: four failures that produce no error

Each of these cost real time during the build. None of them shows an error message, and
three of them look like success.

| Symptom | Cause |
| --- | --- |
| Pages render **completely empty** | The domain was rewritten with `sed`. Builder data is PHP-serialized with length-prefixed strings — §2.5 |
| Form says "Message sent", **nothing in HubSpot** | The sending domain is not registered in the HubSpot portal. HubSpot answers `200 OK` either way — §4.4 |
| The **11th message of the hour** from anyone is refused | Behind a load balancer without `MIRAEX_BEHIND_PROXY`, every visitor arrives as the balancer and shares one rate-limit bucket — §3.3 |
| Sitemap **missing pages** added after deployment | Rank Math serves a cached XML file from `uploads/rank-math/` — §2.8 |

---

## 1. What you are being given

One zip: **`miraex-website.zip`** (31 MB). Unzip it and you get four things:

```
wp-content/        the whole folder the site needs — copy it into the WordPress
                   directory, replacing the one a fresh install creates.
                   Contains themes/betheme (paid parent, v28.5.7),
                   themes/betheme-child (the code), both plugins, and all media.
database.sql       import into an empty database. 20 tables.
DEPLOY.md          this file.
README-first.txt   the same thing in one screen, for whoever only wants the steps.
```

**Only WordPress core is missing.** Nothing to build, no scripts to run, no repository to
clone. Keep the files private: `wp-content` carries a licensed paid theme, and
`database.sql` carries user emails and password hashes.

What the site is made of:

| Part | Where it lives |
| --- | --- |
| 15 pages incl. the front page | database (`wp_posts` + `mfn-page-items` postmeta) |
| Header, footer, mega menu | database — `template` posts #3, #52, #78 |
| 21 reusable sections | database — `template` posts #80–#100 |
| Nav menu | database; the **Solutions** item carries `mfn_menu_item_megamenu = 78` |
| Contact form | `betheme-child/inc/hubspot-proxy.php` → HubSpot API |
| SEO metadata | `rank_math_*` postmeta + `rank_math_options_titles` |
| 23 media items + 14 client logos | `wp-content/uploads` |
| Design layer + builders | `wp-content/themes/betheme-child` |
| Plugins | Rank Math SEO, LiteSpeed Cache (see §3.4) |

---

## 2. Deploy

### 2.1 Provision

A 15-page brochure site with no login, no cart and no search. **Lightsail or a small EC2
instance is enough** — 2 vCPU / 2 GB comfortably runs it. Do not reach for autoscaling
before reading §3.5.

The load profile, measured: a first visit to the front page transfers ~3.2 MB — 39 KB of
gzipped HTML, 355 KB of JavaScript and **2.8 MB of images**. Five thousand of those at once
is ~15 GB. A traffic spike here is a **bandwidth** problem, not a CPU one, which is why
§3.4 matters more than the instance size.

### 2.2 Install WordPress core

Nothing else — both themes and both plugins are in the archive.

### 2.3 Put `wp-content` in place

Copy the `wp-content` folder from the zip into the WordPress directory, replacing the one
the fresh install created. It already contains both themes, both plugins and all media.

### 2.4 Create the database and import

```bash
mysql -u <user> -p <db> < database.sql
```

Empty database. RDS is fine — nothing depends on the database being local.

### 2.5 Configure `wp-config.php`

Database credentials, plus these. The last three are AWS-specific and explained in §3.

```php
/* TLS terminates at the load balancer: without this WordPress sees a plain request
   while siteurl says https, which gives redirect loops and mixed content.
   Must sit ABOVE the require_once of wp-settings.php. */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

/* Only if an ALB or CloudFront is in front. See §3.3 — leaving it out silently
   collapses the contact form's rate limit onto one bucket for the whole internet. */
define( 'MIRAEX_BEHIND_PROXY', true );

/* Stops WP_Filesystem falling back to FTP, which makes the builder scripts fatal
   mid-save. See §3.6. */
define( 'FS_METHOD', 'direct' );
```

**Do not rewrite the URLs.** The database stores `https://miraex.com` (no `www`, https) and
the site is going live on the same name, so the step is unnecessary — and it carries the
worst failure mode in this handover.

If a domain change is ever needed, **never use `sed`**. 17 rows of `mfn-page-items` hold
PHP-serialized arrays whose strings are length-prefixed:

```
s:66:"https://miraex.com/wp-content/uploads/2026/08/hero-photonics.jpg"
```

A text replace changes the string but not the `66`, unserialize then fails, and the page
renders **completely empty** with nothing in any log. Use a serialization-aware tool:

```bash
wp search-replace 'https://miraex.com' 'https://new-domain' --all-tables --precise
```

or Better Search Replace, or `interconnect/it` srdb.

### 2.6 Activate the theme

`betheme-child` — not `betheme`. (`stylesheet = betheme-child`, `template = betheme`.)

### 2.7 Save permalinks

Settings → Permalinks → Save. The structure is `/%category%/%postname%/`. Without this every
inner page 404s and the sitemap routes do not resolve.

### 2.8 Delete the sitemap cache

```bash
rm -rf wp-content/uploads/rank-math/
```

Rank Math writes each sitemap to a file there and serves it verbatim — the XML footer says
*"Served from cache"*. Anything added after that file was written never appears.
`docs/build/migrate_seo.php` deletes them too; its own invalidator goes through
`WP_Filesystem` and fatals on the CLI, which is what §3.6 is about.

### 2.9 Regenerate the BeTheme CSS

BeTheme → Options → *Regenerate CSS*. `uploads/betheme/css/post-*.css` is derived data keyed
by post ID; it survives the import unchanged, but regenerate anyway.

### 2.10 Register the domain in HubSpot

In the HubSpot portal, before testing the form. Until the sending domain is registered
HubSpot **accepts the submission with `200 OK` and discards it** — see §4.4.

---

## 2b. Coolify, and a different domain

Coolify runs everything in Docker behind its own reverse proxy (Traefik or Caddy), which
changes two things and makes a third — the domain change — the riskiest step in the whole
deploy.

### 2b.1 Create the service

Use Coolify's WordPress + MariaDB/MySQL template. Set the **FQDN to the new domain** in the
service settings; Coolify configures the proxy and requests the certificate. Point the DNS
record at the Coolify host first, or the certificate cannot be issued.

Give `wp-content` a **persistent volume**. Without one, every redeploy wipes the themes,
the plugins and every uploaded image.

### 2b.2 Put the files and the database in

The container starts with a stock WordPress. Replace its `wp-content` and load the dump:

```bash
# from the machine holding the unzipped package
docker cp wp-content/. <wordpress-container>:/var/www/html/wp-content/
docker exec -i <database-container> mysql -u<user> -p<pass> <db> < database.sql
```

Coolify's own terminal, or SFTP into the volume, work just as well. Afterwards fix
ownership so WordPress can write:

```bash
docker exec <wordpress-container> chown -R www-data:www-data /var/www/html/wp-content
```

### 2b.3 Change the domain — before opening the site

The database still says `https://miraex.com`, so visiting the new domain would redirect
straight back to the old site. Do the rewrite from the command line first:

```bash
docker exec <wordpress-container> php \
  wp-content/themes/betheme-child/docs/build/search_replace.php \
  https://miraex.com https://new-domain.com
```

That is a **dry run** — it prints what would change and writes nothing. Add `--apply` when
the numbers look right.

The script exists because this database cannot survive a text replace. Page content is
stored as PHP-serialized arrays whose strings carry their own byte length
(`s:66:"https://miraex.com/…"`); changing the text without the `66` makes `unserialize()`
fail and BeBuilder renders the page blank with nothing in any log. The script unserializes
each value, replaces inside it, serializes it back, and refuses to write anything that does
not read back. No WP-CLI needed — Coolify's image will not have it.

Measured on the current database: **39 rows change, 20 of them serialized.** It was tested
by rewriting to a throwaway domain and back: all 75 serialized rows still unserialized, the
front page still read as 8 sections, and the site came back byte for byte.

Two things it deliberately leaves alone:

- **`info@miraex.com`** and other addresses — it only matches the URL with its scheme, so
  the contact email survives.
- **`guid`** — WordPress documentation says never to change it, and nothing renders from it.
  `--include-guid` overrides that if you insist.

What it cannot decide for you: `/privacy/` and `/terms-of-service/` **name miraex.com in
their prose**. If the site is permanently moving, someone has to edit that text.

Afterwards: regenerate the BeTheme CSS (§2.9) and register the new domain in HubSpot
(§2.10) — the form silently discards submissions from an unregistered domain.

### 2b.4 Behind Coolify's proxy

Both settings in §2.5 are **required**, for the same reasons as on AWS:

- `HTTP_X_FORWARDED_PROTO` — Traefik and Caddy terminate TLS and speak HTTP to the
  container, so without it WordPress sees a plain request while `siteurl` says https.
- `MIRAEX_BEHIND_PROXY` — otherwise every visitor arrives as the proxy's container address,
  they share one rate-limit bucket, and the eleventh contact message of the hour is refused.

### 2b.5 Caching

The WordPress image runs Apache or nginx, so LiteSpeed Cache does not cache pages (§3.4).
Its minify and lazy-load still work. Either leave it for those, or deactivate it and put
Cloudflare in front — which is the answer the load numbers point at anyway (§2.1).

---

## 3. AWS specifics

### 3.1 Which service

Lightsail or EC2. Not Elastic Beanstalk or containers unless there is a reason — this is a
single-instance WordPress site, and §3.5 explains why more instances need extra work.

### 3.2 HTTPS behind the load balancer

Covered in §2.5. The symptom without it is a redirect loop or a padlock that will not go
green.

### 3.3 `MIRAEX_BEHIND_PROXY` — required behind an ALB or CloudFront

Without it, `$_SERVER['REMOTE_ADDR']` is the balancer for every request. Two consequences,
neither of which errors:

- The contact form's rate limit buckets by address, so **every visitor shares one bucket**.
  The eleventh submission of the hour from anybody is refused. (10/hour, 30/day —
  `MIRAEX_RATE_HOUR` in `inc/hubspot-proxy.php`.)
- HubSpot receives the balancer's address as `ipAddress`, blinding its own spam scoring.

Set it **only** when a proxy really is in front: it makes the code trust the first hop of
`X-Forwarded-For`, which a client can forge when nothing strips it.

### 3.4 Caching — LiteSpeed Cache does not cache pages here

Its page cache requires LiteSpeed or OpenLiteSpeed. On the nginx or Apache you will run on
EC2, the settings are stored and **inert** — and inert looks identical to working in the
admin. Pick one:

- **CloudFront in front, caching HTML.** The answer that matches the numbers in §2.1.
  Nothing in the page is per-visitor — no nonce, no personalised markup — so whole pages
  can be cached at the edge. Exclude `/wp-json/` and `/wp-admin/`. Keep it that way: the
  moment something visitor-specific is printed into the HTML, a cache starts serving one
  visitor's page to another.
- **Install OpenLiteSpeed** and everything already configured keeps working.
- **Swap the plugin** for Cache Enabler or WP Super Cache, or use nginx `fastcgi_cache` —
  then *deactivate* LiteSpeed Cache rather than leaving it on looking busy.

Whichever you choose, verify it: a second request to `/` should show a cache-hit header
(`x-litespeed-cache: hit`, or `x-cache: Hit from cloudfront`).

Settings are applied by `docs/build/configure_litespeed.php`, with the reasoning inline.
Combining CSS/JS, deferring JS, critical CSS and unused-CSS removal are **off on purpose** —
BeTheme loads a dozen scripts that assume their own order, and those four settings are what
break builder pages. Minify and lazy load are on and were verified: 13/13 pages load, 22/22
images render, no JS errors, and the form's inline script survives HTML minification.

### 3.5 One instance, or shared storage

BeTheme writes `uploads/betheme/css/post-*.css` (44 files) and the builders regenerate them.
Behind an autoscaling group each instance keeps its own copy and they drift. Either stay on
a single instance or put `uploads` on EFS.

### 3.6 `FS_METHOD`

Saving a post makes LiteSpeed purge, which makes Rank Math clear its sitemap cache, which
goes through `WP_Filesystem`. With no FTP credentials that lands in the FTP driver and
throws — killing a builder script **after** the post row is updated and **before** the
builder data is written. `docs/build/bootstrap.php` defines the constant for CLI runs;
`wp-config.php` covers everything else.

### 3.7 Email

EC2 blocks outbound port 25 by default. The contact form does not care — it posts to
HubSpot — but WordPress's own mail (password resets, admin notices) vanishes silently. Use
SES with an SMTP plugin if that matters.

---

## 4. Verify before DNS moves

### 4.1 Reach the new server without touching DNS

The site cannot be checked on a temporary host name: 17 rows of builder data and 102
attachment records carry `https://miraex.com` as an absolute URL, so on a preview domain the
pages render while quietly loading assets **from the old server**. Nothing looks wrong until
DNS moves.

Point the testing machine's hosts file at the new instance instead:

```
<new-server-ip>   miraex.com
```

The site serves from the new host with the URLs it already has, the public keeps seeing the
old one, and no database rewrite happens. Remove the entry afterwards.

### 4.2 Run the checks

```bash
wp-content/themes/betheme-child/docs/build/verify_layout.py https://miraex.com
```

36 assertions: every page responds, button rows measure 14px, the phone gutter is 15px, the
footer holds one row from 1440px down to 960px, the SEO tags are present and the sitemap has
15 URLs. Every one of those was a real defect during the build and **not one was visible in
a screenshot**. Needs headless Chrome; exits non-zero, so CI can run it.

### 4.3 Check by hand

- Front page: Settings → Reading must show *Home* (`page_on_front`).
- **Mega menu**: Appearance → Menus → *Solutions* must still show the mega menu template.
  This is a single postmeta row set by hand — the one thing no script recreates.
- Load `/`, `/technology/` and `/contact/` at 1440px and 390px.

### 4.4 Send one message through the form — and confirm it in HubSpot

Not just that the page says "Message sent". This is the only step that catches a
portal-side block, because **HubSpot returns `200 OK` with an `inlineMessage` body either
way** — the same response a working form gets. Look for the contact in the CRM.

The form posts from the browser to `/wp-json/miraex/v1/contact`, which validates and
forwards server-side; the portal id and form GUID never reach the page. HubSpot **ignores
field names that are not on the form, silently, with a 200**, so the names were confirmed
against the live form rather than guessed:

| Field | HubSpot property |
| --- | --- |
| First name | `firstname` |
| Last name | `lastname` |
| Work email | `email` |
| Organisation | `company` (optional) |
| How can we help? | `miraex_intent` (custom; CRM values equal the labels) |
| Message | **`comments`** — not `message` |
| Consent | `consent`, sent as `"true"` |

Spam is handled by a honeypot rather than a captcha. **It needs a meaningless name:** the
first version called the hidden field `website_url` and labelled it "Company website",
which Chrome autofill and password managers recognised and filled for real visitors, whose
messages were then dropped as bot traffic — success on screen, nothing in HubSpot. It is now
`mx_r9`, and a filled trap only counts as a bot when no key press or pointer press ever
happened on the form.

Turnstile is wired but off. Define both constants and it turns itself on:

```php
define( 'MIRAEX_TURNSTILE_SITEKEY', '...' );
define( 'MIRAEX_TURNSTILE_SECRET',  '...' );
```

### 4.5 Cut over

Change DNS, remove the hosts entry, re-run §4.2 against the live domain.

---

## 5. After go-live

- **Restrict `/wp-login.php` and `/wp-admin/`** by IP or HTTP auth. Both are open to the
  world today, and that is where brute-force traffic actually lands. It matters more than
  everything in §7.
- **Backups.** There are none. More important than a firewall: data loss is a real risk,
  being hacked is a hypothetical one.
- **Betheme purchase code.** The theme ships with the site and runs without one, but
  updates need it, and an out-of-date paid theme is a common way in.
- **`readme.html` and `license.txt` were deleted** because both name the WordPress version.
  A core update puts them back — delete them again, or block them at the server.

---

## 6. Generated vs hand-made — read before re-running anything

Page and template content is **generated**: the scripts in `betheme-child/docs/build/` write
`mfn-page-items` directly, and re-running one **overwrites that whole page**, including
edits made in BeBuilder. The run order and per-script notes are in
[`build/README.md`](build/README.md). The scripts find WordPress themselves, so they run
anywhere:

```bash
php wp-content/themes/betheme-child/docs/build/build_home.php
```

Not reproducible by re-running them:

- the mega-menu link on the *Solutions* menu item (§4.3)
- anything edited later through the builder UI

**Pick one model and tell the team.** Either edits keep going through the scripts — the
design stays consistent and UI edits get overwritten — or the database becomes authoritative
and the builders are retired. Mixing the two loses work silently.

---

## 7. What the site tells visitors about itself

`betheme-child/inc/hardening.php` removes the version numbers and discovery links WordPress
prints by default: the `generator` meta tag (which named the exact version), the
RSD/manifest/shortlink/oEmbed/REST link tags, and the `X-Powered-By` header. The REST API
stays on — the contact form needs it.

Two things are deliberately *not* done:

- **`wp-content` is not renamed.** Image URLs live inside serialized builder data, so a
  rename is the same length-prefix trap as §2.5, and it fools nobody: cookie names, the
  login redirect and the markup still identify WordPress.
- **No "hide WordPress" plugin.** They add a rewrite layer over everything and break as
  often as they help.

Be clear about what this buys: the site is **not** unidentifiable. It no longer volunteers
version numbers, which is what turns a broad scan into a targeted one.

---

## 8. Known gaps

- **`/privacy/` and `/terms-of-service/` are drafts awaiting legal review.** Every open point
  is written into the visible page text as `[TO CONFIRM: …]` — 8 on the privacy page, 6 on
  the terms — so none can be missed. **They must not be public in this state:** they still
  lack the company registration number, the hosting provider, data retention periods and
  confirmation of the HubSpot transfer mechanism.
- Images are JPEG/PNG, **2.8 MB per front-page load**. WebP/AVIF is the largest performance
  win left and would cut a 15 GB burst to roughly 5 GB. LiteSpeed can do it but only through
  a QUIC.cloud account.
- The WordPress **Site Title** had been set to the hero headline, so it appeared in every
  `<title>`, the RSS feed and every WordPress email. `migrate_seo.php` sets it to *Miraex*
  with the tagline *Connecting Quantum*.
- `import_media.php` re-downloads images from the original static site. Do not rely on it
  for a redeploy — transfer `uploads` instead; that host will not stay up forever.
- No HubSpot tracking script, so submissions are not tied to a visitor journey. Adding it
  sets a tracking cookie, which brings a consent banner requirement with it — the site sets
  **no cookies at all** today.

---

## 9. Version control

The repository holds the hand-written half. `wp-config.php`, uploads, plugins, the paid
parent theme, generated caches and `*.sql` are ignored — they travel in the archive, and a
database dump has no business in a repository.

```bash
git clone git@github.com:wisekeylab/miraex-wordpress.git
```

`docs/build/export_site.sh` regenerates a handover bundle from a running install; it reads
the credentials out of `wp-config.php`, so it works unchanged against RDS. Nobody needs it
to deploy — it is for whoever hands the site over next.
