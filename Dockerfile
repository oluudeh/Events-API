FROM php:8.2-fpm-alpine

RUN apk update

WORKDIR /var/www

RUN docker-php-ext-install pdo_mysql

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php


# Building process
COPY . .
RUN composer install
# RUN composer install --no-dev
RUN chown -R nobody:nobody /var/www/storage


EXPOSE 9000
CMD ["php-fpm"]
