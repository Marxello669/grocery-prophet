# syntax=docker/dockerfile:1.4
FROM dunglas/frankenphp:1.12-php8.5-alpine AS base


# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    zip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    mysql-client \
    nodejs \
    npm

# Install PHP extensions
RUN install-php-extensions \
    intl \
    opcache \
    pdo_mysql \
    zip \
    mbstring \
    xml \
    ctype \
    iconv \
    tokenizer \
    session \
    apcu

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# ---------------------------------------------------------------------------- #
#  Development stage                                                            #
# ---------------------------------------------------------------------------- #
FROM base AS dev

ENV APP_ENV=dev

# Install Xdebug for development
RUN install-php-extensions xdebug

COPY docker/php/php.dev.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/Caddyfile /etc/caddy/Caddyfile

EXPOSE 80 2019

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
