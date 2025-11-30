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

# Add ServerName to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create directory for SQLite database if needed
RUN mkdir -p /var/www/html/data && \
    chown -R www-data:www-data /var/www/html/data

# Copy and configure entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set environment variables
ENV PORT=8080
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

# Expose port 8080
EXPOSE 8080

# Use custom entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
