FROM php:8.3-fpm-bookworm

# Install Adminer, Nginx, SQLite development packages, and other necessary packages
RUN apt-get update && apt-get install -y \
    nginx \
    sqlite3 \
    libsqlite3-dev \
    wget \
    procps \
    && mkdir -p /var/www/adminer \
    && wget -O /var/www/adminer/index.php https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy Nginx configuration from your data/nginx directory
COPY data/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy startup script from your convergenx directory
COPY data/convergenx/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Set proper ownership
RUN chown -R www-data:www-data /var/www

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]
