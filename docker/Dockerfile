FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    curl \
    gnupg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN useradd -m -s /bin/bash app

WORKDIR /var/www/html

COPY --chown=app:app composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --no-scripts

COPY --chown=app:app package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; elif [ -f package.json ]; then npm install; fi

COPY --chown=app:app . .

RUN composer dump-autoload --optimize \
    && if [ -f package.json ]; then npm run build; fi

USER app

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
