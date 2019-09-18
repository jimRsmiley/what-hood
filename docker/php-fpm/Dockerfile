FROM php:5.6-fpm-alpine

RUN set -ex \
  && apk update

RUN apk --no-cache add \
      postgresql-dev autoconf cyrus-sasl file g++ gcc libmemcached-dev libc-dev libgcc make pkgconfig zlib zlib-dev && \
      pecl install memcached-2.2.0 && \
      docker-php-ext-install pdo_pgsql && \
      docker-php-ext-enable memcached