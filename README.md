# සිංහල ප්‍රවෘත්ති (Sinhala News Website)

A complete, modern news website built with PHP, MySQL, and responsive design, featuring a comprehensive admin panel and database-driven content management.

## 🌟 Features

### Frontend Features
- **Responsive Design**: Mobile-first design with bottom navigation for mobile users
- **Sinhala Typography**: Full support for Sinhala language with Noto Sans Sinhala font
- **News Categories**: Politics, Sports, Technology, Business, Entertainment, Health
- **Search Functionality**: Database-driven search with highlighting
- **Social Sharing**: Share articles on Facebook, Twitter, WhatsApp with copy link
- **SEO Optimized**: Meta tags and structured data for better search rankings

### Backend Features
- **MySQL Database**: Complete database integration with normalized tables
- **Admin Panel**: Full content management system with role-based access
- **User Authentication**: Secure login system with password hashing
- **CRUD Operations**: Create, read, update, delete articles and categories
- **Security Features**: CSRF protection, SQL injection prevention, input sanitization

### Database Tables
- **Articles**: Title, content, summary, category, author, featured status, views
- **Categories**: Sinhala names, slugs, article counts
- **Users**: Admin, editor, author roles with authentication
- **Tags**: Multilingual tag system
- **Comments**: Article comments with moderation
- **Contact Messages**: Contact form submissions
- **Settings**: Site configuration options

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone or Download the Project**
   ```bash
   # If using git
   git clone <repository-url>
   cd sinhala-news-website

   # Or download and extract the ZIP file
   ```

2. **Configure Database**
   ```bash
   # Edit database configuration
   nano includes/config.php
   ```

   Update the database settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USERNAME', 'your_username');
   define('DB_PASSWORD', 'your_password');
   define('DB_NAME', 'sinhala_news');
   ```

3. **Set up Database**

   **Option A: Web Interface (Recommended)**
   - Start your web server
   - Visit: `http://localhost:8000/database/migrate.php`
   - Click "Run Database Migration"

   **Option B: Command Line**
   ```bash
   # Create database manually in MySQL
   mysql -u root -p
   CREATE DATABASE sinhala_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;

   # Import schema
   mysql -u root -p sinhala_news < database/schema.sql
   ```

4. **Start the Server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000

   # Or configure Apache/Nginx to point to the project directory
   ```

5. **Access the Website**
   - **Frontend**: `http://localhost:8000/index_db.php`
   - **Admin Panel**: `http://localhost:8000/admin_db.php`

## 🔐 Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Role**: Administrator (full access)

### Editor Account
- **Username**: `editor`
- **Password**: `admin123`
- **Role**: Editor (content management)

⚠️ **Important**: Change these default passwords immediately after first login!

## 📁 File Structure

```
sinhala-news-website/
├── includes/
│   ├── config.php          # Database configuration
│   └── Database.php        # Database connection class
├── database/
│   ├── schema.sql          # Database schema with sample data
│   └── migrate.php         # Migration and setup script
├── assets/
│   ├── css/style.css       # Main stylesheet
│   └── js/script.js        # JavaScript functionality
├── uploads/                # File upload directory
├── logs/                   # Security and error logs
├──
│ Frontend Files (Database-driven)
├── index_db.php            # Homepage with database
├── article_db.php          # Article display page
├── category_db.php         # Category listing page
├── search_db.php           # Search functionality
├──
│ Admin Panel Files
├── admin_db.php            # Admin login
├── admin-dashboard_db.php  # Admin dashboard
├── admin-add-article_db.php # Add new articles
├──
│ Static Pages
├── about.html              # About us page
├── contact.html            # Contact page
└── README.md               # This file
```

## 🛠️ Configuration

### Database Settings
Edit `includes/config.php` to configure:
- Database connection details
- Site URL and title
- Upload directories
- Security settings
- Pagination limits

### Site Customization
- **Site Title**: Update `SITE_TITLE` in `config.php`
- **Logo**: Replace the logo in the header
- **Colors**: Modify CSS variables in `assets/css/style.css`
- **Categories**: Add/edit categories through admin panel

## 🔧 Development

### Adding New Features

1. **Database Changes**
   - Add new tables/columns to `database/schema.sql`
   - Create migration scripts for existing installations

2. **Backend Development**
   - Add new methods to `includes/Database.php`
   - Follow the existing PHP file patterns for new pages

3. **Frontend Development**
   - Use the existing CSS classes and JavaScript functions
   - Maintain responsive design principles

### Security Best Practices
- Regular security updates
- Strong passwords for admin accounts
- HTTPS in production
- Regular database backups
- File upload restrictions

## 🚀 Deployment

### Production Deployment

1. **Server Requirements**
   - PHP 7.4+ with MySQLi extension
   - MySQL 5.7+ or MariaDB 10.2+
   - SSL certificate for HTTPS
   - At least 100MB disk space

2. **Security Hardening**
   ```bash
   # Set proper file permissions
   chmod 755 includes/
   chmod 644 includes/*.php
   chmod 777 uploads/
   chmod 777 logs/
   ```

3. **Production Configuration**
   ```php
   // In includes/config.php - disable debug mode
   error_reporting(0);
   ini_set('display_errors', 0);

   // Use production database credentials
   define('DB_HOST', 'your-production-host');
   define('DB_USERNAME', 'your-production-user');
   define('DB_PASSWORD', 'strong-production-password');
   ```

4. **SSL and HTTPS**
   - Configure SSL certificate
   - Update `SITE_URL` to use https://
   - Set up HTTP to HTTPS redirects

### Backup Strategy
- Daily database backups
- Weekly full file backups
- Test restore procedures regularly

## 📱 Mobile Optimization

The website is fully responsive with:
- Mobile-first CSS design
- Touch-friendly navigation
- Bottom navigation bar for mobile
- Optimized images and fonts
- Fast loading times

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 16+
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 📞 Support

For support and questions:
- Create an issue in the repository
- Email: admin@sinhalanews.lk
- Documentation: Check the inline comments in PHP files

## 🏆 Acknowledgments

- **Noto Sans Sinhala Font** by Google Fonts
- **PHP MySQLi** for database operations
- **Responsive Design** principles
- **Sinhala Unicode** support

---

**Version 4.0** - Complete MySQL Database Integration
**Last Updated**: 2025 July 13
**Status**: Production Ready ✅
