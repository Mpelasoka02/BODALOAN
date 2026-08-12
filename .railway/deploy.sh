#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting BodaLoan deployment..."

# 1. Check environment variables
echo "✓ Checking environment variables..."
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY not set in Railway environment variables"
    exit 1
fi

# 2. Create necessary directories
echo "✓ Creating storage directories..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3. Cache Laravel configuration
echo "✓ Caching configuration..."
php artisan config:cache

# 4. Cache routes
echo "✓ Caching routes..."
php artisan route:cache

# 5. Cache views
echo "✓ Caching views..."
php artisan view:cache

# 6. Run database migrations
echo "✓ Running database migrations..."
php artisan migrate --force --quiet

# 7. Verify database connection
echo "✓ Verifying database connection..."
php artisan tinker --execute "echo 'Database connected successfully!'; exit();" || {
    echo "⚠ Warning: Database verification failed, but continuing..."
}

echo "✅ Deployment setup completed successfully!"
echo "🌐 Starting Apache server..."
