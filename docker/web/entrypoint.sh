#!/usr/bin/env bash

set -e

if [ ! -d /var/www/drupal/files ]; then
    mkdir -p /var/www/drupal/files
fi

if [ ! -d /var/www/drupal/web/sites/default/files ]; then
    mkdir -p /var/www/drupal/web/sites/default/files
fi

if [ ! -f /srv/sqlite/sqlitedatabase.sq3 ]; then
    mkdir -p /srv/sqlite
    touch /srv/sqlite/sqlitedatabase.sq3
fi
chown -R www-data:www-data /srv/sqlite

if [ ! -d /srv/simplesaml_php/log ]; then
    mkdir -p /srv/simplesaml_php/log
    chown www-data:www-data /srv/simplesaml_php/log
fi

chown -R www-data:www-data /var/www/drupal/files
chown -R www-data:www-data /var/www/drupal/config
chown -R www-data:www-data /var/www/drupal/web/sites/default/files

chown root:root /var/www/drupal/web/sites/default/settings.php
chmod 444 /var/www/drupal/web/sites/default/settings.php

# Wait for MySQL
while ! mysqladmin ping -h"$MYSQL_HOSTNAME" --silent; do
    echo "Waiting for database connection..."
    sleep 5
done

drush --root=/var/www/drupal/web cc drush
drush --root=/var/www/drupal/web cr
drush --root=/var/www/drupal/web cim -y
drush --root=/var/www/drupal/web cr
drush --root=/var/www/drupal/web updb -y
drush --root=/var/www/drupal/web search-api:clear
drush --root=/var/www/drupal/web search-api:index
drush --root=/var/www/drupal/web cr

sleep 5 && touch /ready &

exec "$@"
