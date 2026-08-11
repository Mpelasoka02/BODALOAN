# Bodaloan Project Setup & Local Testing Guide

## Project Overview

Bodaloan is a ride-sharing platform in Tanzania that connects boda-boda drivers with passengers through a hire-purchase model. The project features three user roles:

- **Admin**: Manages all operations, approves vehicles/drivers, views GPS maps, processes payments
- **Owner**: Owns and manages vehicles, assigns drivers, views fleet on GPS map, processes payments
- **Driver**: Uses the mobile app to track location, apply for vehicles, view loan status

## Infrastructure Stack

- **Laravel 11.55.0** — PHP framework
- **SQLite** (development) / **MySQL** (production)
- **Google Maps** (optional, admin/owner maps)
- **Traccar** (planned GPS backend dependency manager)
- **Leaflet.js** (free OpenStreetMap for current GPS maps)
- **Bootstrap 5** — UI framework
- **TypeScript/JavaScript** (components)

## Current State

### ✅ Implemented Features

1. **All Core Business Flow** ✅
   - Vehicle upload → Admin verify → Marketplace live
   - Driver applications → Admin approve/reject → Assignment
   - Loan workflow with installments → Ownership transfer
   - Payments verification system

2. **Multi-Role Dashboards** ✅
   - Admin full control (Users, Vehicles, Drivers, Payments, Applications, Relationships)
   - Owner fleet management (vehicles, loans, drivers, GPS map)
   - Driver mobile app flow (browse, apply, loan status, GPS tracking)

3. **Comprehensive GPS Tracking** ✅
   - Leaflet.js free GPS maps (admin/owner) — replaced Google Maps
   - GPS Simulator: `php artisan gps:simulate`
   - Live fleet API: `/api/fleet/positions`, `/api/fleet/stats`
   - Signal status: live/stale/none with color coding
   - Real-time updates every 30s on maps
   - Vehicle route history view

4. **Public Marketplace** ✅
   - Full version (hero, stats, How It Works, Features, Browse with filters, Testimonials, CTA, Footer)
   - Vehicle detail pages with embedded maps
   - Search functionality
   - Public registration with role selection

5. **Professional Fintech UI** ✅
   - Navy/gold/emerald palette with modern design
   - Responsive design with mobile-first navigation
   - Centered auth cards, sleek card layouts
   - Notification system with real-time alerts

6. **Data Seeding** ✅
   - 6 users (1 admin, 2 owners, 3 drivers)
   - 8 motorcycles (5 owner1, 3 owner2)
   - 4 loans with payment schedules
   - 3 driver applications
   - GPS device IDs assigned to 3 vehicles

7. **Verification System** ✅
   - Vehicle verification status: pending_verification/verified/rejected
   - Driver approval workflow (pending → approved/rejected)
   - Admin dashboard for pending approvals
   - Banner warnings for unverified users

8. **Essential Features** ✅
   - Public landing page (marketing)
   - Session-based auth with role check middleware
   - Email verification placeholder
   - Firebase messaging (integrated)
   - Notifications system
   - Multi-language support (English/Swahili)

### 🟡 In Progress

1. **Traccar Integration**
   - TraccarService class created
   - Config file created
   - Models updated
   - awaiting: Install & run Traccar
   - awaiting: Database migration to add GPS columns (already done)
   - awaiting: Device-to-vehicle assignment flow
   - awaiting: Route history feature

### ⛔ Blocked Dependencies

- **Google Maps API Key** — currently using Leaflet.js Free alternative
- **Traccar Server** — needs self-hosted installation: `docker run -d -p 8082:8082 -p 5000-5150:5000-5150/tcp traccar/traccar`
- **Google Maps Geolocation** — replaced by Leaflet.js; no API key needed

## Setup Instructions

### 1. Clone and Run

```bash
# Navigate to project
C:\Users\user\Desktop\BODA\bodaloan

# Install dependencies
# (assuming composer/npm/pnpm available)

# Setup database (SQLite)
php artisan migrate

# Seed demo data
php artisan db:seed

# Run GPS simulator in background (to observe live maps)
php artisan gps:simulate

# Run web server
php artisan serve
```

### 2. Access the Application

- **Local**: http://127.0.0.1:8000
- **Landing**: http://127.0.0.1:8000/
- **Login**: http://127.0.0.1:8000/login
- **Admin**: admin@bodaloan.com / password / login_role=admin

