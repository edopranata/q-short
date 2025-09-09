# Q-Shorten - URL Shortening Application

A comprehensive URL shortening application built with Laravel 11, Vue.js 3, and TailwindCSS, featuring role-based access control and detailed analytics.

## Features

### 🔗 URL Management
- **URL Shortening**: Convert long URLs into short, memorable links
- **Custom Slug Support**: Create memorable custom slugs for your URLs (e.g., yoursite.com/my-portfolio)
- **Real-time Slug Validation**: Check slug availability as you type
- **Custom Titles & Descriptions**: Add metadata to your shortened URLs
- **Expiration Control**: Set expiration dates for time-sensitive campaigns
- **Active/Inactive Status**: Enable or disable URLs as needed
- **Bulk Management**: Efficiently manage multiple URLs

### 📊 Analytics & Tracking
- **Click Tracking**: Real-time click counting and analytics
- **Geographic Data**: Track clicks by country and city
- **Device Analytics**: Monitor desktop, mobile, and tablet usage
- **Browser & Platform Stats**: Detailed browser and OS information
- **Referrer Tracking**: See where your traffic is coming from
- **Daily Charts**: Visual representation of click patterns

### 👥 User Management & Security
- **Role-Based Access Control**: Admin and User roles with specific permissions
- **Spatie Laravel-Permission**: Robust permission management system
- **Disabled Public Registration**: Only admins can create new accounts
- **User Isolation**: Users can only manage their own URLs
- **Admin Override**: Admins can view and manage all URLs

### 🎨 Modern UI/UX
- **TailwindCSS**: Beautiful, responsive design
- **Vue.js 3 + Inertia.js**: Smooth, SPA-like experience
- **Mobile-First**: Fully responsive across all devices
- **Intuitive Navigation**: Clean and user-friendly interface
- **Real-time Updates**: Dynamic content updates

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 with Inertia.js
- **Styling**: TailwindCSS
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel-Permission
- **Database**: SQLite (configurable)
- **User Agent Detection**: Jenssegers Agent
- **Testing**: PHPUnit with Feature and Unit tests

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or your preferred database)

### Setup Instructions

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install --legacy-peer-deps
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

5. **Build frontend assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Users

After running the seeders, you'll have access to:

- **Admin User**
  - Email: `admin@example.com`
  - Password: `password`
  - Role: Admin (full access)

- **Test User**
  - Email: `user@example.com`
  - Password: `password`
  - Role: User (limited access)

## Usage

### Creating Short URLs
1. Log in to your account
2. Navigate to "My URLs" or click "Create Short URL"
3. Enter the original URL and optional metadata
4. **Optional**: Enter a custom slug for a memorable URL (e.g., "my-portfolio")
5. Set expiration date if needed
6. Click "Create Short URL"

**Custom Slug Features:**
- Real-time availability checking
- Format validation (letters, numbers, hyphens only)
- Reserved slug protection (admin, api, etc.)
- Unique constraint enforcement

### Viewing Analytics
1. Go to the "Analytics" section
2. View overall statistics on the dashboard
3. Click on any URL to see detailed analytics
4. Analyze click patterns, geographic data, and device information

### Managing URLs
1. Visit "My URLs" to see all your shortened links
2. Edit titles, descriptions, and settings
3. Toggle active/inactive status
4. Delete URLs when no longer needed

## API Endpoints

### Public Routes
- `GET /s/{shortCode}` - Redirect to original URL (with analytics tracking)
- `GET /s/{customSlug}` - Redirect using custom slug (with analytics tracking)

### Authenticated Routes
- `GET /urls` - List user's URLs
- `POST /urls` - Create new shortened URL (supports custom slug)
- `GET /urls/{id}` - View URL details
- `PUT /urls/{id}` - Update URL
- `DELETE /urls/{id}` - Delete URL
- `GET /analytics` - Analytics dashboard
- `GET /analytics/{id}` - Detailed URL analytics
- `POST /api/check-slug` - Check custom slug availability

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test files
php artisan test --filter=ShortenedUrlTest
php artisan test --filter=AnalyticsTest

# Test custom slug functionality
php artisan test --filter="custom slug"
```

## Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **SQL Injection Prevention**: Eloquent ORM with parameter binding
- **XSS Protection**: Input sanitization and output escaping
- **Authentication Required**: All URL management requires authentication
- **Authorization Checks**: Users can only access their own resources
- **Rate Limiting**: Built-in Laravel rate limiting

## Performance Optimizations

- **Database Indexing**: Optimized indexes for fast lookups
- **Eager Loading**: Efficient relationship loading
- **Caching**: Permission and role caching
- **Asset Optimization**: Minified CSS and JS in production
- **Lazy Loading**: Vue components loaded on demand

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Built with ❤️ using Laravel, Vue.js, and TailwindCSS**
