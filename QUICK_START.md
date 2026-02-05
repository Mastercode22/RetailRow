# 🚀 RetailRow - Quick Start Guide

## Get Up and Running in 5 Minutes

### Step 1: Database Setup (2 minutes)

```bash
# Create the database and import schema
mysql -u root -p
```

```sql
CREATE DATABASE retailrow;
USE retailrow;
source db_enhanced.sql;
EXIT;
```

**Or using phpMyAdmin:**
1. Create database named `retailrow`
2. Import `db_enhanced.sql`

### Step 2: Configure Database (30 seconds)

Edit `config/db.php`:

```php
private $host = "localhost";
private $db_name = "retailrow";
private $username = "your_username";  // Change this
private $password = "your_password";  // Change this
```

### Step 3: Set File Permissions (30 seconds)

```bash
chmod 755 assets/uploads
chmod 755 assets/images/banners
```

### Step 4: Verify Setup (1 minute)

```bash
php setup-check.php
```

This will check:
- ✓ PHP version and extensions
- ✓ Database connection
- ✓ Required tables
- ✓ File permissions
- ✓ API files
- ✓ JavaScript files

### Step 5: Access Your Site (1 minute)

**Frontend:**
```
http://localhost/RetailRow/
```

**Admin Panel:**
```
http://localhost/RetailRow/admin/login.php
```

**Default Admin Login:**
- Email: `admin@retailrow.com`
- Password: `admin123`

⚠️ **IMPORTANT:** Change the admin password immediately after first login!

---

## ✅ What You Should See

### Frontend Homepage
- ✅ Dynamic announcement bar
- ✅ Hero carousel (banners from database)
- ✅ Flash sales section
- ✅ Category tiles
- ✅ Featured products
- ✅ Dynamic footer links

### Admin Dashboard
- ✅ Products management
- ✅ Categories management
- ✅ Banners management
- ✅ Flash sales management
- ✅ Settings panel

---

## 🎯 Next Steps

### 1. Change Admin Password
```
Admin → Settings → Change Password
```

### 2. Add Your First Product
```
Admin → Products → Add New
```

Fill in:
- Product name (slug auto-generated)
- Category
- Price
- Description
- Upload image (optional)
- Mark as featured (to show on homepage)

### 3. Upload Banner Images
```
Admin → Banners → Add New
```

Upload hero carousel images for homepage.

### 4. Customize Settings
```
Admin → Settings
```

Update:
- Site title
- Announcement text
- Phone number
- WhatsApp number
- Currency symbol
- Promo text

### 5. Create Pages
```
Admin → Pages → Add New
```

Create dynamic pages like:
- About Us
- Contact
- Privacy Policy
- Terms & Conditions

---

## 🔍 Troubleshooting

### Issue: "Database connection failed"
**Fix:** Check `config/db.php` credentials

### Issue: "No products showing"
**Fix:** 
1. Check products exist in database
2. Ensure `is_active = 1`
3. Check browser console for API errors
4. Test API directly: `/api/products.php?type=featured`

### Issue: "404 on API calls"
**Fix:** 
1. Verify API files exist in `/api/` folder
2. Check file permissions
3. Test direct access: `http://localhost/RetailRow/api/products.php`

### Issue: "Images not loading"
**Fix:**
1. Check upload folder permissions: `chmod 755 assets/uploads`
2. Verify image paths in database
3. Check browser console for 404 errors

### Issue: "Admin login not working"
**Fix:**
1. Verify admin user exists: Check `users` table
2. Password is hashed - use default: `admin123`
3. Clear browser cache and cookies

---

## 📱 Test on Mobile

Visit your site on mobile to verify:
- ✓ Responsive design works
- ✓ Mobile menu functions
- ✓ Touch scrolling works on flash sales
- ✓ Cart panel works
- ✓ All images load

---

## 🌐 Production Deployment

### Before Going Live:

1. **Change database credentials** in `config/db.php`
2. **Update API base URL** in `js/api.js` (auto-detects, but verify)
3. **Change admin password**
4. **Test all functionality**
5. **Enable error logging** (disable display_errors)
6. **Set up SSL certificate** (HTTPS)
7. **Configure backups**
8. **Test payment gateway** (if applicable)

### Production Checklist:

```
[ ] Database backed up
[ ] Admin password changed
[ ] SSL certificate installed
[ ] Error logging enabled
[ ] File permissions verified
[ ] All links tested
[ ] Mobile tested
[ ] Different browsers tested
[ ] SEO meta tags configured
[ ] Analytics installed (optional)
```

---

## 📊 Verify Everything Works

### Test These Features:

#### Homepage
```
[ ] Announcement bar shows correct text
[ ] Hero carousel rotates
[ ] Flash sales display
[ ] Categories show with icons
[ ] Featured products render
[ ] Footer links work
[ ] Mobile menu opens
```

#### Products
```
[ ] Product listing loads
[ ] Product details page works
[ ] Images display
[ ] Prices formatted correctly
[ ] Add to cart works
[ ] Search functions
```

#### Admin
```
[ ] Login works
[ ] Dashboard loads
[ ] Can add product
[ ] Can edit product
[ ] Can delete product (soft delete)
[ ] Settings update
[ ] Banners upload
```

#### APIs (Direct Test)
```
[ ] /api/products.php returns JSON
[ ] /api/categories.php returns JSON
[ ] /api/banners.php returns JSON
[ ] /api/settings.php returns JSON
[ ] /api/pages.php returns JSON
[ ] /api/footer-links.php returns JSON
```

---

## 🎉 You're Ready!

If all checks pass, your RetailRow installation is **production-ready**.

### Key URLs:

| Page | URL |
|------|-----|
| Homepage | `/` |
| Admin Login | `/admin/login.php` |
| Admin Dashboard | `/admin/dashboard.php` |
| Products | `/products` |
| Categories | `/categories` |
| About | `/page/about` |

### API Endpoints:

| Resource | Endpoint |
|----------|----------|
| Products | `/api/products.php` |
| Categories | `/api/categories.php` |
| Banners | `/api/banners.php` |
| Settings | `/api/settings.php` |
| Pages | `/api/pages.php` |
| Footer Links | `/api/footer-links.php` |

---

## 💡 Pro Tips

1. **Use descriptive product names** - They become URLs (slugs)
2. **Optimize images** - Before uploading (resize, compress)
3. **Test on real devices** - Not just browser DevTools
4. **Keep backups** - Regular database exports
5. **Monitor error logs** - Check for issues
6. **Update content regularly** - Keep site fresh
7. **Use categories** - Organize products well
8. **Feature best sellers** - Mark products as featured
9. **Run flash sales** - Create urgency, boost sales
10. **Mobile first** - Most traffic is mobile

---

## 📞 Need Help?

1. Check `DEPLOYMENT_GUIDE.md` for detailed docs
2. Review `INTEGRATION_CHECKLIST.md` for technical details
3. Run `php setup-check.php` to diagnose issues
4. Check browser console for JavaScript errors
5. Test API endpoints directly in browser

---

**Time to deploy:** ~5 minutes  
**Difficulty:** Easy  
**Technical knowledge required:** Basic (can copy/paste)  
**Production ready:** Yes ✅  

---

**Happy selling with RetailRow! 🛒**
