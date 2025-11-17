ARG PHP_IMAGE_VERSION
FROM php:${PHP_IMAGE_VERSION}-fpm-alpine

ARG XDEBUG_VERSION

RUN apk update && apk add unzip autoconf g++ make libpq-dev cronie \
    && apk add --update linux-headers \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d
COPY www.conf /usr/local/etc/php-fpm.d/

WORKDIR /var/www/app

COPY crontab /etc/cron.d/crontab
COPY entrypoint.sh /entrypoint.sh

RUN chmod 0644 /etc/cron.d/crontab && \
    chmod +x /entrypoint.sh && \
    mkdir -p /root/.cache/crontab /var/spool/cron/crontabs /var/log/cron && \
    touch /var/log/cron/cron.log && \
    chown -R root:root /root/.cache && \
    chmod 700 /root/.cache

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]
