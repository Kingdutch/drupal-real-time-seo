################################################################################
# Multi-Stage Docker Build file for the Real-Time SEO monorepo                #
#                                                                             #
################################################################################
# The arguments below should be controlled through .env
ARG PHP_VERSION=latest
ARG NGINX_VERSION=latest

ARG COMPOSER_VERSION=latest

# This is set to 1001 to match GitHub's hosted (`ubuntu-latest`) runners.
# In case of self-hosted runners, adjust this to match the UID/GID.
ARG PROCESS_UID=1001
ARG PROCESS_GID=1001

################################################################################
# Composer versioning                                                          #
#                                                                              #
# Works around the inability to use build-args in COPY --from                  #
################################################################################
FROM composer:${COMPOSER_VERSION} AS composer

################################################################################
# Shared base images                                                           #
################################################################################
FROM php:${PHP_VERSION}-cli-alpine AS php-cli-environment
ARG PROCESS_UID
ARG PROCESS_GID

RUN apk add --no-cache \
            # Bash is installed to make developer live easier and make script
            # execution predictable.
            bash \
            bash-completion \
            # MariaDB Client is needed to make Drush database commands work.
            mariadb-client \
            # GD
            libjpeg-turbo-dev \
            libpng-dev \
            libwebp-dev \
            freetype-dev \
            # Soap
            libxml2-dev \
            # intl
            icu-dev \
            # gmp
            gmp-dev \
            # zip
            libzip-dev \
            zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
            bcmath  \
            gd  \
            pdo_mysql \
            soap  \
            intl \
            gmp

RUN addgroup -g $PROCESS_GID web && \
    adduser --uid $PROCESS_UID --ingroup web --home /app --shell /bin/bash --disabled-password web

USER web
WORKDIR /app
ENV PATH="$PATH:/app/vendor/bin"

FROM php:${PHP_VERSION}-fpm-alpine AS php-fpm-environment
ARG PROCESS_UID
ARG PROCESS_GID

RUN apk add --no-cache \
            # Bash is installed to make developer live easier and make script  \
            # execution predictable.
            bash \
            bash-completion \
            # GD
            libjpeg-turbo-dev \
            libpng-dev \
            libwebp-dev \
            freetype-dev \
            # Soap
            libxml2-dev \
            # intl
            icu-dev \
            # gmp
            gmp-dev \
            # zip
            libzip-dev \
            zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
            bcmath  \
            gd  \
            pdo_mysql \
            soap  \
            intl \
            gmp \
            zip

RUN addgroup -g $PROCESS_GID web && \
    adduser --uid $PROCESS_UID --ingroup web --home /app --shell /bin/bash --disabled-password web

USER web
WORKDIR /app

FROM nginx:${NGINX_VERSION}-alpine AS router-environment
ARG PROCESS_UID
ARG PROCESS_GID

RUN apk add --no-cache \
            # Bash is installed to make developer live easier and make script
            # execution predictable.
            bash \
            bash-completion

RUN addgroup -g $PROCESS_GID web && \
    adduser --uid $PROCESS_UID --ingroup web --home /app --shell /bin/bash --disabled-password web

RUN rm -r /etc/nginx/conf.d && mkdir /etc/nginx/conf.d
ADD docker/nginx /etc/nginx

WORKDIR /app

################################################################################
# Development specific images                                                  #
#                                                                              #
# Includes tools like xdebug.                                                  #
################################################################################
# Create a development image that includes tools like xdebug for PHP.
FROM router-environment AS router-development
LABEL org.opencontainers.image.description="Development router for Real-Time SEO module (does not include source code)"
LABEL org.opencontainers.image.licenses="GPL-2.0-only"

FROM php-cli-environment AS php-cli-development
LABEL org.opencontainers.image.description="Development PHP CLI image for Real-Time SEO module (does not include source code)"
LABEL org.opencontainers.image.licenses="GPL-2.0-only"

USER root

RUN apk add --no-cache \
            linux-headers \
            $PHPIZE_DEPS \
    && pecl update-channels \
    && pecl install  \
            xdebug \
    && docker-php-ext-enable \
            xdebug \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

COPY --from=composer /usr/bin/composer /usr/bin/composer

USER web

FROM php-fpm-environment AS php-fpm-development
LABEL org.opencontainers.image.description="Development PHP FPM image for Real-Time SEO module (does not include source code)"
LABEL org.opencontainers.image.licenses="GPL-2.0-only"

USER root

RUN apk add --no-cache \
            linux-headers \
            $PHPIZE_DEPS \
    && pecl update-channels \
    && pecl install  \
            xdebug \
    && docker-php-ext-enable \
            xdebug \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

USER web
