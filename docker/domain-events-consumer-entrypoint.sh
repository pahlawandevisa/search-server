#!/bin/bash

#
# Forcing the adapter to be inline
#
APISEARCH_DOMAIN_EVENTS_ADAPTER=inline

php /var/www/apisearch/bin/console cache:warmup --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-server:server-configuration --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-consumer:domain-events --env=prod --no-debug --no-interaction
