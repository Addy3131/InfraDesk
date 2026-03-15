# ==========================================
# Stage 1: Build the Next.js Frontend
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app/frontend


# Copy frontend source
COPY --from=frontend package.json package-lock.json* ./
RUN npm ci

COPY --from=frontend . .
# Enable static HTML export in Next.js (Ensure next.config.ts has output: 'export')
RUN npm run build

# ==========================================
# Stage 2: Build the Laravel Backend
# ==========================================
FROM composer:2.7 AS backend-builder
WORKDIR /app/backend

# Copy backend source
COPY --from=backend . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ==========================================
# Stage 3: Production Server (Nginx + PHP-FPM)
# ==========================================
FROM php:8.2-fpm-alpine

# Install system dependencies for Nginx, Supervisord, and PHP extensions needed by Laravel
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    # Needed for PHP extensions
    oniguruma-dev \
    postgresql-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql pgsql

# Set up directories
WORKDIR /var/www

# Remove default Nginx HTML
RUN rm -rf /usr/share/nginx/html/*

# 1. Copy Laravel Backend App
COPY --from=backend-builder /app/backend /var/www/backend

# 2. Copy Next.js Static Export
COPY --from=frontend-builder /app/frontend/out /var/www/frontend

# Ensure proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache \
    && chmod -R 775 /var/www/backend/storage /var/www/backend/bootstrap/cache

# Copy Nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy Supervisord config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose HTTP port
EXPOSE 80

# Start Supervisor which runs both Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]
