# Strativ — production image
# Bakes the custom theme + plugin onto official WordPress. Uploads and the
# database live in volumes; application code ships with the image so every
# deploy is reproducible from git.

FROM wordpress:php8.3-apache

# Tools needed for the first-boot auto-install (wp-cli + mysql client)
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl less default-mysql-client \
    && rm -rf /var/lib/apt/lists/* \
    && curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# Pretty permalinks: enable mod_rewrite and allow .htaccess overrides in the
# web root, otherwise every non-homepage URL 404s.
RUN a2enmod rewrite
COPY docker/apache-wordpress.conf     /etc/apache2/conf-enabled/z-wordpress.conf
COPY docker/wordpress.htaccess        /usr/src/wordpress/.htaccess

# Custom theme + plugin go into the pristine source tree so the official
# entrypoint copies them into /var/www/html on first run.
COPY wp-content/themes/strativ        /usr/src/wordpress/wp-content/themes/strativ
COPY wp-content/plugins/strativ-core  /usr/src/wordpress/wp-content/plugins/strativ-core

# Placeholder-content seeder (kept outside the web root) + boot script
COPY scripts/seed-content.php         /usr/local/share/strativ/seed-content.php
COPY docker/strativ-entrypoint.sh     /usr/local/bin/strativ-entrypoint.sh
RUN chmod +x /usr/local/bin/strativ-entrypoint.sh

ENTRYPOINT ["strativ-entrypoint.sh"]
CMD ["apache2-foreground"]
