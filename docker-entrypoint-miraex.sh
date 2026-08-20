#!/usr/bin/env bash
set -euo pipefail

UPLOADS=/var/www/html/wp-content/uploads

# First boot on a fresh volume: copy in the media that ships with the site. Afterwards the
# volume owns the directory, so anything uploaded later survives a redeploy and nothing
# here overwrites it.
mkdir -p "$UPLOADS"

if [ -z "$(ls -A "$UPLOADS" 2>/dev/null)" ]; then
	echo "[miraex] seeding uploads volume from the image"
	cp -a /usr/src/miraex-uploads/. "$UPLOADS/"
fi

chown -R www-data:www-data "$UPLOADS" || true

# Hand over to the stock WordPress entrypoint, which writes wp-config.php from the
# environment and then runs the command.
exec docker-entrypoint.sh "$@"
