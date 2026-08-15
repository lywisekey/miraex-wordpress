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
mysqldump --single-transaction --default-character-set=utf8mb4 \
	-h "${DB_HOST%%:*}" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" \
	> "$out/miraex-db-$stamp.sql"
echo "database  -> miraex-db-$stamp.sql   ($(du -h "$out/miraex-db-$stamp.sql" | cut -f1))"

# ---- 2. files this project owns -----------------------------------------
# The parent betheme (45M) is a paid theme: ship it only if the receiving side
# has no licence of their own. Add it to the list below if so.
# tar exits 1 on "file changed as we read it", which a bind mount triggers just from
# timestamp jitter — that is a warning, not a failure. Only 2+ is fatal.
# uploads/rank-math holds generated sitemap XML: shipping it would serve a stale
# sitemap on the target until something invalidates it.
tar --warning=no-file-changed -czf "$out/miraex-content-$stamp.tar.gz" \
	--exclude='wp-content/uploads/rank-math' \
	--exclude='wp-content/uploads/wpcf7_uploads' \
	-C "$root" \
	wp-content/themes/betheme-child \
	wp-content/plugins \
	wp-content/uploads || [ $? -le 1 ]
echo "content   -> miraex-content-$stamp.tar.gz  ($(du -h "$out/miraex-content-$stamp.tar.gz" | cut -f1))"

# ---- 3. the instructions, readable without unpacking anything -----------
cp "$here/../DEPLOY.md" "$out/DEPLOY.md"
echo "guide     -> DEPLOY.md"

# ---- 4. what the receiving side has to know -----------------------------
cat > "$out/README-first.txt" <<EOF
Miraex handover — exported $stamp

  miraex-db-$stamp.sql          full database (pages, templates, menu, CF7 form,
                                 theme options, media records)
  miraex-content-$stamp.tar.gz   wp-content/themes/betheme-child + wp-content/uploads

Not included, install separately:
  - WordPress core
  - betheme (parent theme, paid — needs its own licence/purchase code)
  - Contact Form 7 (the only plugin)

Read DEPLOY.md in this folder first — it is the same file that ships inside the
tarball at wp-content/themes/betheme-child/docs/DEPLOY.md.

Two things that will bite if skipped:
  1. Do NOT rewrite the domain with sed. The builder data is PHP-serialized with
     length-prefixed strings; use wp search-replace --precise or Better Search Replace.
  2. /privacy/ and /terms-of-service/ are DRAFTS pending legal review — every open
     point is marked [TO CONFIRM: ...] on the page itself. Do not publish as-is.
EOF

echo
echo "done. Hand over the contents of $out"
