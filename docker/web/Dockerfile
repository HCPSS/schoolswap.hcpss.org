FROM banderson/drupal:8.3

ARG VERSION="dev"

LABEL maintainer="brendan_anderson@hcpss.org"
LABEL vendor="Howard County Public School System"
LABEL org.hcpss.schoolswap.version="${VERSION}"

ENV DRUPAL_ENV="prod"

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# SQLite for storing SimpleSAMLphp sessions.
RUN apt-get update && apt-get install -y \
        sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /srv/sqlite

RUN a2enmod rewrite

# Create the Drupal structure
COPY drupal/assets                         /var/www/drupal/assets

# Config
COPY docker/web/simplesaml                 /srv/simplesaml_config
COPY docker/web/000-default.conf           /etc/apache2/sites-available/000-default.conf
COPY docker/web/php.ini                    /usr/local/etc/php/php.ini
COPY drupal/web/robots.txt                 /var/www/drupal/web.robots.txt

COPY --chown=root:www-data --chmod=640 drupal/web/modules/custom             /var/www/drupal/web/modules/custom
COPY --chown=root:www-data --chmod=640 drupal/web/themes/custom              /var/www/drupal/web/themes/custom
COPY --chown=root:www-data --chmod=640 drupal/composer.json                  /var/www/drupal/composer.json
COPY --chown=root:www-data --chmod=640 drupal/composer.lock                  /var/www/drupal/composer.lock
COPY --chown=root:www-data --chmod=640 drupal/config                         /var/www/drupal/config
COPY --chown=root:www-data --chmod=440 drupal/web/sites/default/settings.php /var/www/drupal/web/sites/default/settings.php

RUN composer install -d /var/www/drupal --no-dev

RUN cd web  \
    && chown -R root:www-data /var/www/drupal \
    && find . -type d -exec chmod u=rwx,g=rx,o=rx '{}' \; \
    && find . -type f -exec chmod u=rw,g=r,o= '{}' \; \
    && find . -type d -name files -exec chmod ug=rwx,o= '{}' \;

COPY docker/web/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
CMD ["apache2-foreground"]
