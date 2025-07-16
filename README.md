<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# GenZ Supply Chain Management System

A comprehensive, world-class clothing supply chain management system with role-based dashboards and interactive features for all supply chain actors.

## 🚀 **Working Features**

### **Core Supply Chain Management**
- ✅ **Order Management** - Create, edit, track, and manage orders
- ✅ **Inventory Management** - Real-time stock tracking and updates
- ✅ **Product Management** - Complete product lifecycle management
- ✅ **Delivery Management** - Track deliveries and optimize routes
- ✅ **Role-Based Access** - Secure access control for all actors

### **Interactive Features**

#### **Order Management**
- **Place Orders** - Interactive order creation with product selection
- **Edit Orders** - Modify order details and status
- **Track Orders** - Real-time order status tracking
- **Order Analytics** - Sales and performance analytics
- **Bulk Operations** - Process multiple orders simultaneously

#### **Inventory Management**
- **Stock Updates** - Add, subtract, or set stock levels
- **Low Stock Alerts** - Automatic notifications for reordering
- **Inventory Analytics** - Stock value and turnover analysis
- **Location Management** - Track inventory across warehouses and stores
- **Bulk Stock Updates** - Update multiple items at once

#### **Product Management**
- **Product Creation** - Add new products with images and specifications
- **Product Editing** - Update product details and pricing
- **Product Analytics** - Sales performance and inventory analysis
- **Bulk Operations** - Activate/deactivate multiple products
- **Image Management** - Upload and manage product images

#### **Delivery Management**
- **Delivery Creation** - Assign deliveries to drivers
- **Status Updates** - Real-time delivery status tracking
- **Route Optimization** - Optimize delivery routes
- **Proof of Delivery** - Digital delivery confirmation
- **Delivery Analytics** - Performance and timing analysis

### **Role-Based Dashboards**

#### **Manufacturer Dashboard**
- Production order management
- Real-time status tracking
- Cost analysis and reporting
- Sample data generation for testing

#### **Warehouse Manager Dashboard**
- Inventory overview and management
- Stock level monitoring
- Delivery coordination
- Warehouse utilization analytics

#### **Retailer Dashboard**
- Order management and tracking
- Inventory management
- Sales analytics
- Customer order processing

#### **Delivery Personnel Dashboard**
- Delivery assignments
- Route optimization
- Status updates
- Proof of delivery

#### **Customer Dashboard**
- Order history and tracking
- Product browsing
- Order placement
- Delivery status

#### **Admin Dashboard**
- System overview and analytics
- User management
- Role management
- System configuration

## 🛠 **Technical Architecture**

### **Backend (Laravel 10)**
- **MVC Architecture** - Clean separation of concerns
- **Eloquent ORM** - Powerful database relationships
- **Role-Based Authentication** - Secure access control
- **Real-time Features** - Live updates and notifications
- **API Integration** - RESTful API endpoints

### **Frontend (Bootstrap + Blade)**
- **Responsive Design** - Works on all devices
- **Interactive UI** - Modern, user-friendly interface
- **Real-time Updates** - Live data without page refresh
- **Modal Dialogs** - Smooth user interactions

### **Database (MySQL)**
- **Normalized Schema** - Efficient data structure
- **Foreign Key Relationships** - Data integrity
- **Indexed Queries** - Fast performance
- **Transaction Support** - Data consistency

## 📊 **Key Features**

### **Order Management System**
```php
// Create new order
POST /orders
{
    "customer_id": 1,
    "products": [
        {"product_id": 1, "quantity": 2},
        {"product_id": 3, "quantity": 1}
    ],
    "shipping_address": "123 Main St",
    "notes": "Handle with care"
}

// Update order status
PATCH /orders/{id}/status
{
    "status": "shipped"
}
```

### **Inventory Management**
```php
// Update stock levels
POST /inventory/update-stock
{
    "inventory_id": 1,
    "quantity": 50,
    "operation": "add",
    "notes": "New shipment received"
}

// Get low stock alerts
GET /inventory/low-stock-alerts
```

### **Product Management**
```php
// Create new product
POST /products
{
    "name": "Classic Cotton T-Shirt",
    "sku": "TSHIRT-001",
    "price": 25.00,
    "cost": 12.00,
    "category": "tops",
    "unit": "piece"
}

// Toggle product status
PATCH /products/{id}/toggle-status
```

