# Quick Setup Guide

This guide provides a quick setup process for the One Way Interview Platform.

## Prerequisites

- PHP 8.1+
- MySQL/PostgreSQL
- Composer
- Node.js & NPM
- Web Server (Apache/Nginx)

## 1-Click Setup Script

Create a setup script for automated installation:

```bash
#!/bin/bash

# One Way Interview Platform Setup Script
# Run this script with: bash setup.sh

set -e

echo "🚀 Starting One Way Interview Platform Setup..."

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install composer first."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ NPM is not installed. Please install Node.js and NPM first."
    exit 1
fi

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install --production

# Build frontend assets
echo "🔨 Building frontend assets..."
npm run build

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating environment file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ Environment file created. Please update .env with your database credentials."
else
    echo "✅ Environment file already exists."
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Set permissions
echo "🔐 Setting file permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "✅ Setup completed successfully!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with database credentials"
echo "2. Create your database: mysql -u root -p -e 'CREATE DATABASE one_way_interview;'"
echo "3. Run migrations: php artisan migrate"
echo "4. (Optional) Seed database: php artisan db:seed"
echo "5. Start the development server: php artisan serve"
echo ""
echo "🎉 Your One Way Interview Platform is ready!"
```

Save this as `setup.sh` and run:
```bash
chmod +x setup.sh
bash setup.sh
```

## Manual Setup

### Step 1: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

### Step 2: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database and email settings.

### Step 3: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE one_way_interview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Optional: Seed database
php artisan db:seed
```

### Step 4: Final Setup
```bash
# Create storage link
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 5: Development Server
```bash
php artisan serve
```

Access the application at: http://localhost:8000

## First User Setup

### Create Admin User
```bash
php artisan tinker
```

```php
User::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
exit
```

### Login
- URL: http://localhost:8000/login
- Email: admin@example.com
- Password: password

## Production Deployment

### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=one_way_interview
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
```

### Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Queue Worker (Optional)
```bash
# Install Horizon
composer require laravel/horizon
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

# Start queue worker
php artisan queue:work --tries=3 --timeout=90
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **Cache Issues**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Database Connection Issues**
   - Verify database credentials in `.env`
   - Ensure database server is running
   - Check database exists

4. **Storage Issues**
   ```bash
   php artisan storage:link
   ```

### Log Files
Check application logs for debugging:
```bash
tail -f storage/logs/laravel.log
```

## Support

For additional support:
- Check the full documentation in `README.md`
- Review troubleshooting section
- Create an issue on GitHub

---

Happy interviewing! 🎥
