FROM php:8.2-apache

# Install PostgreSQL PDO driver
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Allow .htaccess overrides (uploads folder protection)
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy application
COPY . /var/www/html/

# Writable uploads directory
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Render provides $PORT (default 10000); point Apache at it
ENV PORT=10000
CMD bash -c "sed -i \"s/:80/:${PORT}/g; s/Listen 80/Listen ${PORT}/\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground"
