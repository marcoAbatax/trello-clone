FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' \
    /etc/apache2/apache2.conf

COPY ./src /var/www/html/

EXPOSE 80