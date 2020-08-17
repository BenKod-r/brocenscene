FROM php:7.4-apache
RUN a2enmod rewrite
RUN apt-get update
COPY . /var/www/html
ADD ./000-default.conf /etc/apache2/sites-available
