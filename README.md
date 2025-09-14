# One Way Interview Platform

A modern Laravel-based platform for conducting one-way video interviews, designed to streamline the recruitment process for companies and candidates.

## 🚀 Features

### For Administrators
- **User Management**: Create and manage admin, recruiter, and candidate accounts
- **Job Management**: Create, edit, and publish job listings with detailed requirements
- **Question Management**: Design interview questions with time limits and response types
- **System Monitoring**: Track applications, responses, and system performance

### For Recruiters
- **Dashboard**: Overview of assigned jobs and applications
- **Application Review**: View candidate applications and responses
- **Video Playback**: Secure video streaming of candidate responses
- **Rating System**: Rate individual responses and overall applications
- **Comments & Feedback**: Provide detailed feedback for candidates
- **Status Management**: Update application status (proceed/reject/hold)
- **Notifications**: Real-time alerts for new applications and updates

### For Candidates
- **Job Search**: Browse and apply for available positions
- **Video Recording**: Record video responses to interview questions
- **Time Management**: Built-in timers for each question
- **Application Tracking**: Monitor application status
- **Notifications**: Receive updates on application progress

## 🛠️ Technology Stack

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5, JavaScript (ES6+)
- **Database**: MySQL/PostgreSQL
- **Video Storage**: Local filesystem or AWS S3
- **Authentication**: Laravel Sanctum
- **File Upload**: Laravel Filesystem
- **Validation**: Custom validation services
- **Error Handling**: Comprehensive exception handling

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL or PostgreSQL
- Web Server (Apache/Nginx)
- SSL Certificate (for production)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/one-way-interview.git
cd one-way-interview
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Build frontend assets
npm run build
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Configure your `.env` file:

```env
APP_NAME="One Way Interview"
APP_ENV=production
APP_KEY=your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=one_way_interview
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# File Storage
FILESYSTEM_DRIVER=local
# For AWS S3:
# FILESYSTEM_DRIVER=s3
# AWS_ACCESS_KEY_ID=your-access-key
# AWS_SECRET_ACCESS_KEY=your-secret-key
# AWS_DEFAULT_REGION=your-region
# AWS_BUCKET=your-bucket-name

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE one_way_interview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### 5. Storage Link

```bash
# Create symbolic link for storage
php artisan storage:link
```

### 6. Web Server Configuration

#### Apache (.htaccess)

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/one-way-interview/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location /storage {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 7. Permission Setup

```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 8. Queue Worker (Optional)

For handling background jobs:

```bash
# Install Horizon for queue monitoring
composer require laravel/horizon

# Publish Horizon assets
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

# Start queue worker
php artisan queue:work
```

## 🎯 Usage Guide

### Administrator Setup

1. **Create Admin Account**:
   ```bash
   php artisan tinker
   >>> User::create([
   ...     'first_name' => 'Admin',
   ...     'last_name' => 'User',
   ...     'email' => 'admin@example.com',
   ...     'password' => Hash::make('password'),
   ...     'role' => 'admin'
   ... ]);
   ```

2. **Login**: Access `/login` with admin credentials

3. **Create Jobs**: Use the admin dashboard to create job listings

4. **Add Questions**: For each job, add interview questions with time limits

### Recruiter Workflow

1. **Create Recruiter Account**: Admin creates recruiter accounts
2. **Assign Jobs**: Admin assigns jobs to recruiters
3. **Review Applications**: Recruiters can view and rate applications
4. **Provide Feedback**: Add comments and ratings for responses
5. **Update Status**: Change application status as needed

### Candidate Experience

1. **Register**: Candidates create accounts via `/register`
2. **Browse Jobs**: View available job listings
3. **Apply**: Submit applications for desired positions
4. **Record Responses**: Answer interview questions via video
5. **Track Status**: Monitor application progress

## 🔧 Configuration

### Video Storage

#### Local Storage (Default)

```env
FILESYSTEM_DRIVER=local
```

#### AWS S3 Storage

```env
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.amazonaws.com
```

### Email Configuration

Configure SMTP settings for notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
```

### Security Settings

```env
# Session security
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true

# Cookie settings
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=.your-domain.com
```

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter=JobTest

# Run with coverage
php artisan test --coverage
```

## 📊 Monitoring

### Application Monitoring

```bash
# View application logs
tail -f storage/logs/laravel.log

# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear
```

### Database Monitoring

```bash
# Check database status
php artisan db:show

# Backup database
php artisan db:backup
```

## 🚀 Deployment

### Production Deployment Checklist

1. [ ] Set `APP_ENV=production` in `.env`
2. [ ] Set `APP_DEBUG=false` in `.env`
3. [ ] Configure production database
4. [ ] Set up SSL certificate
5. [ ] Configure email settings
6. [ ] Set up file storage (S3 recommended)
7. [ ] Run `php artisan config:cache`
8. [ ] Run `php artisan route:cache`
9. [ ] Run `php artisan view:cache`
10. [ ] Set up queue worker for production
11. [ ] Configure backup system
12. [ ] Set up monitoring and alerts

### Deployment Commands

```bash
# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# Clear and cache configurations
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Restart queue workers
php artisan queue:restart
```

## 🔍 Troubleshooting

### Common Issues

#### 1. Video Upload Fails

**Solution**: Check file permissions and storage configuration
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

#### 2. Email Not Sending

**Solution**: Verify SMTP configuration in `.env` file
```bash
php artisan tinker
>>> Mail::raw('Test email', function($message) { $message->to('test@example.com'); });
```

#### 3. Queue Jobs Not Processing

**Solution**: Start queue worker
```bash
php artisan queue:work --tries=3 --timeout=90
```

#### 4. Permission Denied Errors

**Solution**: Set proper permissions
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Debug Mode

Enable debug mode for development:

```env
APP_DEBUG=true
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup

```bash
# Install development dependencies
composer install
npm install

# Run development server
php artisan serve

# Run asset compilation in watch mode
npm run watch