### **Delivery Management**
```php
// Create delivery
POST /deliveries
{
    "order_id": 1,
    "driver_id": 5,
    "delivery_fee": 5.00,
    "estimated_delivery_date": "2025-01-15"
}

// Update delivery status
PATCH /deliveries/{id}/status
{
    "status": "delivered"
}
```

## 🔧 **Installation & Setup**

### **Prerequisites**
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js & NPM

### **Installation Steps**

1. **Clone the repository**
```bash
git clone <repository-url>
cd GenZ
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database configuration**
```bash
# Update .env with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=genz_supply_chain
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Start the development server**
```bash
php artisan serve
```

7. **Access the application**
```
http://localhost:8000
```

---

## 🖌️ **Ensuring Consistent Design (CSS/JS Assets)**

To make sure everyone sees the same design (especially for the welcome, login, and register pages):

1. **After every pull from GitHub:**
   - Run `npm install` (if dependencies changed)
   - Run `npm run build` (to compile the latest assets)
   - Run `php artisan view:clear` (to clear cached views)
   - Refresh your browser with Ctrl+F5 (to clear browser cache)

2. **Do NOT commit compiled files in `public/build`**
   - Only commit changes in `resources/css/`, `resources/js/`, and Blade files.
   - The `.gitignore` already excludes `public/build` (see project root).
   - _Excluding compiled assets from git does **not** compromise collaboration. It is the standard practice for Laravel + Vite projects. Each developer and the server will generate these files as needed._

3. **If you change any CSS/JS:**
   - Commit the source files (`resources/css/`, `resources/js/`), NOT the compiled output.
   - Notify your team to re-run `npm run build` after pulling.

4. **If you add new dependencies:**
   - Commit both `package.json` and `package-lock.json`.

---

## 👥 **User Roles & Access**

### **Default Users**
- **Admin**: admin@genzfashionz.com
- **Manufacturer**: manufacturer@genzfashionz.com
- **Warehouse Manager**: warehouse@genzfashionz.com
- **Retailer**: retailer@genzfashionz.com
- **Delivery Personnel**: delivery@genzfashionz.com
- **Customer**: customer@genzfashionz.com

### **Password for all users**: `password`

## 🎯 **Interactive Features Guide**

### **Creating Orders**
1. Navigate to Orders → Create New Order
2. Select customer and products
3. Set quantities and shipping details
4. Click "Place Order" to create

### **Managing Inventory**
1. Go to Inventory → Manage Stock
2. Select product and location
3. Choose operation (add/subtract/set)
4. Enter quantity and notes
5. Click "Update Stock"

### **Tracking Deliveries**
1. Access Delivery → Track Delivery
2. Enter tracking number
3. View real-time status updates
4. Update delivery status as needed

### **Product Management**
1. Navigate to Products → Manage Products
2. Create new products with images
3. Edit existing product details
4. Toggle product status
5. View product analytics

## 📈 **Analytics & Reporting**

### **Order Analytics**
- Total orders and revenue
- Orders by status
- Monthly order trends
- Customer order patterns

### **Inventory Analytics**
- Stock value and turnover
- Low stock alerts
- Top products by value
- Stock levels by category

### **Delivery Analytics**
- Delivery success rates
- On-time vs late deliveries
- Driver performance
- Route optimization metrics

## 🔒 **Security Features**

- **Role-based access control**
- **CSRF protection**
- **Input validation**
- **SQL injection prevention**
- **XSS protection**
- **Secure file uploads**

## 🚀 **Deployment**

### **Production Setup**
1. Set environment to production
2. Configure database for production
3. Run migrations
4. Set up web server (Apache/Nginx)
5. Configure SSL certificates
6. Set up backup systems

### **Performance Optimization**
- Enable caching (Redis/Memcached)
- Optimize database queries
- Use CDN for static assets
- Enable compression
- Monitor performance metrics

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📝 **License**

This project is licensed under the MIT License.

## 🆘 **Support**

For support and questions:
- Create an issue on GitHub
- Contact the development team
- Check the documentation

---

**GenZ Supply Chain Management System** - Streamlining fashion supply chains with modern technology and interactive features.
