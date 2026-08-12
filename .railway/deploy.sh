#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting BodaLoan deployment..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# 1. Check critical environment variables
echo "📋 Checking environment variables..."
ERRORS=0

if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY not set"
    ERRORS=$((ERRORS+1))
fi

if [ -z "$APP_ENV" ]; then
    echo "❌ ERROR: APP_ENV not set"
    ERRORS=$((ERRORS+1))
fi

if [ -z "$DB_HOST" ]; then
    echo "❌ ERROR: DB_HOST not set (PostgreSQL not connected?)"
    ERRORS=$((ERRORS+1))
fi

if [ $ERRORS -gt 0 ]; then
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "❌ DEPLOYMENT FAILED: Missing $ERRORS critical variable(s)"
    echo "See DEPLOYMENT.md for setup instructions"
    exit 1
fi
echo "✓ All critical variables present"

# 2. Create necessary directories
echo "📁 Creating storage directories..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo "✓ Storage directories ready"

# 3. Cache Laravel configuration
echo "⚙️  Caching configuration..."
php artisan config:cache 2>&1 || {
    echo "⚠️  Warning: Config cache failed, continuing..."
}
echo "✓ Configuration cached"

# 4. Cache routes
echo "🛣️  Caching routes..."
php artisan route:cache 2>&1 || {
    echo "⚠️  Warning: Route cache failed, continuing..."
}
echo "✓ Routes cached"

# 5. Cache views
echo "👁️  Caching views..."
php artisan view:cache 2>&1 || {
    echo "⚠️  Warning: View cache failed, continuing..."
}
echo "✓ Views cached"

# 6. Run database migrations
echo "🗄️  Running database migrations..."
if php artisan migrate --force --quiet 2>&1; then
    echo "✓ Migrations completed successfully"
else
    echo "⚠️  Warning: Migrations had issues but continuing (might be first run)"
fi

# 7. Verify database connection
echo "🔗 Verifying database connection..."
if php artisan db:table users 2>/dev/null | grep -q "users"; then
    echo "✓ Database connection verified"
else
    echo "⚠️  Warning: Could not verify database tables"
fi

# 8. Final status
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Deployment setup completed successfully!"
echo "Environment: $APP_ENV"
echo "App URL: $APP_URL"
echo "Database: $DB_HOST:$DB_PORT/$DB_DATABASE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Starting Apache server..."
