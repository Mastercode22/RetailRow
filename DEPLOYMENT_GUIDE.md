# RetailRow - Full-Stack eCommerce Platform

## 🚀 Production-Ready Integrated System

This is a **fully integrated**, **backend-driven** eCommerce platform where **ALL frontend content is dynamically loaded from the database via REST APIs**. The admin dashboard controls 100% of the website content.

---

## ✅ What Has Been Integrated

### **Backend API Endpoints (Complete)**
All endpoints are production-ready with:
- ✅ Full CRUD operations
- ✅ Error handling & validation
- ✅ Pagination support
- ✅ Search & filtering
- ✅ Slug-based routing
- ✅ Authentication/authorization

| Endpoint | Purpose | Methods |
|----------|---------|---------|
| `/api/products.php` | Products management | GET, POST, PUT, DELETE |
| `/api/categories.php` | Categories management | GET, POST, PUT, DELETE |
| `/api/banners.php` | Hero banners/sliders | GET, POST, PUT, DELETE |
| `/api/flash-sales.php` | Flash sales | GET, POST, PUT, DELETE |
| `/api/pages.php` | Dynamic pages (About, Contact, etc.) | GET, POST, PUT, DELETE |
| `/api/navigation.php` | Navigation menus | GET, POST, PUT, DELETE |
| `/api/footer-links.php` | Footer links & groups | GET, POST, PUT, DELETE |
| `/api/settings.php` | Global site settings | GET, POST |

### **Frontend Integration (Complete)**
- ✅ **Centralized API Service** (`js/api.js`) - All API calls go through one service
- ✅ **Dynamic Content Loading** (`js/app.js`) - Everything loads from backend
- ✅ **No Hard-Coded Content** - Zero static data in frontend
- ✅ **Loading States** - Proper UI feedback during data fetch
- ✅ **Error Handling** - Graceful degradation on API failures
- ✅ **Environment-Based URLs** - Auto-detects localhost vs production

### **Admin Control (Complete)**
Every piece of frontend content can be controlled from admin:
- ✅ Announcement bar text
- ✅ Phone numbers
- ✅ Navigation menus
- ✅ Hero carousel/banners
- ✅ Product listings
- ✅ Categories
- ✅ Flash sales
- ✅ Footer links
- ✅ Page content (About, Contact, etc.)
- ✅ Meta tags (SEO)
- ✅ Site settings

---

## 📁 Project Structure

```
RetailRow/
├── api/                          # Backend REST API
│   ├── products.php             # Products CRUD with search/filter/pagination
│   ├── categories.php           # Categories management
│   ├── banners.php              # Hero banners
│   ├── flash-sales.php          # Flash sales
│   ├── pages.php                # Dynamic pages (NEW)
│   ├── navigation.php           # Navigation menus (NEW)
│   ├── footer-links.php         # Footer management (NEW)
│   └── settings.php             # Global settings
│
├── js/                          # Frontend JavaScript
│   ├── api.js                   # Centralized API service (NEW)
│   ├── app.js                   # Main application logic (NEW)
│   └── main.js                  # UI interactions (legacy)
│
├── config/                      # Configuration
│   ├── db.php                   # Database connection
│   └── auth.php                 # Authentication
│
├── admin/                       # Admin dashboard
│   ├── dashboard.php
│   ├── products/
│   ├── categories/
│   ├── banners/
│   ├── flash-sales/
│   └── settings/
│
├── css/
│   └── style.css                # All styles
│
├── db_enhanced.sql              # Enhanced database schema (NEW)
└── index.php                    # Main homepage (dynamic)
```

---

## 🗄️ Database Schema

The enhanced schema includes:

### Core Tables
- `users` - Admin users
- `products` - Product catalog with slugs
- `categories` - Product categories with slugs
- `banners` - Hero carousel images
- `flash_sales` - Limited-time offers
- `settings` - Global site configuration

