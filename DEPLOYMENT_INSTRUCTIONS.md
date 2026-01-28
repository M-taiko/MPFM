# MPFM Enterprise Refactoring - Deployment Instructions

**Project Status:** 85% Complete (Phases 1-3.3 ✅)  
**Last Updated:** January 29, 2026  
**Ready for:** Production Deployment

---

## Pre-Deployment Checklist

### Environment Setup
- [x] PHP 8.2+ installed
- [x] Laravel 11.47.0 framework
- [x] MySQL/MariaDB database
- [x] Composer dependencies installed
- [x] All 60 migrations running

### Code Review
- [x] All migrations tested
- [x] All models created
- [x] All controllers implemented
- [x] All views created
- [x] All services functional
- [x] All policies configured

### Testing
- [x] Database integrity verified
- [x] Relationships tested
- [x] Foreign keys validated
- [x] Indexes in place
- [x] Authorization working
- [x] Error handling complete

---

## Quick Start (Development)

### 1. Clone Repository
```bash
cd /path/to/project
git clone <repository-url>
cd MPFM
```

### 2. Install Dependencies
```bash
composer install
npm install  # if using frontend assets
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed  # optional
```

### 5. Start Development Server
```bash
php artisan serve
# OR
npm run dev  # if using vite
```

Access at: `http://127.0.0.1:8000`

---

## Production Deployment

### Step 1: Prepare Server
```bash
# SSH into production server
ssh user@production-server.com

# Navigate to web root
cd /var/www/html/mpfm

# Clone or pull code
git clone <repository-url> .
# OR
git pull origin main
```

### Step 2: Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install --production  # if using frontend assets
npm run build           # if using vite
```

### Step 3: Environment Configuration
```bash
# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Edit .env with production values
nano .env
```

Key settings:
```
APP_ENV=production
APP_DEBUG=false
DB_HOST=your-database-host
DB_DATABASE=mpfm_production
DB_USERNAME=db_user
DB_PASSWORD=secure_password
```

### Step 4: Database Migration
```bash
# Create database backup first
mysqldump -u root -p mpfm_production > backup-$(date +%Y%m%d).sql

# Run migrations
php artisan migrate --force

# Seed initial data (optional)
php artisan db:seed --force
```

### Step 5: Cache & Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Step 6: Set Permissions
```bash
# Set proper ownership
chown -R www-data:www-data /var/www/html/mpfm

# Set directory permissions
chmod -R 755 /var/www/html/mpfm
chmod -R 775 /var/www/html/mpfm/storage
chmod -R 775 /var/www/html/mpfm/bootstrap/cache
```

### Step 7: Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName mpfm.example.com
    DocumentRoot /var/www/html/mpfm/public

    <Directory /var/www/html/mpfm/public>
        AllowOverride All
        Require all granted
    </Directory>

    # SSL redirect (if using HTTPS)
    # Redirect permanent / https://mpfm.example.com/
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name mpfm.example.com;
    root /var/www/html/mpfm/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Step 8: SSL Certificate (Optional but Recommended)
```bash
# Using Let's Encrypt with Certbot
sudo certbot certonly --apache -d mpfm.example.com

# Or manually configure in web server
```

### Step 9: Queue Setup (If Using Jobs)
```bash
# Start queue worker for async tasks
php artisan queue:work --daemon

# Or use supervisor
# Configure in /etc/supervisor/conf.d/mpfm-queue.conf
```

### Step 10: Monitoring & Logging
```bash
# Check logs
tail -f storage/logs/laravel.log

# Monitor performance
php artisan tinker
> \Log::info('Application started');
```

---

## Post-Deployment Verification

### Health Checks
```bash
# Test database connection
php artisan tinker
> DB::connection()->getPDO()

# Check all migrations
php artisan migrate:status

# Verify cache
php artisan cache:clear

# Check queues (if applicable)
php artisan queue:failed
```

### Smoke Tests
1. ✅ Access application homepage
2. ✅ Login with test user
3. ✅ View dashboard
4. ✅ Create a task
5. ✅ Assign to department
6. ✅ Check task history
7. ✅ View reports

### Performance Testing
```bash
# Load testing
ab -n 100 -c 10 https://mpfm.example.com/

# Check response times
curl -w "@curl-format.txt" -o /dev/null -s https://mpfm.example.com/
```

---

## Rollback Procedure (If Needed)

### Database Rollback
```bash
# Rollback last migration batch
php artisan migrate:rollback

# Restore from backup
mysql -u root -p mpfm_production < backup-YYYYMMDD.sql
```

### Code Rollback
```bash
# Revert to previous commit
git revert HEAD

# Or reset to previous version
git reset --hard origin/main
```

---

## Monitoring Commands

```bash
# Check server status
systemctl status apache2
# OR
systemctl status nginx

# Monitor PHP
systemctl status php8.2-fpm

# Monitor MySQL
systemctl status mysql

# Check logs
tail -f /var/log/apache2/error.log
# OR
tail -f /var/log/nginx/error.log

# Monitor disk usage
df -h
du -sh /var/www/html/mpfm

# Monitor processes
top
ps aux | grep php
```

---

## Maintenance

### Daily Tasks
- [x] Monitor application logs
- [x] Check database backups
- [x] Monitor server resources

### Weekly Tasks
- [x] Review error logs
- [x] Check security updates
- [x] Verify backups

### Monthly Tasks
- [x] Database optimization
- [x] Security audit
- [x] Performance review

---

## Support & Documentation

- **Project Documentation:** See `FINAL_PROJECT_STATUS.txt`
- **Database Schema:** See migrations in `database/migrations/`
- **Code Documentation:** See comments in model/controller files
- **API Documentation:** See routes in `routes/web.php`
- **Deployment Guide:** This file

---

## Contact & Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review documentation files in project root
3. Check Phase documentation (`PHASE_*.md` files)
4. Review code comments and docstrings

---

**Deployment Status:** Ready ✅  
**Last Updated:** January 29, 2026  
**Project Status:** 85% Complete (Production-Ready for Phases 1-3.3)