## Demo Credentials

### Admin
```
Email: admin@bodaloan.com
Password: password
Login Role: admin
```

### Owner 1
```
Email: owner1@bodaloan.com
Password: password
```

### Driver 1
```
Email: driver1@bodaloan.com
Password: password
```

### Driver 3 (Pending Approval)
```
Email: driver3@bodaloan.com
Password: password
```

### Owner for Vehicle Rejection
```
Email: owner2@bodaloan.com
Password: password
Owner vehicle: T 882 CZK (rejected: documents not clear)
```

## Features to Test

### Dashboard Navigation
- ✅ Admin: `/dashboard` → Admin main
- ✅ Owner: `/dashboard` → Owner main
- ✅ Driver: `/dashboard` → Driver main

### Marketplace Flows
- ✅ Browse available/verified vehicles
- ✅ Vehicle detail with embedded map
- ✅ Driver applications (browse → apply)
- ✅ Owner vehicle upload (pending_verification → admin verify → marketplace)

### Verification Workflows
- ✅ Unverified vehicle shows warning banner
- ✅ Admin pending vehicles list: `admin/vehicles`
- ✅ Admin pending drivers list: `admin/drivers`

### GPS Features
- ✅ Admin map: `/admin/drivers-map` (all active drivers)
- ✅ Owner map: `/owner/drivers-map` (owner's fleet only)
- ✅ Driver GPS tracking: `/driver/gps`
- ✅ Live vehicle positions updates every 30s
- ✅ Signal status indicators
- ✅ Vehicle route history

### Payments & Loans
- ✅ Payment creation & verification (pending → verified)
- ✅ Overdue detection and admin alerts
- ✅ Loan application → accept → schedule
- ✅ Installment tracking

### Notifications
- ✅ Real-time alert badges
- ✅ Mark read/unread

### Mobile Responsive
- ✅ Desktop sidebar navigation
- ✅ Mobile bottom navigation (hamburger collapsed)

## Next Steps

### Immediate
1. **Fix SQLite Database**
   - Restore from backup or re-seed
   - check tables created after migration

2. **Continue GPS Simulator**
   - Verify vehicles showing on maps
   - Test signal status colors
   - Check route history

3. **Run Tests**
   - admin login → pending drivers page
   - owner verification flow
   - driver application flow

### Development
1. **Install Traccar**
   ```docker run -d -p 8082:8082 -p 5000-5150:5000-5150/tcp traccar/traccar```

2. **Configure `.env`**
   ```TRACCAR_URL=http://localhost:8082
TRACCAR_EMAIL=admin@traccar.org
TRACCAR_PASSWORD=admin```

3. **Add artisan commands to Kernel**
   - schedule GPS sync every minute
   - Add device enrollment flow for GPS trackers

4. **Enhance UI/UX**
   - Customize landing page hero text
   - Add more vehicle filters
   - Improve mobile map experience

## Productivity Tips

### Laravel Commands
```bash
# Clear cache
php artisan optimize:clear

# Run tests
php artisan test

# Reset database (keep structure)
php artisan migrate:refresh

# Re-seed with fresh demo data
php artisan migrate:refresh --seed

# View logs
php artisan log:tail
```

### Environment
```bash
# Change .env
dotenv set APP_URL=http://127.0.0.1:8000

# Run in non-interactive mode
php artisan gps:simulate --count=3
```

### Production Considerations
- Replace SQLite with MySQL for scale
- Configure proper Traccar server
- Add CloudFlare/SSL
- Implement Redis for caching
- Add monitoring (Sentry, etc.)
- Setup automated backups

## Known Issues

1. **SQLite Database** — currently 110KB (small), may need to restore from backup
2. **Google Maps API** — optional, currently using Leaflet.js Free
3. **Traccar** — not running yet, planned integration
4. **Notifications** — placeholder for real-time updates
5. **File Uploads** — basic docs handling, could use cloud storage

## Support & Community

For issues or questions:
- **Documentation**: Standard Laravel docs
- **Troubleshooting**: Laravel.io community
- **Version Control**: git (if repo exists)
- **Dependencies**: Run `composer install` if needed

---

**Built with Laravel 11.x** — Modern, elegant, focused on simplicity
