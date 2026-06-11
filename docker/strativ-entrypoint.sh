#!/bin/bash
# Strativ container entrypoint.
#
# Runs a one-time, idempotent WordPress install + content seed in the
# background, then hands off to the stock WordPress entrypoint (which copies
# core files, writes wp-config.php from env vars, and starts Apache).
#
# Deliberately NOT using `set -e`: a single transient failure (e.g. the DB
# being briefly unavailable while MariaDB finishes starting) must not abort
# the whole init and leave WordPress half-configured.
set -uo pipefail

# All setup runs with plugins/themes skipped so a half-configured site can't
# break wp-cli's bootstrap during init.
WP="wp --allow-root --skip-plugins --skip-themes"
URL="${WORDPRESS_SITE_URL:-http://localhost}"

log() { echo "[strativ] $*"; }

# Require several consecutive successful checks so we never act during a brief
# startup blip in the database connection.
wait_for_db() {
  local ok=0 tries=0
  while [ "$tries" -lt 90 ]; do
    if $WP db check >/dev/null 2>&1; then
      ok=$((ok + 1))
      [ "$ok" -ge 3 ] && return 0
    else
      ok=0
    fi
    tries=$((tries + 1))
    sleep 2
  done
  return 1
}

strativ_init() {
  cd /var/www/html || return

  # Wait until the official entrypoint has copied core files + written config.
  until [ -f /var/www/html/wp-load.php ]; do sleep 2; done
  for _ in $(seq 1 30); do
    [ -f /var/www/html/wp-config.php ] && break
    sleep 2
  done

  log "waiting for a stable database connection..."
  if ! wait_for_db; then
    log "database did not stabilize; leaving WordPress to serve as-is."
    return
  fi
  log "database is stable."

  if $WP core is-installed >/dev/null 2>&1; then
    log "WordPress already installed."
  else
    log "First boot — installing WordPress..."
    $WP core install \
      --url="$URL" \
      --title="${WP_TITLE:-Strativ}" \
      --admin_user="${WP_ADMIN_USER:-admin}" \
      --admin_password="${WP_ADMIN_PASSWORD:-changeme-please}" \
      --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
      --skip-email || log "core install reported an error (continuing)"
  fi

  # Force the canonical URL even if the install raced a DB blip and left it
  # empty — this is what broke earlier deploys.
  $WP option update siteurl "$URL" >/dev/null 2>&1 || true
  $WP option update home "$URL" >/dev/null 2>&1 || true

  $WP rewrite structure '/%postname%/' >/dev/null 2>&1 || true
  $WP plugin install elementor advanced-custom-fields contact-form-7 --activate >/dev/null 2>&1 \
    || log "plugin install skipped (will retry on next boot)"
  $WP plugin activate strativ-core >/dev/null 2>&1 || true
  $WP theme activate strativ >/dev/null 2>&1 || log "theme activate failed"

  # Seed only when there's no project content yet (idempotent guard).
  local projects
  projects="$($WP post list --post_type=project --format=count 2>/dev/null || echo 0)"
  if [ "$projects" = "0" ]; then
    log "seeding placeholder content..."
    $WP eval-file /usr/local/share/strativ/seed-content.php || log "seed reported an error"
  else
    log "content already present ($projects projects) — skipping seed."
  fi

  $WP rewrite flush --hard >/dev/null 2>&1 || true
  log "Init done."
}

strativ_init &

exec docker-entrypoint.sh "$@"
