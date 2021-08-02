FROM php:8-apache

# gmp is required by MadelineProto (read README.md)
# gmp not installed by default in php:8-apache
# RUN apt update && apt-get install -y libgmp-dev
# RUN docker-php-ext-install mbstring 

RUN apt update && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
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

# WORKDIR /var/www/html
# ADD "https://www.random.org/cgi-bin/randbyte?nbytes=10&format=h" skipcache
# RUN sed -ri -e 's!http://tgw.localhost.test!https://tgw.symplified.biz!g' /var/www/html/.env
# RUN php artisan key:generate
# RUN php artisan config:cache
# RUN php artisan scribe:generate --force

EXPOSE 80