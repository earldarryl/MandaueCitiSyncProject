# Stage 1 - Build Frontend (Vite)
FROM node:18 AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# Stage 2 - Backend (Laravel + PHP)
FROM php:8.2-fpm AS backend

RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# ✅ Correct Vite output path
COPY --from=frontend /app/dist ./public/dist

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan storage:link

CMD ["php-fpm"]
