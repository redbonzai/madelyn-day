#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$project_dir"

set -a
# shellcheck disable=SC1091
source "$project_dir/.env"
set +a

docker compose up -d database wordpress

for attempt in $(seq 1 30); do
  if docker compose --profile tools run --rm cli core is-installed >/dev/null 2>&1; then
    break
  fi
  if docker compose --profile tools run --rm cli core version >/dev/null 2>&1; then
    break
  fi
  if [ "$attempt" -eq 30 ]; then
    echo "WordPress did not become ready in time."
    exit 1
  fi
  sleep 2
done

if ! docker compose --profile tools run --rm cli core is-installed >/dev/null 2>&1; then
  docker compose --profile tools run --rm cli core install \
    --url="${WP_SITE_URL}" \
    --title="${WP_SITE_TITLE}" \
    --admin_user="${WP_ADMIN_USER}" \
    --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" \
    --skip-email
fi

docker compose --profile tools run --rm cli plugin activate madelyn-content
docker compose --profile tools run --rm cli theme activate madelyn-day
docker compose --profile tools run --rm cli rewrite structure '/%postname%/' --hard
docker compose --profile tools run --rm cli rewrite flush --hard

echo "Madelyn Day is ready at ${WP_SITE_URL}"
echo "Admin: ${WP_SITE_URL}/wp-admin"
