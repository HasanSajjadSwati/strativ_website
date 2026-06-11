#!/bin/bash
# Strativ container entrypoint.
#
# Runs a one-time, idempotent WordPress install + content seed in the
# background, then hands off to the stock WordPress entrypoint (which copies
# core files, writes wp-config.php from env vars, and starts Apache).
set -euo pipefail

strativ_init() {
  cd /var/www/html

  # Wait until the official entrypoint has copied core files into place.
  until [ -f /var/www/html/wp-load.php ]; do sleep 2; done

  # DB is started before us via compose healthcheck; give wp-config a moment.
  for _ in $(seq 1 30); do
    [ -f /var/www/html/wp-config.php ] && break
    sleep 2
  done

  if wp core is-installed --allow-root >/dev/null 2>&1; then
    echo "[strativ] WordPress already installed — ensuring theme & plugins are active."
  else
    echo "[strativ] First boot — installing WordPress..."
    wp core install --allow-root \
      --url="${WORDPRESS_SITE_URL:-http://localhost}" \
      --title="${WP_TITLE:-Strativ}" \
      --admin_user="${WP_ADMIN_USER:-admin}" \
      --admin_password="${WP_ADMIN_PASSWORD:-changeme-please}" \
      --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
      --skip-email

    wp rewrite structure '/%postname%/' --allow-root
    wp plugin install elementor advanced-custom-fields contact-form-7 --activate --allow-root || true
    wp eval-file /usr/local/share/strativ/seed-content.php --allow-root || true
    echo "[strativ] Install + seed complete."
  fi

  # Always make sure our code is active and rewrites are fresh.
  wp plugin activate strativ-core --allow-root || true
  wp theme activate strativ --allow-root || true
  wp rewrite flush --hard --allow-root || true
  echo "[strativ] Init done."
}

strativ_init &

exec docker-entrypoint.sh "$@"
