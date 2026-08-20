#!/usr/bin/env bash
#
# Package the site for handover: database dump + everything under wp-content that
# this project owns. Run it from anywhere inside the WordPress install.
#
#   ./export_site.sh [output-dir]
#
# Credentials and paths are read from wp-config.php, so it works on any machine.
# Run it inside the container/host that can reach the database.

set -euo pipefail

here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# walk up to the WordPress root
root="$here"
while [ "$root" != "/" ] && [ ! -f "$root/wp-config.php" ]; do
	root="$(dirname "$root")"
done

if [ ! -f "$root/wp-config.php" ]; then
	echo "wp-config.php not found above $here" >&2
	exit 1
fi

cfg() {
	php -r '$s=file_get_contents($argv[1]); if(preg_match("/define\(\s*[\x27\"]".$argv[2]."[\x27\"]\s*,\s*[\x27\"]([^\x27\"]*)[\x27\"]/",$s,$m)) echo $m[1];' "$root/wp-config.php" "$1"
}

DB_NAME="$(cfg DB_NAME)"
DB_USER="$(cfg DB_USER)"
DB_PASS="$(cfg DB_PASSWORD)"
DB_HOST="$(cfg DB_HOST)"

out="${1:-$root/../miraex-handover}"
stamp="$(date +%Y%m%d-%H%M)"
mkdir -p "$out"

echo "WordPress root : $root"
echo "database       : $DB_NAME @ $DB_HOST"
echo "output         : $out"
echo

# ---- 1. database ---------------------------------------------------------
# --single-transaction keeps it consistent without locking the site.
# Dumped to a temporary file and only moved into place once it looks complete.
# Writing straight to the output path leaves a 0-byte .sql behind when mysqldump is
# missing or the credentials are wrong — a file that looks like a dump, and wipes the
# site of anyone who imports it.
tmp_sql="$(mktemp)"
trap 'rm -f "$tmp_sql"' EXIT

if ! command -v mysqldump >/dev/null 2>&1; then
	echo "mysqldump not found. Run this where the database is reachable — in the" >&2
	echo "laradock setup that is the workspace container, not the host." >&2
	exit 1
fi

mysqldump --single-transaction --default-character-set=utf8mb4 \
	-h "${DB_HOST%%:*}" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" > "$tmp_sql"

# mysqldump writes this as its last line; without it the dump was cut short.
if ! tail -5 "$tmp_sql" | grep -q "Dump completed"; then
	echo "The dump is incomplete — refusing to hand over a truncated database." >&2
	exit 1
fi

mv "$tmp_sql" "$out/miraex-db-$stamp.sql"
trap - EXIT
echo "database  -> miraex-db-$stamp.sql   ($(du -h "$out/miraex-db-$stamp.sql" | cut -f1))"

# ---- 2. everything under wp-content the site needs ----------------------
# The parent betheme is included. It is a paid theme, but this is the same site
# moving to its own server under the same licence — leaving it out means the
# receiving side untars the archive and gets a blank page, because nothing renders
# without the parent. Keep the archive private; it has no place in a public
# repository, which is why git ignores the folder.
# tar exits 1 on "file changed as we read it", which a bind mount triggers just from
# timestamp jitter — that is a warning, not a failure. Only 2+ is fatal.
# uploads/rank-math holds generated sitemap XML: shipping it would serve a stale
# sitemap on the target until something invalidates it.
tar --warning=no-file-changed -czf "$out/miraex-content-$stamp.tar.gz" \
	--exclude='wp-content/uploads/rank-math' \
	--exclude='wp-content/uploads/wpcf7_uploads' \
	-C "$root" \
	wp-content/themes/betheme \
	wp-content/themes/betheme-child \
	wp-content/plugins \
	wp-content/uploads || [ $? -le 1 ]
echo "content   -> miraex-content-$stamp.tar.gz  ($(du -h "$out/miraex-content-$stamp.tar.gz" | cut -f1))"

# ---- 3. the hand-written code on its own --------------------------------
# The same files are already inside the content archive under
# wp-content/themes/betheme-child. This copy exists so the code can be read and
# reviewed without unpacking 28 MB or cloning anything, and so the file listing makes
# it obvious that the code is part of the handover.
#
# git archive rather than a plain zip, so the contents are exactly what the repository
# tracks: no wp-config.php, no uploads, no paid parent theme, no database dumps.
if command -v git >/dev/null 2>&1 && git -C "$root" rev-parse --git-dir >/dev/null 2>&1; then
	git -C "$root" archive --format=zip -o "$out/miraex-code-$stamp.zip" HEAD
	echo "code      -> miraex-code-$stamp.zip      ($(du -h "$out/miraex-code-$stamp.zip" | cut -f1), commit $(git -C "$root" rev-parse --short HEAD))"
else
	echo "code      -> skipped (no git here; the code is inside the content archive)"
fi

# ---- 3. the instructions, readable without unpacking anything -----------
cp "$here/../DEPLOY.md" "$out/DEPLOY.md"
echo "guide     -> DEPLOY.md"

# ---- 4. what the receiving side has to know -----------------------------
cat > "$out/README-first.txt" <<EOF
Miraex website handover — exported $stamp

Read DEPLOY.md in this folder first. Sections 1-4 are the ordered deploy path, and it
opens with four failures that produce no error message — three of them look like
success, so that is where to start if anything behaves oddly.

FILES

  miraex-content-$stamp.tar.gz   Everything under wp-content the site needs:
                                 themes/betheme (paid parent, v28.5.7),
                                 themes/betheme-child (the code), plugins, uploads.
                                 THIS is what you untar to deploy.

  miraex-db-$stamp.sql           The database: pages, templates, nav menu, SEO
                                 metadata, plugin settings, media records.

  miraex-code-$stamp.zip         The hand-written code on its own, for reading and
                                 review. Already present inside the content archive —
                                 there is nothing extra to install from it.

  DEPLOY.md                      The runbook.

NOT INCLUDED

  WordPress core. That is all — both themes and both plugins are in the archive.

THE TWO THAT MATTER MOST

  1. Do NOT rewrite the domain. It is not changing: the database already holds
     https://miraex.com. If it ever changes, never with sed — the page builder data
     is PHP-serialized with length-prefixed strings, and a text replace leaves pages
     rendering completely blank with nothing in any log.

  2. /privacy/ and /terms-of-service/ are DRAFTS pending legal review. Every open
     point is marked [TO CONFIRM: ...] in the visible page text. Not for public.

Keep this bundle private: it carries a paid theme, and the database dump carries user
emails and password hashes.
EOF

echo
echo "done. Hand over the contents of $out"
