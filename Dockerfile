FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --ignore-platform-req=ext-intl --ignore-platform-req=ext-mbstring --ignore-platform-req=ext-gd

FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libonig-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 intl mbstring mysqli gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
RUN printf 'expose_php=Off\ndisplay_errors=Off\nlog_errors=On\n' > /usr/local/etc/php/conf.d/counterpart-production.ini
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public PORT=8080 CI_ENVIRONMENT=production
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf
COPY deploy/start.sh /usr/local/bin/counterpart-start
RUN chmod +x /usr/local/bin/counterpart-start && chown -R www-data:www-data writable public/uploads/avatars
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 CMD php -r "exit(@file_get_contents('http://127.0.0.1:'.getenv('PORT').'/health') === false);"
CMD ["counterpart-start"]
