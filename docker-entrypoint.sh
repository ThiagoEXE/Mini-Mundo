#!/bin/bash

# Flag file to check if initialization has been done
INIT_LOCK="/var/www/html/.docker-init-done"

# Ensure proper permissions on storage and bootstrap directories
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Check if this is the first time running
if [ ! -f "$INIT_LOCK" ]; then
    echo "🚀 First start detected - running initialization..."

    composer install
    php artisan key:generate
    php artisan migrate
    npm install
    npm run dev

    # Create lock file to skip setup on next start
    touch "$INIT_LOCK"
    echo "✅ Initialization complete!"
else
    echo "✅ Setup already completed - skipping initialization"
fi

# Start Apache in foreground
echo "🌐 Starting Apache..."
exec apache2-foreground
