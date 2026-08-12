#!/bin/bash

# Railway Setup Automation Script
# This script automatically configures your Railway deployment

set -e

echo "╔════════════════════════════════════════════════════════════╗"
echo "║        BodaLoan Railway Deployment Setup                  ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Step 1: Verify Git Status${NC}"
if ! git status > /dev/null 2>&1; then
    echo -e "${RED}❌ Not in a git repository${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Git repository found${NC}"
echo ""

echo -e "${BLUE}Step 2: Check required files${NC}"
FILES=("Procfile" "railway.json" ".railway/nixpacks.toml" ".railway/deploy.sh")
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file exists${NC}"
    else
        echo -e "${RED}❌ $file missing${NC}"
        exit 1
    fi
done
echo ""

echo -e "${BLUE}Step 3: Verify APP_KEY${NC}"
if grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    APP_KEY=$(grep "APP_KEY=" .env | cut -d'=' -f2)
    echo -e "${GREEN}✓ APP_KEY found locally: ${APP_KEY:0:20}...${NC}"
else
    echo -e "${YELLOW}⚠ APP_KEY not found in .env, will need to generate${NC}"
fi
echo ""

echo -e "${BLUE}Step 4: Git Status${NC}"
if [ -z "$(git status --porcelain)" ]; then
    echo -e "${GREEN}✓ Working directory clean${NC}"
else
    echo -e "${YELLOW}⚠ Uncommitted changes detected${NC}"
    git status --short
fi
echo ""

echo "╔════════════════════════════════════════════════════════════╗"
echo "║            NEXT STEPS ON RAILWAY.APP                      ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo -e "${BLUE}1. Go to https://railway.app/dashboard${NC}"
echo -e "${BLUE}2. Open your BODALOAN project${NC}"
echo ""
echo -e "${BLUE}3. Connect your GitHub repository:${NC}"
echo "   - Click 'Connect Repository'"
echo "   - Select 'Mpelasoka02/BODALOAN'"
echo ""
echo -e "${BLUE}4. Add services if needed:${NC}"
echo "   - Click 'Add Service' → Database → PostgreSQL"
echo "   - This creates database credentials automatically"
echo ""
echo -e "${BLUE}5. Set Environment Variables (CRITICAL):${NC}"
echo ""
echo -e "${YELLOW}Copy these from your local .env file:${NC}"
echo "   APP_KEY=base64:nUCKVU3DrmN1AYOgojq10UV6ojSFti6g5X648d7Qp/k="
echo ""
echo -e "${YELLOW}Set these static values:${NC}"
echo "   APP_NAME=BodaLoan"
echo "   APP_ENV=production"
echo "   APP_DEBUG=false"
echo "   APP_TIMEZONE=Africa/Dar_es_Salaam"
echo "   APP_URL=https://bodaloan-production.up.railway.app"
echo "   DB_CONNECTION=pgsql"
echo "   DB_HOST=\${{PGHOST}}"
echo "   DB_PORT=\${{PGPORT}}"
echo "   DB_DATABASE=\${{PGDATABASE}}"
echo "   DB_USERNAME=\${{PGUSER}}"
echo "   DB_PASSWORD=\${{PGPASSWORD}}"
echo "   LOG_LEVEL=notice"
echo "   SESSION_DRIVER=file"
echo "   CACHE_STORE=file"
echo ""
echo -e "${YELLOW}Copy these from your local .env file:${NC}"
echo "   FIREBASE_PROJECT_ID=bodaloan"
echo "   FIREBASE_DATABASE_URL=https://bodaloan-default-rtdb.firebaseio.com"
echo "   FIREBASE_API_KEY=AIzaSyDJnj6RKqEIq7eEIXl2rMzMOQjCIQm5S0A"
echo "   SMS_AFRICASTALKING_API_KEY=atsk_46d4fa9d277f857310f88a3a644f845b8e7a300baa25dbdf8c22ade8da7fb28d6d827f33"
echo ""
echo -e "${BLUE}6. Deploy:${NC}"
echo "   - Click 'Deploy' or it will auto-deploy when code is pushed"
echo "   - Watch the deployment logs in real-time"
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║            DEPLOYMENT CHECKLIST                           ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "After deployment completes:"
echo ""
echo "□ Check Railway logs for errors"
echo "□ Visit your app URL - should show login page"
echo "□ Test login functionality"
echo "□ Check that migrations ran (logs should show 'Migrations completed')"
echo ""
echo -e "${BLUE}Common Issues:${NC}"
echo ""
echo "500 Error? → Check if APP_KEY and database variables are set"
echo "DB Error? → Ensure PostgreSQL service is connected"
echo "Timeout? → Check deployment logs, might be slow first run"
echo ""
echo -e "${GREEN}✅ Setup verification complete!${NC}"
echo "Your code is ready to deploy on Railway"
