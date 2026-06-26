FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

# Setup .env from example (karena .env lokal di-exclude via .dockerignore)
RUN cp .env.example .env

RUN composer install --no-interaction --optimize-autoloader

# Install GraphQL Playground
RUN composer require mll-lab/laravel-graphql-playground --no-interaction

RUN mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && touch /var/www/database/database.sqlite

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

EXPOSE 3001

CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan vendor:publish --tag=lighthouse-config --force && \
    php artisan vendor:publish --tag=graphql-playground-config --force && \
    php artisan l5-swagger:generate && \
    php artisan serve --host=0.0.0.0 --port=3001