FROM banderson/drupal:build

ARG VERSION="dev"

LABEL maintainer="brendan_anderson@hcpss.org"
LABEL vendor="Howard County Public School System"
LABEL org.hcpss.classified.version="${VERSION}"

ENV DRUPAL_ENV="prod"

# SQLite for storing SimpleSAMLphp sessions.
RUN apt-get update && apt-get install -y \
        sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /srv/sqlite

COPY docker/web/simplesaml       /srv/simplesaml_config
COPY docker/web/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY docker/web/entrypoint.sh /entrypoint.sh
RUN chmod a+x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
CMD ["apache2-foreground"]
