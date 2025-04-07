FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www

COPY . .

CMD ["php", "worker/worker.php"]
