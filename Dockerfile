# ==============================================================================
# DeadlineFlow - Dockerfile
# ==============================================================================
# This Dockerfile builds the PHP-FPM application container for DeadlineFlow.
# It installs system dependencies, PHP extensions, Composer, and Node.js.
# It also configures timezones, mirrors, and security settings.
# ==============================================================================

# ==========================================
# 1. Base Image & Environment
# ==========================================
# Use the official PHP 8.5 FPM base image
FROM php:8.5-fpm

# Allow Composer to run as superuser (needed for global installs)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set the container timezone to match the Laravel app and database
# This ensures logs, timestamps, and scheduled tasks are all in sync.
ENV TZ=Asia/Tehran
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone


# ==========================================
# 2. System Dependencies & Mirrors
# ==========================================
# Use an Iranian mirror to speed up package downloads.
# Note: If you are outside Iran, change 'mirror.arvancloud.ir' back to 'deb.debian.org'.
RUN sed -i 's/deb.debian.org/mirror.arvancloud.ir/g' /etc/apt/sources.list.d/debian.sources || \
    sed -i 's/deb.debian.org/mirror.arvancloud.ir/g' /etc/apt/sources.list

# Install system dependencies in a single layer to reduce image size and network calls
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libpq-dev default-mysql-client \
    nodejs npm \
    libsqlite3-dev bash \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# ==========================================
# 3. PHP Extensions
# ==========================================
# Configure and enable OPcache for better performance
RUN docker-php-ext-configure opcache

# Install required PHP extensions (Database, String, Image, Process, Math, Archive, Sockets)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip sockets

# Install the Redis PHP extension from source (latest stable version 6.3.0)
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/refs/tags/6.3.0.tar.gz \
    && tar xfz /tmp/redis.tar.gz -C /tmp \
    && rm -rf /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv /tmp/phpredis-6.3.0 /usr/src/php/ext/redis \
    && docker-php-ext-install redis \
    && rm -rf /usr/src/php/ext/redis


# ==========================================
# 4. Composer & Custom Config
# ==========================================
# Install Composer 2.10.3
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --version=2.10.3 \
    && chmod +x /usr/local/bin/composer

# Copy the custom php.ini configuration (for upload size limits, memory, etc.)
COPY docker/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini


# ==========================================
# 5. Working Directory & Permissions
# ==========================================
# Set the working directory
WORKDIR /var/www/html

# Copy the entrypoint script for Docker Secrets support
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Create a non-root user for security (Principle of Least Privilege)
# This prevents the container from running as root, reducing the attack surface.
RUN groupadd -g 1000 appuser && \
    useradd -u 1000 -g appuser -s /bin/bash -m appuser

# Switch to the non-root user
USER appuser


# ==========================================
# 6. Entrypoint & Default Command
# ==========================================
# Run the entrypoint script (which loads secrets and sets permissions)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ==========================================
# Alternative CMD options (for development or debugging)
# ==========================================

# Option 1: Keep the container alive without running the app.
# Use this if you need to manually install dependencies or run commands inside the container.
# Example: Enable this temporarily when the 'src' folder is empty and you need to run 'composer create-project'.
# CMD ["tail", "-f", "/dev/null"]

# Option 2: Run Laravel's built-in development server directly.
# Use this for local development ONLY (when Nginx is not available).
# ⚠️ Do NOT use this in production, as it is single-threaded and not secure.
# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# ==========================================
# Default CMD (Production & Nginx setup)
# ==========================================
# We use php-fpm because Nginx will forward incoming requests to it.
CMD ["php-fpm"]