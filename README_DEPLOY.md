# PakAutoParts - Deployment & Installation Manual

Comprehensive guide for deploying this auto spare parts e-commerce store locally (Laragon / XAMPP) or on production shared hosting (cPanel / hPanel / Apache).

---

## 🔑 Default Administrator Credentials

> [!WARNING]
> **CHANGE THIS DEFAULT PASSWORD IMMEDIATELY UPON FIRST LOGIN!**
> Do not leave these default credentials on a live, publicly accessible production server.

- **Admin Login URL:** `http://your-domain.com/admin/login`
- **Default Email:** `admin@pakautoparts.pk`
- **Default Password:** `Admin@12345`
- **Default Role:** `owner`

---

## 💻 Local Setup (Windows with Laragon or XAMPP)

### Prerequisites
- PHP 8.2 or higher with `pdo_mysql`, `gd`, `fileinfo`, `mbstring` extensions enabled.
- MySQL 8.0+ or MariaDB 10.4+.
- Apache with `mod_rewrite` and `mod_headers` enabled.

### Step 1: Place the Project in Your Web Directory
- **Laragon:** Copy the folder to `C:\laragon\www\sparepart-website`
- **XAMPP:** Copy the folder to `C:\xampp\htdocs\sparepart-website`

### Step 2: Create Database & Import Schema
1. Open phpMyAdmin (`http://localhost/phpmyadmin`) or your MySQL client.
2. Create a new database named `spareparts_db` with collation `utf8mb4_unicode_ci`:
   ```sql
   CREATE DATABASE `spareparts_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the provided SQL dump:
   - File location: `database/database.sql`
   - In phpMyAdmin: Select `spareparts_db` → Click **Import** → Choose `database.sql` → Click **Import**.

### Step 3: Configure Database Connection
Open `config/config.php` and verify the `db` array:
```php
'db' => [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'port'      => 3306,
    'database'  => 'spareparts_db',
    'username'  => 'root',
    'password'  => '', // Your local MySQL password (empty by default on XAMPP/Laragon)
    'charset'   => 'utf8mb4',
],
```

### Step 4: Run the Application
Open your web browser and navigate to:
- If using Laragon: `http://sparepart-website.test` or `http://localhost/sparepart-website`
- If using XAMPP: `http://localhost/sparepart-website`

---

## 🌐 Production Deployment (cPanel / hPanel Shared Hosting)

### Step 1: Upload Files
1. Log into your **cPanel** or **hPanel**.
2. Open **File Manager** and navigate to your domain's document root (typically `public_html` or a subdomain folder).
3. Upload all project files.
4. Ensure the root `.htaccess` file was uploaded (enable **"Show Hidden Files"** in cPanel settings to verify).

### Step 2: Create Database in cPanel
1. In cPanel, click **MySQL Database Wizard**.
2. Create a new database (e.g., `cpuser_spareparts`).
3. Create a new MySQL user and set a strong password.
4. Assign the user to the database with **ALL PRIVILEGES**.
5. Open **phpMyAdmin** from cPanel, select your newly created database, and import `database/database.sql`.

### Step 3: Update `config/config.php`
Edit `config/config.php` via cPanel File Manager:
```php
'app' => [
    'env'   => 'prod',   // Set to 'prod' for production performance
    'debug' => false,    // Disables verbose error reporting
],
'db' => [
    'host'      => 'localhost',
    'database'  => 'cpuser_spareparts',
    'username'  => 'cpuser_dbuser',
    'password'  => 'YourStrongDbPassword123#',
],
```

### Step 4: Verify Folder Permissions
Ensure the following directories have write permissions (chmod `755` or `775`):
- `public/uploads/`
- `public/uploads/products/`
- `public/uploads/blog/`
- `public/uploads/payments/`
- `storage/cache/`
- `storage/logs/`

---

## 🎨 Tailwind CSS Compiling (Optional)

In `APP_ENV=dev`, the application automatically loads the Tailwind CDN. For maximum production speed in `APP_ENV=prod`, you can compile the minified stylesheet:

- **Windows:** Double-click or run `build-css.bat`
- **Linux / Mac:** Run `./build-css.sh`
- **Or via Standalone CLI:**
  ```bash
  npx tailwindcss -i ./src/input.css -o ./public/assets/css/app.css --minify
  ```

---

## ✅ Post-Deployment Verification Checklist

- [ ] **Storefront:** Visit `http://your-domain.com/` and confirm products, categories, and brands load correctly.
- [ ] **Vehicle Fitment:** Test the hero "Select Your Vehicle" widget (e.g., Toyota → Corolla → 2018) and confirm "My Garage" status updates.
- [ ] **Search:** Type `04152` or `Brake Pads` in the header search input and confirm real-time auto-suggestions show up.
- [ ] **Shopping Cart:** Add an item to the cart, verify quantity changes, and confirm wholesale tier volume discounts calculate properly.
- [ ] **Checkout:** Place a test order using Cash on Delivery (COD) and verify that stock is deducted and the confirmation screen appears.
- [ ] **WhatsApp Direct:** Test the "Order on WhatsApp" button and verify the prefilled itemized message opens in WhatsApp.
- [ ] **Order Tracking:** Visit `/order/track`, input the generated Order ID and mobile number, and confirm the step timeline renders.
- [ ] **Admin Panel:** Sign in at `/admin/login`, check the analytics dashboard charts, and change the default administrator password under `/admin/settings`.
- [ ] **Printable Invoices:** Open an order in `/admin/orders` and test printing the A4 commercial invoice and 80mm thermal receipt.
- [ ] **Technical SEO:** Check `/sitemap.xml` and `/robots.txt` in your browser.

