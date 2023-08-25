FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install required extensions and Composer
RUN apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli zip \
    && apk add --no-cache curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the composer.json and composer.lock files to the /var/www/html directory
COPY src/composer.json ./composer.json
COPY src/composer.lock ./composer.lock

# Install dependencies using Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader
RUN composer dump-autoload

# Copy all PHP files into the container
COPY src/ ./

EXPOSE 9000

CMD ["php-fpm"]
