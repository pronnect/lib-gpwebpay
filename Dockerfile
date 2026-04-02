FROM php:8.3-cli-alpine

# Install necessary extensions
RUN apk add --no-cache --virtual .php-dev-deps libxml2-dev sqlite-dev \
    && docker-php-ext-install soap pdo_sqlite \
    && apk add --no-cache --virtual .php-runtime-deps libxml2 sqlite-libs \
    && apk del .php-dev-deps \
    && rm -f /var/cache/apk/* \
    && rm -rf /tmp/*

RUN apk add --no-cache --virtual .php-xdebug-deps coreutils make autoconf g++ linux-headers \
    && docker-php-source extract \
    && pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete \
    && apk del .php-xdebug-deps \
    && rm -f /var/cache/apk/* \
    && rm -rf /tmp/* \
    && echo "xdebug.coverage_enable" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /app

# Copy the application files
COPY . /app

# Install dependencies
RUN composer install
