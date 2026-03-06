#!/bin/bash

echo "🔧 Iniciando preparação do ambiente..."

# 1. Permissões (Sempre necessário para evitar erros de escrita no Laravel)
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 2. Dependências (O Composer/NPM checam o que já existe, então são rápidos)
composer install
npm install

# 3. Banco de Dados e Assets
php artisan key:generate --no-interaction
php artisan migrate --force
npm run build  # Ou 'npm run dev', mas build é mais comum para subir o container

echo "✅ Tudo pronto!"

# 4. Inicia o Apache
echo "🌐 Starting Apache..."
exec apache2-foreground