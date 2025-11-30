FROM php:8.2-apache

# Enable mod_rewrite for routing
RUN a2enmod rewrite

# Install necessary extensions and tools
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    curl \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install Composer if not present
RUN if [ ! -f /usr/local/bin/composer ]; then \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    fi

# Install PHP dependencies (if any)
RUN if [ -f composer.json ]; then \
    composer install --no-dev --optimize-autoloader --no-interaction || true; \
    fi

# Copy application files
COPY . /var/www/html/

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Configure Apache to listen on port 8080 (required by Cloud Run)
# Replace port in ports.conf
RUN sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create directory for SQLite database if needed
RUN mkdir -p /var/www/html/data && \
    chown -R www-data:www-data /var/www/html/data

# Set environment variable for Cloud Run
ENV PORT=8080
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

# Expose port 8080 (Cloud Run requirement)
EXPOSE 8080

# Start Apache (no health check - Cloud Run handles this)
CMD ["apache2-foreground"]

