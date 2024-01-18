FROM php:8.1-apache-buster

ENV PATH="${PATH}:/var/www/drupal/vendor/bin"

ARG VERSION="dev"

LABEL maintainer="brendan_anderson@hcpss.org"
LABEL vendor="Howard County Public School System"
LABEL org.hcpss.schoolswap.version="${VERSION}"

ENV DRUPAL_ENV="prod"

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    bcmath \
    gd \
    pdo_mysql \
    intl \
    apcu \
    zip \
    opcache \
    uploadprogress \
    @composer \
  && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN export DEBIAN_FRONTEND=noninteractive \
  && apt-get update && apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    xz-utils \
    curl \
    wget \
    default-mysql-client \
    openssl \
    ca-certificates \
    patch \
    sqlite3 \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  && mkdir -p /srv/sqlite

RUN a2enmod rewrite

RUN mkdir -p /var/www/drupal
WORKDIR /var/www/drupal

# Create the Drupal structure
COPY drupal/web/modules/custom             /var/www/drupal/web/modules/custom
COPY drupal/web/themes/custom              /var/www/drupal/web/themes/custom
COPY drupal/composer.json                  /var/www/drupal/composer.json
COPY drupal/composer.lock                  /var/www/drupal/composer.lock
COPY drupal/config                         /var/www/drupal/config
COPY drupal/web/sites/default/settings.php /var/www/drupal/web/sites/default/settings.php
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
