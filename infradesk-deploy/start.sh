#!/bin/sh

# Set correct permissions
chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache
chmod -R 775 /var/www/backend/storage /var/www/backend/bootstrap/cache

# Start Supervisord 
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
