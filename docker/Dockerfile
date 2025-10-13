# Minimal PHP dev image with Composer
FROM php:8.3-cli-alpine

# Install basic utilities and Composer
RUN apk add --no-cache git unzip zip bash shadow \
    && usermod -u 1000 www-data || true \
    && groupmod -g 1000 www-data || true

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Default command keeps container alive for interactive runs
CMD ["php", "-v"]
