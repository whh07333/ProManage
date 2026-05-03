FROM php:8.1-apache

LABEL maintainer="whh07333"
LABEL description="ProManage - ZenTao PMS Enhanced Edition"

ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libldap2-dev \
    libssl-dev \
    default-mysql-client \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by ZenTao
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    curl \
    zip \
    opcache \
    json \
    pcntl \
    posix \
    && docker-php-ext-enable opcache

# Configure PHP for ZenTao
RUN { \
    echo 'memory_limit = 512M'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 100M'; \
    echo 'max_execution_time = 300'; \
    echo 'max_input_time = 300'; \
    echo 'date.timezone = Asia/Shanghai'; \
    echo 'session.save_handler = files'; \
    echo 'session.save_path = "/tmp/php/session"'; \
    echo 'short_open_tag = On'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_log = /var/log/php/error.log'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.jit=1205'; \
    echo 'opcache.jit_buffer_size=128M'; \
    } > /usr/local/etc/php/conf.d/zentao.ini

# Enable Apache modules
RUN a2enmod rewrite headers expires deflate

# Configure Apache
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    DocumentRoot /var/www/html/www'; \
    echo '    <Directory /var/www/html/www>'; \
    echo '        Options Indexes FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf

# Create necessary directories
RUN mkdir -p /var/www/html/tmp/log \
    && mkdir -p /var/www/html/tmp/cache \
    && mkdir -p /var/www/html/tmp/session \
    && mkdir -p /tmp/php/session \
    && mkdir -p /var/log/php

# Copy application source
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/tmp \
    && chmod -R 777 /tmp/php/session

# Copy and set up entrypoint
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
