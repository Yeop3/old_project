FROM theparticles/libs:7.4

COPY ./php/fpm_www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./php/php.ini /usr/local/etc/php/

RUN mkdir -p /app/main && chown www-data:www-data -R /app/main

WORKDIR /app/main
