# RetailRow — Dynamic eCommerce Platform

A full-stack eCommerce platform built with PHP, MySQL, and dynamic frontend loading.

## 🚀 Features

- **Dynamic Content Management**: All content controlled by admin dashboard
- **Real-time Updates**: Changes in admin reflect instantly on frontend
- **MVC-like Architecture**: Clean separation of concerns
- **Secure Admin Panel**: Role-based access with session management
- **RESTful API**: JSON endpoints for all data
- **Responsive Design**: Mobile-first approach
- **Flash Sales**: Time-limited discount system
- **Product Management**: Full CRUD operations

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+ (OOP)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **API**: RESTful JSON endpoints
- **Security**: Prepared statements, CSRF protection, password hashing

## 📁 Project Structure

```
retailrow/
├── index.php                 # Dynamic homepage
├── category.php             # Category pages
├── product.php              # Product detail pages
├── cart.php                 # Shopping cart
├── checkout.php             # Checkout process
├── api/                     # REST API endpoints
│   ├── categories.php
│   ├── products.php
│   ├── banners.php
│   ├── flash-sales.php
│   └── settings.php
├── admin/                   # Admin panel
│   ├── login.php
│   ├── dashboard.php
│   ├── products/
│   ├── categories/
│   └── ...
├── config/                  # Configuration
│   ├── db.php              # Database connection
│   └── auth.php            # Authentication
├── assets/                  # Static assets
│   ├── css/
│   ├── js/
│   └── uploads/            # User uploaded files
└── db.sql                  # Database schema
```

## 🗄️ Database Setup

1. Create a MySQL database named `retailrow`
2. Import `db.sql` to create tables and sample data
3. Update database credentials in `config/db.php`

```sql
-- Default admin login:
-- Email: admin@retailrow.com
-- Password: admin123
```

## 🔧 Installation

1. **Clone/Download** the project to your XAMPP htdocs folder
2. **Database Setup**:
   - Create database `retailrow`
   - Import `db.sql`
3. **Configuration**:
   - Update `config/db.php` with your database credentials
4. **File Permissions**:
   - Ensure `assets/uploads/` is writable
5. **Access**:
   - Frontend: `http://localhost/retailrow/`
   - Admin: `http://localhost/retailrow/admin/`

## 🎯 Admin Features

### Dashboard
- Overview statistics
- Quick access to all management sections

### Products Management
- Add/Edit/Delete products
- Image upload
- Category assignment
- Price management
- Stock control
- Featured product toggle

### Categories Management
- Create product categories
- Upload category icons
- Set display order
- Enable/disable categories

### Banners Management
- Hero carousel banners
- Image upload
- Link management
- Position ordering

### Flash Sales
- Create time-limited discounts
- Product selection
- Countdown timers
- Automatic expiration

### Settings
- Site configuration
- Announcement text
- Contact information
- Promo messages

## 🔌 API Endpoints

All endpoints return JSON responses:

- `GET /api/categories.php` - Get all categories
- `GET /api/products.php?type=featured` - Get featured products
- `GET /api/banners.php` - Get active banners
- `GET /api/flash-sales.php` - Get active flash sales
- `GET /api/settings.php?keys=key1,key2` - Get settings

## 🔒 Security Features

- Password hashing with `password_hash()`
- Prepared statements for all database queries
- CSRF token protection for admin forms
- Session-based authentication
- Role-based access control
- File upload validation

## 📱 Responsive Design

- Desktop-first approach
- Mobile breakpoints: 576px, 768px, 992px, 1200px
- Touch-friendly interactions
- Optimized for all screen sizes

## 🚀 Future Enhancements

- User registration and login
- Order management system
- Payment gateway integration
- Email notifications
- Advanced search and filtering
- Product reviews and ratings
- Inventory management
- Analytics dashboard

## 📞 Support

For issues or questions, check the admin panel or contact the development team.

---

**Built with ❤️ for modern eCommerce**