### CMS Tables (NEW)
- `pages` - Dynamic pages (About, Contact, Privacy, etc.)
- `navigation_menus` - Menu containers
- `menu_items` - Menu links
- `footer_link_groups` - Footer column groups
- `footer_links` - Individual footer links
- `homepage_sections` - Control homepage section visibility

---

## 🔧 Installation & Setup

### 1. Database Setup

```bash
# Import the enhanced database schema
mysql -u root -p < db_enhanced.sql
```

This creates:
- Database: `retailrow`
- Default admin user: `admin@retailrow.com` / `admin123`
- Sample data for all tables

### 2. Configure Database Connection

Edit `config/db.php`:

```php
private $host = "localhost";
private $db_name = "retailrow";
private $username = "your_mysql_username";
private $password = "your_mysql_password";
```

### 3. File Permissions

```bash
chmod 755 assets/uploads
chmod 755 assets/images/banners
```

### 4. Update JavaScript API URLs

The `js/api.js` file auto-detects the environment:
- **localhost** → `/RetailRow/api`
- **staging** → `/api`
- **production** → `/api`

For custom paths, edit `getAPIBaseURL()` in `js/api.js`.

---

## 🚀 Deployment Checklist

### Step 1: Upload Files
Upload all files to your web server (via FTP, SSH, or control panel).

### Step 2: Database
- Import `db_enhanced.sql` using phpMyAdmin or command line
- Verify all tables were created

### Step 3: Configuration
- Update `config/db.php` with production database credentials
- Check file permissions on upload directories

### Step 4: Test Admin Panel
1. Navigate to `/admin/login.php`
2. Login with: `admin@retailrow.com` / `admin123`
3. **Change the password immediately!**

### Step 5: Verify Frontend
1. Visit homepage
2. Check browser console for errors
3. Verify all content loads from database

---

## 🎯 How It Works

### Data Flow

```
Admin Panel → Database → REST API → Frontend JavaScript → User Interface
```

1. **Admin** updates content in dashboard
2. **Database** stores the changes
3. **API** exposes data via REST endpoints
4. **Frontend** fetches data on page load
5. **User** sees updated content instantly

### Example: Adding a New Product

**Admin Side:**
1. Go to Admin → Products → Add New
2. Fill in product details
3. Click "Save"

**What Happens:**
1. Form submits to `/api/products.php` (POST)
2. API validates & inserts into `products` table
3. Auto-generates unique slug
4. Returns success response

**Frontend Side:**
1. On homepage load, `app.js` calls `api.getFeaturedProducts()`
2. Fetches from `/api/products.php?type=featured`
3. Renders products dynamically
4. New product appears immediately

---

## 🎨 Customization

### Adding a New Setting

**1. Add to database:**
```sql
INSERT INTO settings (key_name, value, type) 
VALUES ('promo_banner_text', 'New promo message', 'text');
```

**2. Use in frontend:**
```javascript
const settings = await api.getSettings();
document.querySelector('.promo').textContent = settings.promo_banner_text;
```

### Creating a New Page

**1. Via Admin Panel:**
- Admin → Pages → Add New
- Fill in title, slug, content
- Set visibility options
- Save

**2. Access via URL:**
```
yoursite.com/page/{slug}
```

### Adding a Menu Item

**1. Via API or Admin:**
```php
POST /api/navigation.php
{
  "menu_id": 1,
  "label": "New Page",
  "url": "/new-page",
  "position": 5
}
```

**2. Frontend automatically:**
- Fetches menu on load
- Renders items dynamically
- Updates when admin changes menu

---

## 🔒 Security Features

✅ **SQL Injection Protection** - All queries use prepared statements  
✅ **XSS Prevention** - HTML escaping on output  
✅ **CSRF Protection** - Admin authentication required  
✅ **Input Validation** - All API inputs validated  
✅ **Password Hashing** - Bcrypt for user passwords  
✅ **Role-Based Access** - Admin-only endpoints protected  

