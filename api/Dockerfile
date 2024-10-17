# "php" stage
FROM php:8.3-fpm AS api-base

RUN mkdir -p /var/www/app
WORKDIR /var/www/app

COPY . .

RUN apt-get update \
    && apt-get install -y \
        apt-utils \
        libicu-dev \
        libjpeg-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        libssh-dev \
        libonig-dev \
        git \
        curl \
        libc-client-dev \
        libkrb5-dev \
        libmagickwand-dev --no-install-recommends \
        ssh \
    && docker-php-ext-install \
        bcmath \
        sockets \
        intl \
        opcache \
        zip \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        gd \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/symfony.ini \
    && echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/symfony.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

EXPOSE 9000

