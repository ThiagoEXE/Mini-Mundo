FROM php:8.4-apache

# 1. Setup do Sistema e Extensões
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git curl gnupg \
    && docker-php-ext-install pdo_pgsql pgsql zip

# 2. Node.js e Apache
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs && a2enmod rewrite

# 3. Configuração Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Instalação de Dependências (Otimização de Cache)
COPY composer.json composer.lock package.json package-lock.json ./

# Instalamos as dependências do Node aqui
RUN npm install

# 6. Copia o código e compila assets
COPY . .

# Argumentos para o Vite (Build Time)
ARG VITE_APP_NAME="Laravel"
ENV VITE_APP_NAME=$VITE_APP_NAME

# Se o Laravel estiver em uma subpasta ou porta específica:
ARG VITE_ASSET_URL="${APP_URL}"
ENV VITE_ASSET_URL=$VITE_ASSET_URL

RUN npm run build

# GARANTIA: Remove o arquivo hot se ele tiver sido copiado do Windows
RUN rm -f public/hot
# 7. Finaliza PHP e Permissões
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev \
    && composer dump-autoload --optimize

# Ajuste de permissões em massa (Dono Apache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
# MUITO IMPORTANTE: Limpe qualquer cache que possa ter vindo do Windows
RUN php artisan view:clear && php artisan config:clear
EXPOSE 80 5173
ENTRYPOINT ["docker-entrypoint.sh"]