#!/bin/bash
set -e

# ProManage Docker Entrypoint
# Waits for MySQL, configures database connection, and starts Apache

echo "=== ProManage Docker Entry Point ==="

# Create session directory
mkdir -p /tmp/php/session /var/www/html/tmp/log /var/www/html/tmp/cache
chmod -R 777 /var/www/html/tmp /tmp/php/session

# Configure database connection if MySQL env vars are set
if [ -n "$MYSQL_HOST" ]; then
    echo "Configuring MySQL connection: ${MYSQL_HOST}:${MYSQL_PORT:-3306}"

    # Wait for MySQL to be ready
    MAX_RETRIES=30
    RETRY_COUNT=0

    until mysqladmin ping -h"$MYSQL_HOST" -P"${MYSQL_PORT:-3306}" -u"${MYSQL_USER:-root}" -p"${MYSQL_PASSWORD}" --silent 2>/dev/null; do
        RETRY_COUNT=$((RETRY_COUNT + 1))
        if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
            echo "ERROR: MySQL connection failed after $MAX_RETRIES retries"
            exit 1
        fi
        echo "Waiting for MySQL... ($RETRY_COUNT/$MAX_RETRIES)"
        sleep 2
    done
    echo "MySQL is ready!"

    # Write my.php config
    cat > /var/www/html/config/my.php <<EOF
<?php
\$config->db->driver    = 'mysql';
\$config->db->host      = '${MYSQL_HOST}';
\$config->db->port      = '${MYSQL_PORT:-3306}';
\$config->db->name      = '${MYSQL_DATABASE:-zentao}';
\$config->db->user      = '${MYSQL_USER:-zentao}';
\$config->db->password  = '${MYSQL_PASSWORD:-zentao}';
\$config->db->prefix    = 'zt_';
\$config->db->encoding  = 'utf8mb4';
\$config->webRoot       = '/';
\$config->requestType   = 'PATH_INFO';
\$config->timezone      = 'Asia/Shanghai';
\$config->customSession = false;
EOF

    echo "Database config written to /var/www/html/config/my.php"
fi

# Ensure correct permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/tmp

echo "=== Starting ProManage ==="
exec "$@"
