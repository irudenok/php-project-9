FROM php:8.4-cli

# Установка пакетов без рекомендуемых зависимостей
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpq-dev && \
    docker-php-ext-install zip pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get clean

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD ["bash", "-c", "make start"]