# Miraex website — themes, plugins and media baked into the image.
#
# Built this way because the deploy has no shell access: Coolify pulls this repository,
# builds, and starts. Nothing is copied by hand and a redeploy always lands on the same
# known state.
FROM wordpress:php8.3-apache

# Themes and plugins live in the image. They are part of the release, not runtime data —
# to change them you change this repository and redeploy, which is the point.
COPY wp-content/ /var/www/html/wp-content/

# Media is different: the client uploads more of it through wp-admin, so it belongs on a
# volume. The volume starts empty, which would hide the media that ships with the site —
# so the shipped copy is kept aside here and the entrypoint seeds the volume once.
COPY uploads-seed/ /usr/src/miraex-uploads/

COPY docker-entrypoint-miraex.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint-miraex.sh \
 && chown -R www-data:www-data /var/www/html/wp-content /usr/src/miraex-uploads

ENTRYPOINT ["docker-entrypoint-miraex.sh"]
CMD ["apache2-foreground"]
