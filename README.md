# 🌾 AGRIBOOST SHOP
## Fertilizers & Agricultural Essentials Platform

A complete e-commerce platform built with **Core PHP** and **MySQL**, featuring a modern **Tailwind CSS** interface designed specifically for agricultural businesses.

---

## 📋 Project Overview

**AGRIBOOST SHOP** is a simple yet powerful e-commerce system designed for selling fertilizers and agricultural products. Built with beginner-friendly Core PHP (no frameworks), it's perfect for BCA/MCA major projects or small agricultural businesses.

### ✨ Key Features

#### 👥 User Features
- User registration and login with secure password hashing
- Browse products with detailed information
- Session-based shopping cart
- Secure checkout process
- Order history tracking
- Profile management with password update

#### 🔐 Admin Features
- Comprehensive admin dashboard with statistics
- Product management (Add, Edit, Delete)
- Order management with status updates
- User management and viewing
- CSV export of orders for reporting
- Real-time inventory tracking

---

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+** - Core PHP (No frameworks)
- **MySQL** - Database
- **MySQLi** - Database connection with prepared statements

### Frontend
- **HTML5** - Structure
- **Tailwind CSS** - Modern utility-first CSS framework
- **JavaScript** - Interactive features
- **Google Fonts (Poppins)** - Typography

### Security
- Password hashing with `password_hash()` and `password_verify()`
- Prepared statements for SQL injection prevention
- Session-based authentication
- Role-based access control (Admin/User)
- Input sanitization and validation

---

## 📁 Project Structure

```
agriboost-shop/
│
├── index.php                 # Homepage with product listing
├── login.php                 # User/Admin login
├── register.php              # User registration
├── logout.php                # Session logout
├── agriboost.sql            # Database schema with sample data
│
├── config/
│   └── db.php               # Database configuration
│
├── includes/
│   ├── header.php           # Common header with Tailwind CSS
│   └── footer.php           # Common footer
│
├── user/
│   ├── cart.php             # Shopping cart
│   ├── checkout.php         # Checkout process
│   ├── orders.php           # Order history
│   └── profile.php          # User profile management
│
├── admin/
│   ├── index.php            # Admin dashboard
│   ├── products.php         # Product management
│   ├── orders.php           # Order management
│   ├── users.php            # User listing
│   └── export_orders.php    # CSV export
│
└── uploads/                 # Product images directory
```

---

## 🚀 Installation Guide

### Prerequisites
- **XAMPP** (or WAMP/LAMP) installed
- **PHP 7.4+**
- **MySQL 5.7+**
- Modern web browser

### Step-by-Step Installation

1. **Copy Project Files**
   ```
   Copy the agriboost-shop folder to:
   C:\xampp\htdocs\agriboost-shop
   ```

2. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named `agriboost_shop`
   - Import the SQL file: `agriboost.sql`

3. **Configure Database Connection**
   - Open `config/db.php`
   - Update credentials if needed (default: root/no password)

4. **Start Apache and MySQL**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

5. **Access the Application**
   - Homepage: `http://localhost/agriboost-shop/`
   - Admin Panel: `http://localhost/agriboost-shop/admin/`

---

## 🔑 Default Login Credentials

### Admin Account
- **Email:** admin@agriboost.com
- **Password:** admin123

### Test User Account
- **Email:** john@example.com
- **Password:** admin123

---

## 🎨 Design Features

### Color Palette (Agricultural Green Theme)
- **#CFFCD8** - Lightest green (backgrounds)
- **#6AEC8E** - Light green (accents)
- **#55C173** - Primary green (buttons)
- **#419759** - Medium green (hover states)
- **#2E6F40** - Dark green (text)
- **#1C4A29** - Darker green (navbar)
- **#0C2713** - Darkest green (footer)

### UI/UX Highlights
- ✅ Fully responsive design (mobile, tablet, desktop)
- ✅ Modern Tailwind CSS styling
- ✅ Smooth transitions and hover effects
- ✅ Clean typography with Poppins font
- ✅ Intuitive navigation
- ✅ Professional agricultural business aesthetic

---

## 📊 Database Schema

### Tables

#### `users`
- id, name, email, password, role, phone, address, created_at

#### `products`
- id, name, description, price, stock, image, category, created_at

#### `orders`
- id, user_id, total_amount, status, shipping_address, created_at

#### `order_items`
- id, order_id, product_id, quantity, price

---

## 🔒 Security Features

1. **Password Security**
   - Passwords hashed using PHP's `password_hash()`
   - Verification with `password_verify()`

2. **SQL Injection Prevention**
   - All queries use prepared statements
   - Input sanitization with custom `clean_input()` function

3. **Session Management**
   - Secure session-based authentication
   - Role-based access control

4. **Input Validation**
   - Server-side validation for all forms
   - Email format validation
   - Password strength requirements

---

## 📝 Usage Guide

### For Users
1. Register a new account
2. Browse available products
3. Add products to cart
4. Proceed to checkout
5. Track orders in "My Orders"
6. Update profile information

### For Admins
1. Login with admin credentials
2. View dashboard statistics
3. Manage products (add/edit/delete)
4. Update order statuses
5. View registered users
6. Export orders to CSV

---

## 🎯 Project Highlights

### Perfect for Academic Projects
- ✅ Simple Core PHP (no complex frameworks)
- ✅ Well-commented code
- ✅ Clean file structure
- ✅ Complete documentation
- ✅ Modern UI design
- ✅ All essential e-commerce features

### Suitable for Real Business
- ✅ Secure authentication system
- ✅ Complete order management
- ✅ Inventory tracking
- ✅ Professional design
- ✅ Responsive interface
- ✅ Export functionality for reports

---

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Check if MySQL is running in XAMPP
- Verify database credentials in `config/db.php`
- Ensure database `agriboost_shop` exists

**Page Not Found (404)**
- Check if files are in correct directory
- Verify Apache is running
- Check file paths in code

**Login Not Working**
- Ensure database is imported correctly
- Check if sessions are enabled in PHP
- Verify password hashing is working

---

## 📄 License

This project is created for educational purposes. Feel free to use and modify for your projects.

---

## 👨‍💻 Developer Notes

### Future Enhancements
- Payment gateway integration
- Email notifications
- Product reviews and ratings
- Advanced search and filters
- Wishlist functionality
- Multi-image product gallery

### Customization Tips
- Update color scheme in `includes/header.php` (Tailwind config)
- Modify product categories in database
- Add custom fields to user profile
- Implement file upload for product images

---

## 📞 Support

For issues or questions:
- Check the code comments for explanations
- Review the database schema
- Verify all files are in correct locations
- Ensure XAMPP services are running

---

**Made with ❤️ for Farmers and Students**

🌾 **AGRIBOOST SHOP** - Empowering Farmers, Growing Together
