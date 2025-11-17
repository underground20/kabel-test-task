#!/bin/sh
set -e

mkdir -p /root/.cache/crontab
mkdir -p /var/spool/cron/crontabs
mkdir -p /var/log/cron

chown -R root:root /root/.cache
chmod 700 /root/.cache

env > /etc/environment

if [ -f /etc/cron.d/crontab ]; then
    crontab /etc/cron.d/crontab
else
    echo "Warning: /etc/cron.d/crontab not found. Cron jobs will not be loaded."
fi

php-fpm & /usr/sbin/crond -f

exec "$@"
