FROM banderson/drupal:build-8.1

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

RUN composer install -d /var/www/drupal

COPY docker/web/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
CMD ["apache2-foreground"]