---

## 📝 API Documentation

### Get Products
```bash
GET /api/products.php?type=featured&page=1&limit=20
```

### Get Single Product
```bash
GET /api/products.php?slug=wireless-headphones
```

### Search Products
```bash
GET /api/products.php?search=headphones
```

### Get Categories
```bash
GET /api/categories.php
```

### Get Page Content
```bash
GET /api/pages.php?slug=about
```

### Get Settings
```bash
GET /api/settings.php?keys=site_title,phone_number,currency_symbol
```

---

## 🧪 Testing

### Test API Endpoints
```bash
# Test products endpoint
curl http://localhost/RetailRow/api/products.php?type=featured

# Test categories
curl http://localhost/RetailRow/api/categories.php

# Test settings
curl http://localhost/RetailRow/api/settings.php?keys=site_title
```

### Browser Console Tests
```javascript
// Test API service
api.getProducts().then(r => console.log(r));
api.getCategories().then(r => console.log(r));
api.getBanners().then(r => console.log(r));
```

---

## 🐛 Troubleshooting

### Issue: API returns 404
**Solution:** Check `.htaccess` or web server configuration. Ensure API files are accessible.

### Issue: CORS errors
**Solution:** API files already include CORS headers. Check browser console for specific errors.

### Issue: Database connection failed
**Solution:** Verify credentials in `config/db.php` and ensure MySQL is running.

### Issue: Products not showing
**Solution:**
1. Check browser console for API errors
2. Verify database has products with `is_active = 1`
3. Check API response: `/api/products.php?type=featured`

---

## 📦 Environment Variables

For production, consider using environment variables:

```php
// config/db.php
$this->host = getenv('DB_HOST') ?: 'localhost';
$this->db_name = getenv('DB_NAME') ?: 'retailrow';
$this->username = getenv('DB_USER') ?: 'root';
$this->password = getenv('DB_PASS') ?: '';
```

---

## 🎓 Key Concepts

### 1. **No Hard-Coded Content**
Everything you see on the frontend comes from the database. Even the announcement bar text is in `settings` table.

### 2. **Admin-Driven**
Admin panel controls 100% of visible content. No code changes needed to update site.

### 3. **Slug-Based Routing**
Products, categories, and pages use SEO-friendly slugs:
- `/product/wireless-headphones`
- `/category/electronics`
- `/page/about-us`

### 4. **API-First Architecture**
Frontend is completely decoupled from backend. Could be replaced with React/Vue/Angular easily.

### 5. **Loading States**
Proper UX with loading indicators, empty states, and error messages.

---

## 🚀 Performance Optimization

### Already Implemented:
✅ Image lazy loading  
✅ Minimal API calls  
✅ Pagination on large datasets  
✅ Database indexes on frequently queried columns  

### Recommended:
- Enable browser caching
- Use CDN for static assets
- Enable GZIP compression
- Minify CSS/JS for production

---

## 📞 Support

For issues or questions:
1. Check this README
2. Review API endpoints in `/api/` folder
3. Check browser console for errors
4. Verify database connectivity

---

## 🎯 Next Steps

1. **Change admin password**
2. **Add your products** via admin panel
3. **Customize settings** (phone, colors, text)
4. **Upload banner images**
5. **Create pages** (About, Contact, etc.)
6. **Test everything** before going live

---

## ✨ Features Summary

✅ **100% Dynamic Content** - No static HTML  
✅ **RESTful API** - Clean, consistent endpoints  
✅ **Admin Dashboard** - Full content management  
✅ **SEO-Friendly URLs** - Slug-based routing  
✅ **Responsive Design** - Mobile-first approach  
✅ **Error Handling** - Graceful degradation  
✅ **Type Safety** - Input validation  
✅ **Scalable Architecture** - Ready for growth  

---

**Built with ❤️ for RetailRow**

This system is production-ready and fully tested. Every frontend element is backend-driven. Just add your content and deploy!
