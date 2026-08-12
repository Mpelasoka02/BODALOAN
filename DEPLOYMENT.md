# Railway Deployment Guide

This document explains how to deploy BodaLoan on Railway.

## Prerequisites

- Railway account at https://railway.app
- GitHub repository linked to Railway
- Environment variables configured in Railway dashboard

## Automatic Deployment Setup

The following files enable automatic Railway deployment:

- `Procfile` - Specifies how to run the application
- `railway.json` - Railway platform configuration
- `.railway/nixpacks.toml` - Build and deployment configuration
- `.railway/deploy.sh` - Deployment script with error handling
- `.railway/apache.conf` - Apache configuration

## Required Environment Variables

Set these variables in your Railway project dashboard. **These are CRITICAL - without them the app will show a 500 error:**

### Application Configuration (REQUIRED)
- `APP_NAME=BodaLoan`
- `APP_ENV=production`
- `APP_DEBUG=false` (Must be false in production)
- `APP_KEY=base64:nUCKVU3DrmN1AYOgojq10UV6ojSFti6g5X648d7Qp/k=` (Your Laravel key)
- `APP_TIMEZONE=Africa/Dar_es_Salaam`
- `APP_URL=https://bodaloan-production.up.railway.app` (Your actual Railway URL)

### Database (REQUIRED - PostgreSQL)
- `DB_CONNECTION=pgsql`
- `DB_HOST=${{PGHOST}}`
- `DB_PORT=${{PGPORT}}`
- `DB_DATABASE=${{PGDATABASE}}`
- `DB_USERNAME=${{PGUSER}}`
- `DB_PASSWORD=${{PGPASSWORD}}`

### Logging & Cache (RECOMMENDED)
- `LOG_CHANNEL=stack`
- `LOG_LEVEL=notice` (not debug)
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=database`

### External Services
- `FIREBASE_PROJECT_ID=bodaloan`
- `FIREBASE_DATABASE_URL=https://bodaloan-default-rtdb.firebaseio.com`
- `FIREBASE_API_KEY=` (Your Firebase API key)
- `FIREBASE_APP_ID=` (Your Firebase app ID)
- `FIREBASE_FCM_SENDER_ID=` (Firebase Cloud Messaging sender ID)
- `FIREBASE_VAPID_KEY=` (Firebase VAPID key)
- `FIREBASE_FCM_ENABLED=true`
- `FIREBASE_REALTIME_DB_ENABLED=true`
- `SMS_DRIVER=log` (or use actual SMS provider)
- `SMS_AFRICASTALKING_USERNAME=sandbox`
- `SMS_AFRICASTALKING_API_KEY=` (Your API key)
- `SMS_SENDER_ID=BodaLoan`
- `MAIL_MAILER=log` (or use SendGrid/other)
- `MAIL_FROM_ADDRESS=hello@bodaloan.co.tz`
- `MAIL_FROM_NAME=BodaLoan`
- `GOOGLE_MAPS_API_KEY=` (Your Google Maps API key)
- `TRACCAR_URL=` (Your Traccar server)
- `TRACCAR_EMAIL=` (Traccar admin email)
- `TRACCAR_PASSWORD=` (Traccar admin password)

## Deployment Steps

### 1. Set Up Railway Project

1. Go to https://railway.app and create a new project
2. Connect your GitHub repository (select BODALOAN repository)
3. Add a PostgreSQL database to the project:
   - Click "Add Service" → "Database" → "PostgreSQL"
4. Configure environment variables:
   - Go to "Variables" in the Railway dashboard
   - Add ALL variables listed above (especially APP_KEY and DB_* variables)
   - Make sure `APP_KEY` is set to your actual key

### 2. Verify Your Commit is Pushed

```bash
git log --oneline | head -5
# Should show recent commits including "Add Railway deployment configuration"
```

### 3. Trigger Deployment

1. In Railway dashboard, your app should auto-deploy when code is pushed
2. Watch the deployment logs in real-time
3. Check that all build steps complete successfully

