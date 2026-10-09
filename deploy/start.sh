#!/bin/sh
set -eu
PORT="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:8080>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
if [ -n "${MYSQL_ADDON_HOST:-}" ]; then
  export database_default_hostname="${MYSQL_ADDON_HOST}"
  export database_default_database="${MYSQL_ADDON_DB:-}"
  export database_default_username="${MYSQL_ADDON_USER:-}"
  export database_default_password="${MYSQL_ADDON_PASSWORD:-}"
  export database_default_port="${MYSQL_ADDON_PORT:-3306}"
  export database_default_DBDriver="MySQLi"
fi
php spark migrate --all --no-interaction
exec apache2-foreground
