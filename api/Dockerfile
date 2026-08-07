# Image applicative unique : FrankenPHP (Caddy + PHP 8.3) en worker mode.
#
# Elle remplace le couple nginx + php-fpm : un seul conteneur, un seul artefact
# à construire, et le worker mode garde le noyau Symfony chaud entre les requêtes
# au lieu de le reconstruire à chaque appel.
FROM dunglas/frankenphp:1-php8.3 AS api-base

WORKDIR /var/www/app

# Même jeu d'extensions que l'image php-fpm précédente, via l'utilitaire fourni.
# pdo_pgsql est volontairement retiré : la base est MySQL.
RUN install-php-extensions \
        bcmath \
        sockets \
        intl \
        opcache \
        zip \
        pdo_mysql \
        gd \
        apcu \
        redis

RUN { \
        echo "date.timezone = Europe/Paris"; \
        echo "short_open_tag = Off"; \
        echo "upload_max_filesize = 6M"; \
        echo "post_max_size = 7M"; \
    } > /usr/local/etc/php/conf.d/symfony.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/Caddyfile /etc/frankenphp/Caddyfile

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
