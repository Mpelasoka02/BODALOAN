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

## Required Environment Variables

Set these variables in your Railway project dashboard:

### Application Configuration
- `APP_NAME=BodaLoan`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=` (Your base64-encoded key - use: `php artisan key:generate`)
- `APP_TIMEZONE=Africa/Dar_es_Salaam`
- `APP_URL=` (Your Railway app URL, e.g., https://bodaloan-production.up.railway.app)

### Database (PostgreSQL - provided by Railway)
- `DB_CONNECTION=pgsql`
- `DB_HOST=${{PGHOST}}`
- `DB_PORT=${{PGPORT}}`
- `DB_DATABASE=${{PGDATABASE}}`
- `DB_USERNAME=${{PGUSER}}`
- `DB_PASSWORD=${{PGPASSWORD}}`

### Logging & Cache
- `LOG_CHANNEL=stack`
- `LOG_LEVEL=notice`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=database`

### External Services
- `FIREBASE_PROJECT_ID=` (Your Firebase project ID)
- `FIREBASE_DATABASE_URL=` (Your Firebase realtime DB URL)
- `FIREBASE_API_KEY=` (Your Firebase API key)
- `FIREBASE_APP_ID=` (Your Firebase app ID)
- `FIREBASE_FCM_SENDER_ID=` (Firebase Cloud Messaging sender ID)
- `FIREBASE_VAPID_KEY=` (Firebase VAPID key for push notifications)
- `FIREBASE_FCM_ENABLED=true`
- `FIREBASE_REALTIME_DB_ENABLED=true`
- `SMS_DRIVER=` (log or actual SMS provider)
- `SMS_AFRICASTALKING_USERNAME=` (Africa's Talking account)
- `SMS_AFRICASTALKING_API_KEY=` (Africa's Talking API key)
- `SMS_SENDER_ID=BodaLoan`
- `MAIL_MAILER=log` (or configure actual email service)
- `GOOGLE_MAPS_API_KEY=` (Your Google Maps API key)
- `TRACCAR_URL=` (Your Traccar server URL)
- `TRACCAR_EMAIL=` (Traccar admin email)
- `TRACCAR_PASSWORD=` (Traccar admin password)

## Deployment Steps

### 1. Set Up Railway Project

1. Go to https://railway.app and create a new project
2. Connect your GitHub repository
3. Add a PostgreSQL database to the project
4. Configure environment variables in the Railway dashboard

### 2. Push to GitHub

```bash
git add .
git commit -m "Add Railway deployment configuration (Procfile, railway.json, .railway/)"
git push origin main
```

### 3. Railway Auto-Deployment

Once pushed to GitHub, Railway will automatically:
1. Build your application
2. Run database migrations (`php artisan migrate --force`)
3. Start the Apache server with PHP

## Troubleshooting

### 500 Server Error

1. Check Railway logs in the dashboard
2. Verify all required environment variables are set
3. Ensure `APP_KEY` is properly configured
4. Check database migrations ran successfully

### Database Connection Issues

1. Ensure PostgreSQL service is connected to your project
2. Verify `DB_*` variables match the PostgreSQL service
3. Check that migrations completed: `ph artisan migrate:status`

### Missing Application Key

Generate a new key locally and set it in Railway:
```bash
php artisan key:generate
```

Then copy the `APP_KEY` value to your Railway environment variables.

## Manual Deployment (if needed)

Connect to your Railway app via Heroku CLI:
```bash
heroku git:remote -a your-app-name
git push heroku main
```

## Production Checklist

- [ ] Database migrations run successfully
- [ ] APP_KEY is set and correct
- [ ] All external service credentials are configured
- [ ] APP_DEBUG is set to false
- [ ] LOG_LEVEL is set to notice (not debug)
- [ ] MAIL_MAILER is configured (log for testing, real service for production)
- [ ] Storage directory permissions are correct
- [ ] Session and cache drivers are configured
