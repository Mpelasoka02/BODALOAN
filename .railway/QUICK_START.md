# 🚀 DEPLOY TO RAILWAY IN 2 MINUTES

## Copy-Paste Instructions (Do This Now!)

### ✅ STEP 1: Open Railway Dashboard
Go to: **https://railway.app/dashboard**

### ✅ STEP 2: Click Your BODALOAN Project

### ✅ STEP 3: Add PostgreSQL (if not already there)
1. Click "Add Service"
2. Select "Database" → "PostgreSQL"
3. Wait for it to create (takes 30 seconds)

### ✅ STEP 4: Open Variables Tab
1. Click the "BODALOAN" service (the web service)
2. Click "Variables" tab

### ✅ STEP 5: Copy-Paste These Variables

**Paste EXACTLY as shown below (one at a time in the variable fields):**

```
APP_NAME
Value: BodaLoan

APP_ENV
Value: production

APP_DEBUG
Value: false

APP_KEY
Value: base64:nUCKVU3DrmN1AYOgojq10UV6ojSFti6g5X648d7Qp/k=

APP_TIMEZONE
Value: Africa/Dar_es_Salaam

APP_URL
Value: https://bodaloan-production.up.railway.app

DB_CONNECTION
Value: pgsql

DB_HOST
Value: ${{PGHOST}}

DB_PORT
Value: ${{PGPORT}}

DB_DATABASE
Value: ${{PGDATABASE}}

DB_USERNAME
Value: ${{PGUSER}}

DB_PASSWORD
Value: ${{PGPASSWORD}}

LOG_LEVEL
Value: notice

SESSION_DRIVER
Value: file

CACHE_STORE
Value: file
```

### ✅ STEP 6: Click Deploy
1. Click "Deploy" button (or just wait - it auto-deploys)
2. Watch the Logs tab
3. Should show green checkmarks ✓

### ✅ STEP 7: Test
1. Click the URL that appears
2. Should see BodaLoan login page
3. Done! 🎉

---

## If 500 Error Appears:

**Problem:** Missing APP_KEY
**Solution:** Make sure APP_KEY is set to: `base64:nUCKVU3DrmN1AYOgojq10UV6ojSFti6g5X648d7Qp/k=`

**Problem:** Database not connecting
**Solution:** Make sure PostgreSQL is added and DB_* variables show values (not empty)

**Problem:** Still broken
**Go to:** Logs tab and look for error message

---

That's it! You're done in 2 minutes! 🚀
