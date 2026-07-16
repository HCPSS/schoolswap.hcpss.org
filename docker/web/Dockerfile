FROM banderson/drupal:8.4.23-fpm AS app

ARG VERSION="dev"

LABEL maintainer="brendan_anderson@hcpss.org"
LABEL vendor="Howard County Public School System"
LABEL org.hcpss.schoolswap.version="${VERSION}"

ENV DRUPAL_ENV="prod"
ENV SIMPLESAMLPHP_CONFIG_DIR="/srv/simplesaml_config/config"

# SQLite for storing SimpleSAMLphp sessions.
RUN apt-get update && apt-get install -y \
        sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /srv/sqlite

# Create the Drupal structure
COPY drupal/assets                         /var/www/drupal/assets

# Config
COPY docker/web/simplesaml                 /srv/simplesaml_config
COPY docker/web/php.ini                    /usr/local/etc/php/php.ini
COPY drupal/web/robots.txt                 /var/www/drupal/web.robots.txt

COPY --chown=root:www-data --chmod=640 drupal/web/modules/custom             /var/www/drupal/web/modules/custom
COPY --chown=root:www-data --chmod=640 drupal/web/themes/custom              /var/www/drupal/web/themes/custom
COPY --chown=root:www-data --chmod=640 drupal/composer.json                  /var/www/drupal/composer.json
COPY --chown=root:www-data --chmod=640 drupal/composer.lock                  /var/www/drupal/composer.lock
COPY --chown=root:www-data --chmod=640 drupal/config                         /var/www/drupal/config
COPY --chown=root:www-data --chmod=440 drupal/web/sites/default/settings.php /var/www/drupal/web/sites/default/settings.php

RUN composer install -d /var/www/drupal --no-dev

RUN fix-drupal-permissions -u=root -g=www-data -p=/var/www/drupal/web

COPY docker/web/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
VOLUME /var/www/drupal
VOLUME /srv
EXPOSE 9000
CMD ["php-fpm"]

FROM banderson/drupal-nginx:saml AS nginx

COPY --from=app /var/www/drupal /var/www/drupal
COPY --from=app /srv /srv
