# Super Studio Project - Photography Studio Management System

A comprehensive Laravel-based web application for managing a photography studio business, featuring client galleries, appointment scheduling, blog management, and administrative tools.

## 🎯 Project Overview

**Super Studio Project** is a full-featured photography studio management system built with Laravel 12, Livewire 3, and modern web technologies. It provides a complete solution for photography studios to manage their business operations, client galleries, appointments, and content marketing.

## ✨ Key Features

### 📸 **Photo & Video Management**
- **Album System**: Create and manage photo albums with different privacy levels (public, private, client-specific)
- **Photo Upload**: Bulk photo upload with automatic thumbnail generation
- **Video Support**: Upload and manage video content with thumbnails
- **Gallery Views**: Interactive photo galleries with lightbox functionality
- **Photo Liking System**: Users can like photos and view their favorites
- **Grid Galleries**: Curated photo collections for showcasing work

### 📅 **Appointment Scheduling**
- **Smart Booking System**: Real-time availability checking with business hours configuration
- **Service Management**: Multiple service types with different durations and pricing
- **Guest Booking**: Allow non-registered users to book appointments
- **Email Notifications**: Automatic confirmation emails to clients and admin notifications
- **Business Hours Configuration**: Flexible scheduling with lunch breaks and disabled dates
- **Admin Controls**: Administrators can manage business hours and availability

### 👥 **User Management & Authentication**
- **Role-Based Access**: Admin, user, and guest roles with different permissions
- **User Impersonation**: Admins can impersonate users for support purposes
- **Profile Management**: User profiles with photo uploads
- **Two-Factor Authentication**: Enhanced security with 2FA support

### 🎨 **Content Management**
- **Blog System**: Complete blog with categories, comments, and likes
- **Image Management**: Multiple image support for blog posts
- **Content Cards**: Dynamic content sections for homepage
- **Hero Sections**: Customizable hero blocks for different pages
- **Carousel Management**: Image carousels for homepage and galleries

### 🌟 **Marketing & Reviews**
- **Google Reviews Integration**: Automatic fetching and display of Google reviews
- **Review Slider**: Interactive review carousel with sorting options
- **SEO Optimization**: Meta tags and structured content for search engines
- **Contact Forms**: Multiple contact forms for different purposes

### 🛠 **Administrative Features**
- **Dashboard**: Comprehensive admin dashboard with statistics
- **User Management**: Manage users, roles, and permissions
- **Content Management**: Manage all content types from admin panel
- **File Management**: S3-based file storage with organized structure
- **Email Campaigns**: Email marketing system with recipient management

## 🏗️ Technical Architecture

### **Backend Framework**
- **Laravel 12**: Latest Laravel framework with modern PHP 8.2+ features
- **Livewire 3**: Full-stack framework for dynamic user interfaces
- **Laravel Jetstream**: Authentication scaffolding with team management
- **Laravel Fortify**: Backend authentication services

### **Database & Storage**
- **SQLite**: Development database (easily configurable for production)
- **AWS S3**: Cloud storage for all media files with organized folder structure
- **Queue System**: Background job processing for image thumbnails
- **Database Migrations**: Comprehensive schema with 50+ migrations

### **Frontend Technologies**
- **Tailwind CSS**: Utility-first CSS framework for styling
- **Alpine.js**: Lightweight JavaScript framework (via Livewire)
- **Vite**: Modern build tool for asset compilation
- **GSAP**: Animation library for smooth user interactions
- **PhotoSwipe**: Lightbox library for image viewing
- **Swiper**: Touch slider library for carousels

### **Third-Party Integrations**
- **AWS SDK**: Complete AWS S3 integration for file storage
- **Google API Client**: Google Reviews integration
- **Intervention Image**: Advanced image processing and manipulation
- **Laravel FFmpeg**: Video processing capabilities
- **Resend**: Email delivery service
- **Spatie Permissions**: Role and permission management

## 📁 Project Structure

```
super_studio_project/
├── app/
│   ├── Http/Controllers/          # API and web controllers
│   ├── Livewire/                  # Livewire components
│   │   ├── Admin/                # Admin-specific components
│   │   ├── Homepage/             # Homepage components
│   │   └── Forms/                # Form components
│   ├── Models/                   # Eloquent models
│   ├── Jobs/                     # Background job classes
│   ├── Mail/                     # Email templates and classes
│   └── Services/                 # Business logic services
├── database/
│   ├── migrations/               # Database schema migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories for testing
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript files
├── public/                       # Public assets and build files
├── storage/                      # File storage and logs
└── config/                       # Application configuration
```

## 🚀 Installation & Setup

### **Prerequisites**
- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite (or MySQL/PostgreSQL for production)
- AWS S3 account (for file storage)

### **Installation Steps**

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd super_studio_project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure environment variables**
   ```env
   # Database
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/database.sqlite
   
   # AWS S3 Configuration
   AWS_ACCESS_KEY_ID=your_access_key
   AWS_SECRET_ACCESS_KEY=your_secret_key
   AWS_DEFAULT_REGION=your_region
   AWS_BUCKET=your_bucket_name
   AWS_URL=your_s3_url
   
   # Mail Configuration
   MAIL_MAILER=resend
   RESEND_KEY=your_resend_key
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Your Studio Name"
   
   # Google Reviews
   GOOGLE_REVIEW_LINK=your_google_reviews_link
   ```

6. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the application**
   ```bash
   php artisan serve
   ```

