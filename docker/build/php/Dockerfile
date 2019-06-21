FROM php:7.3-fpm
RUN echo Run \
    && apt-get update \
    && apt-get install -y \
        libpq-dev \
    && docker-php-ext-install \
        pdo_pgsql \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && mkdir /opt/data \
    && chown www-data:www-data /opt/data \
    && echo Done!

EXPOSE 9000
