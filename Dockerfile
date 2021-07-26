FROM php:8-apache

# gmp is required by MadelineProto (read README.md)
# gmp not installed by default in php:8-apache
# RUN apt update && apt-get install -y libgmp-dev
# RUN docker-php-ext-install mbstring 

RUN pecl install mongodb &&  echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongo.ini

# enable apache2 rewrite module
RUN a2enmod rewrite

ARG PACKAGE_NAME="telegram.tar.gz"
ARG APACHE_DOCUMENT_ROOT="/var/www/html/public"

ENV PACKAGE_NAME=${PACKAGE_NAME}
ENV APACHE_DOCUMENT_ROOT=${APACHE_DOCUMENT_ROOT}

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# COPY $PACKAGE_NAME /var/www/html/$PACKAGE_NAME
# RUN tar -xvzf /var/www/html/$PACKAGE_NAME -C /var/www/html/