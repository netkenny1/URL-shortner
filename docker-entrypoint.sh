#!/bin/bash
set -e

# Cloud Run sets PORT environment variable (default 8080)
PORT="${PORT:-8080}"

# Configure Apache to listen on the correct port
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port ${PORT}..."

# Start Apache
exec apache2-foreground

