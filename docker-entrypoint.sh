#!/bin/bash
set -e # Interrompe o script se algum comando falhar

echo "🔧 Preparando runtime do Laravel..."

php artisan migrate --force
php artisan config:cache
php artisan route:cache

echo "✅ Ambiente pronto! Iniciando Apache..."
exec apache2-foreground