## 🔧 Configuration

### **File Storage Configuration**
The application uses multiple S3 disks for organized file storage:
- `albums`: Photo and video storage
- `blog-media`: Blog post images
- `hero-home`: Homepage hero images
- `content-cards`: Content card images
- `logos`: Logo and branding files
- `page-headers`: Page header images
- `info-blocks`: Information block images

### **Business Settings**
Configure business hours and availability through the admin panel:
- Opening and closing hours
- Lunch break configuration
- Disabled dates
- Daily-specific hours
- Service durations and pricing

### **Email Configuration**
The system supports multiple email providers:
- Resend (recommended)
- SMTP
- Amazon SES
- Postmark
- Mailgun

## 📊 Database Schema

### **Core Tables**
- `users`: User accounts with role-based access
- `albums`: Photo/video collections
- `photos`: Individual photo records
- `videos`: Video content records
- `superappointments`: Appointment bookings
- `blog_posts`: Blog content
- `google_reviews`: Customer reviews
- `business_settings`: Business configuration

### **Key Relationships**
- Users can create multiple albums
- Albums contain multiple photos and videos
- Users can like photos (many-to-many)
- Appointments belong to users (optional for guests)
- Blog posts have categories and multiple images

## 🎨 User Interface

### **Public Pages**
- **Homepage**: Hero section, carousel, content cards
- **Weddings**: Wedding photography showcase
- **Videos**: Video gallery and showcase
- **Blog**: Article listing and individual posts
- **Comuniones**: First communion photography
- **Fotocarnet**: ID photo services
- **Studio**: Creative studio content

### **Client Dashboard**
- **Albums**: View assigned photo albums
- **Favorites**: Liked photos collection
- **Appointments**: Booking and management

### **Admin Dashboard**
- **User Management**: Manage clients and permissions
- **Content Management**: Blog, galleries, carousels
- **Appointment Management**: View and manage bookings
- **Settings**: Business configuration
- **Analytics**: Usage statistics and insights

## 🔒 Security Features

- **Role-based Access Control**: Granular permissions system
- **Two-Factor Authentication**: Enhanced account security
- **CSRF Protection**: Cross-site request forgery prevention
- **SQL Injection Prevention**: Parameterized queries
- **File Upload Security**: Validated file types and sizes
- **User Impersonation**: Secure admin user switching

## 📱 Responsive Design

The application is fully responsive and optimized for:
- Desktop computers
- Tablets
- Mobile phones
- Various screen sizes and orientations

## 🚀 Performance Optimizations

- **Image Optimization**: Automatic thumbnail generation
- **Lazy Loading**: Images load as needed
- **Caching**: Computed properties and database queries
- **CDN Integration**: AWS S3 for fast file delivery
- **Queue Processing**: Background job processing
- **Database Indexing**: Optimized database queries

## 🧪 Testing

The project includes comprehensive testing setup:
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **Database Factories**: Test data generation
- **Model Factories**: User and content generation

## 📈 Scalability

The application is designed for scalability:
- **Cloud Storage**: AWS S3 for unlimited file storage
- **Queue System**: Background processing for heavy tasks
- **Database Optimization**: Efficient queries and indexing
- **Caching Strategy**: Multiple caching layers
- **Modular Architecture**: Easy to extend and maintain


## 🔄 Background Jobs

The system uses Laravel's queue system for:
- **Photo Thumbnail Generation**: Automatic thumbnail creation
- **Email Sending**: Asynchronous email delivery
- **Image Processing**: Heavy image manipulation tasks
- **Data Synchronization**: External API integrations

## 📧 Email System

Comprehensive email functionality:
- **Appointment Confirmations**: Automatic booking confirmations
- **Admin Notifications**: New appointment alerts
- **Contact Form Notifications**: Lead generation alerts
- **Blog Notifications**: Content update alerts
- **Marketing Campaigns**: Email marketing system

## 🌐 SEO Features

Built-in SEO optimization:
- **Meta Tags**: Dynamic meta descriptions and titles
- **Structured Data**: Rich snippets for search engines
- **URL Optimization**: Clean, SEO-friendly URLs
- **Image Alt Tags**: Accessibility and SEO
- **Sitemap Generation**: Automatic sitemap creation

## 🛠️ Development Tools

- **Laravel Pint**: Code formatting
- **Laravel Pail**: Log viewing
- **Laravel Tinker**: Interactive shell
- **IDE Helper**: Enhanced IDE support
- **Debug Tools**: Comprehensive debugging utilities

## 📋 API Endpoints

The application provides RESTful API endpoints for:
- User authentication and management
- Album and photo management
- Appointment booking
- Blog content access
- Admin operations

## 🔧 Customization

The system is highly customizable:
- **Themes**: Easy theme switching
- **Content Management**: Dynamic content sections
- **Business Logic**: Configurable business rules
- **Email Templates**: Customizable email designs
- **User Interface**: Flexible component system

## 📚 Documentation

- **Code Documentation**: Comprehensive inline documentation
- **API Documentation**: Detailed endpoint documentation
- **User Guides**: Step-by-step user instructions
- **Admin Guides**: Administrative operation guides

## 🤝 Contributing

This is a private project, but the codebase follows Laravel best practices and is well-documented for future maintenance and enhancement.

## 📄 License

This project is proprietary software developed for internal use.

## 🆘 Support

For technical support or questions about the system, please contact the development team.

---

**Super Studio Project** - A complete photography studio management solution built with modern web technologies and best practices.
