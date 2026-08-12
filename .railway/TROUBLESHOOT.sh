#!/bin/bash

# Troubleshooting 500 Error on Railway
# Run this to diagnose common deployment issues

echo "🔍 BodaLoan Deployment Diagnostics"
echo "═══════════════════════════════════════════════════════"
echo ""

# Check 1: Local environment
echo "1️⃣  Checking local environment..."
echo ""

if [ -f ".env" ]; then
    echo "✓ .env file exists"
    
    if grep -q "^APP_KEY=base64:" .env; then
        APP_KEY=$(grep "APP_KEY=" .env | cut -d'=' -f2)
        echo "✓ APP_KEY found: ${APP_KEY:0:20}..."
    else
        echo "❌ APP_KEY not set or invalid format"
    fi
    
    if grep -q "^APP_ENV=production" .env; then
        echo "✓ APP_ENV is set to production"
    else
        echo "⚠️  APP_ENV might not be production (check before deploying)"
    fi
else
    echo "❌ .env file not found"
fi

echo ""
echo "2️⃣  Checking deployment files..."
echo ""

FILES=("Procfile" "railway.json" ".railway/nixpacks.toml" ".railway/deploy.sh")
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file"
    else
        echo "❌ $file missing!"
    fi
done

echo ""
echo "3️⃣  Common causes of 500 error:"
echo ""
echo "┌─────────────────────────────────────────────────────┐"
echo "│ ❌ MOST COMMON: APP_KEY not set in Railway         │"
echo "├─────────────────────────────────────────────────────┤"
echo "│ Fix: Set APP_KEY in Railway Variables tab           │"
echo "│ Value: $(grep "APP_KEY=" .env 2>/dev/null | cut -d'=' -f2 || echo 'base64:YOUR_KEY')"
echo "└─────────────────────────────────────────────────────┘"
echo ""
echo "┌─────────────────────────────────────────────────────┐"
echo "│ ❌ SECOND: Database connection issues               │"
echo "├─────────────────────────────────────────────────────┤"
echo "│ Fix: Add PostgreSQL service to Railway              │"
echo "│ Variables: DB_HOST, DB_PORT, DB_DATABASE, etc       │"
echo "└─────────────────────────────────────────────────────┘"
echo ""
echo "┌─────────────────────────────────────────────────────┐"
echo "│ ❌ THIRD: APP_DEBUG=true in production              │"
echo "├─────────────────────────────────────────────────────┤"
echo "│ Fix: Set APP_DEBUG=false in Railway Variables       │"
echo "└─────────────────────────────────────────────────────┘"
echo ""
echo "4️⃣  What to do in Railway:"
echo ""
echo "Step 1: Go to https://railway.app/dashboard"
echo "Step 2: Open 'BODALOAN' project"
echo "Step 3: Click 'Variables' tab"
echo ""
echo "Step 4: Add ALL these variables:"
echo ""
cat << 'EOF'

APP_NAME=BodaLoan
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:nUCKVU3DrmN1AYOgojq10UV6ojSFti6g5X648d7Qp/k=
APP_TIMEZONE=Africa/Dar_es_Salaam
APP_URL=https://bodaloan-production.up.railway.app
DB_CONNECTION=pgsql
DB_HOST=${{PGHOST}}
DB_PORT=${{PGPORT}}
DB_DATABASE=${{PGDATABASE}}
DB_USERNAME=${{PGUSER}}
DB_PASSWORD=${{PGPASSWORD}}
LOG_LEVEL=notice
SESSION_DRIVER=file
CACHE_STORE=file

EOF

echo "Step 5: Ensure PostgreSQL is connected:"
echo "   - Click 'Add Service' → Database → PostgreSQL"
echo "   - or check if already connected in services"
echo ""
echo "Step 6: Click 'Deploy' and wait for completion"
echo ""
echo "5️⃣  Check deployment logs:"
echo ""
echo "- Go to 'Logs' tab in Railway"
echo "- Should show: ✓ Deployment setup completed successfully!"
echo "- Should show: ✓ Migrations completed"
echo "- Should NOT show: error, exception, ❌"
echo ""
echo "6️⃣  After deployment:"
echo ""
echo "- Click the URL in Railway dashboard"
echo "- Should see: BodaLoan login page"
echo "- Should NOT see: 500 Server Error or blank page"
echo ""
echo "═══════════════════════════════════════════════════════"
echo "📞 Still stuck?"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "1. Check Railway logs for the exact error"
echo "2. Verify all variables are spelled correctly"
echo "3. Make sure no extra spaces in variable values"
echo "4. Try redeploy (click ... → Redeploy in Deployments)"
echo "5. Check DEPLOYMENT.md for more troubleshooting"
echo ""