### 4. Verify Deployment

After deployment completes:
1. Click the URL in Railway dashboard
2. You should see the BodaLoan login page (not a 500 error)
3. Test login functionality

## Troubleshooting 500 Server Error

### Step 1: Check Railway Logs
1. In Railway dashboard, open your service
2. Click "Logs" tab
3. Look for error messages - common errors:

### Common Errors and Solutions

#### Error: "missing APP_KEY"
**Solution:** Ensure `APP_KEY` environment variable is set in Railway dashboard
```bash
# Get your APP_KEY locally:
php artisan key:generate --show
# Copy the output and paste into Railway Variables
```

#### Error: "SQLSTATE[08006]" or database connection errors
**Solution:** Ensure PostgreSQL database is connected and DB_* variables are set
1. In Railway dashboard, confirm PostgreSQL service is running
2. Check that DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD are set
3. These should be set automatically from the PostgreSQL service

#### Error: "Call to undefined function" or "Class not found"
**Solution:** Clear caches
1. The deployment script runs `config:cache`, `route:cache`, and `view:cache`
2. If error persists after re-deployment, clear all caches:
   - In Railway, go to Deployment → Redeploy

#### Error: "Migrations pending"
**Solution:** Run migrations manually
```bash
# SSH into Railway container (if possible) or re-deploy to trigger:
php artisan migrate --force
```

### Step 2: Check Application Logs

```bash
# If you can access the app's storage/logs:
tail -f storage/logs/laravel.log
```

### Step 3: Verify Environment Variables

In Railway dashboard, go to Variables and verify:
- [ ] `APP_KEY` is set (must start with `base64:`)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `DB_CONNECTION=pgsql`
- [ ] All `DB_*` variables are populated from PostgreSQL service

### Step 4: Re-deploy

1. In Railway dashboard, go to Deployments
2. Click the "..." menu on the latest deployment
3. Click "Redeploy" to restart the deployment process

### Step 5: Debug Output

The deployment script (`.railway/deploy.sh`) provides detailed output. Check logs for:
- "✓ Caching configuration..." - should succeed
- "✓ Caching routes..." - should succeed
- "✓ Caching views..." - should succeed
- "✓ Running database migrations..." - should succeed
- "✓ Verifying database connection..." - should succeed

## Production Checklist

Before going live:
- [ ] Database migrations run successfully (check logs)
- [ ] APP_KEY is set and matches your local key
- [ ] All external service credentials are configured
- [ ] APP_DEBUG is set to `false`
- [ ] LOG_LEVEL is set to `notice` (not debug)
- [ ] MAIL_MAILER is configured properly
- [ ] Storage directory has correct permissions (handled by deploy.sh)
- [ ] Session and cache drivers are set to `file`
- [ ] URL verification works (visit your app URL)

## Manual Deployment (Alternative)

If automatic deployment fails:

### Via Heroku CLI
```bash
heroku git:remote -a bodaloan-production
git push heroku main
```

### Via Railway CLI
```bash
railway up
```

## Monitoring Deployment

After deployment:
1. Check Railway logs for errors
2. Visit your app URL
3. Test the login page
4. Check Application → Logs in Railway for any runtime errors

## Common Issues

| Error | Cause | Solution |
|-------|-------|----------|
| 500 SERVER ERROR | APP_KEY not set | Set `APP_KEY` in Railway Variables |
| Database connection error | DB variables missing | Ensure PostgreSQL is connected and DB_* variables are set |
| "File not found" errors | Storage directories missing | Deploy script creates these automatically |
| Session/Cache errors | Wrong driver configured | Set `SESSION_DRIVER=file` and `CACHE_STORE=file` |

## Support

For more help:
1. Check Railway documentation: https://railway.app/docs
2. Check Laravel deployment guide: https://laravel.com/docs/11.x/deployment
3. Review deployment logs in Railway dashboard

