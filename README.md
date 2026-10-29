# PakAutoParts - E-Commerce Store & Auto Spare Parts Network

A high-performance, production-ready automotive spare parts e-commerce store, technical blog, and administrative management panel built specifically for the Pakistani automobile market (cars, commercial vehicles, and motorcycles).

---

## 🏎️ Business & Regional Context (Pakistan Market)

- **Target Audience:** Car & motorcycle owners, workshop mechanics, procurement managers.
- **Key Local Optimizations:**
  - **Currency:** Pakistani Rupee (`PKR / Rs.`) formatted with local standards.
  - **Mobile-First Experience:** Engineered for high responsiveness on local 4G mobile networks with minimal data payloads and pre-cached UI states.
  - **Phone Format Normalization:** Accepts `03XX-XXXXXXX`, `03XXXXXXXXX`, or `+923XXXXXXXXX` formats and normalizes transparently.
  - **Payment Methods:**
    - Cash on Delivery (COD) with configurable city-based delivery fees.
    - Direct Bank Transfer (Meezan Bank, HBL, 1Link).
    - JazzCash & Easypaisa manual wallet transfers with payment screenshot upload.
    - Instant WhatsApp Direct Checkout (opens pre-formatted itemized bill in WhatsApp chat).
  - **Vehicle Fitment ("My Garage"):** Chained selection engine (Make → Model → Year → Engine) persisted via `localStorage` and synchronized cookie for instant compatibility verification (`✓ Guaranteed to fit` vs `⚠ May not fit`).

---

## 🛠️ Technology Stack & Shared Hosting Architecture

Strictly adhering to shared-hosting constraints (Apache, cPanel, hPanel, XAMPP, Laragon):

- **Backend:** PHP 8.2+ Object-Oriented Architecture (strict typing, PSR-4 autoloader without Composer).
- **Database:** MySQL 8.0+ / MariaDB 10.4+ via PDO with strict prepared statements, foreign keys, row-locking transactions, and FULLTEXT search.
- **Frontend & Styling:**
  - Tailwind CSS with standalone compilation runner (No Node.js needed at runtime).
  - Alpine.js 3 via pinned CDN for reactive components.
  - Chart.js 4 via pinned CDN for admin analytics.
  - Quill 2 via pinned CDN for rich-text article and product description editing.
- **Image Processing:** PHP GD library (finfo MIME verification, WebP conversion, thumbnail generation, malware stripping).
- **Caching & Rate Limiting:** Zero-dependency file-based cache and rate limiters (No Redis/Memcached required).

---

## 📁 Project Structure

```text
sparepart-website/
├── app/
│   ├── Core/               # Autoloader, Config, Database, Request, Response, Router, Session, Csrf, Auth, RateLimiter, ImageHandler
│   ├── Helpers/            # Formatters, Currency, Phone Normalization, Helpers
│   ├── Lang/               # UI localization (English with Urdu-ready key mapping)
│   ├── Models/             # Product, Category, Brand, Vehicle, Order, Coupon, Review, PartRequest, BlogPost, Setting, AuditLog
│   ├── Controllers/        # Storefront Controllers (Home, Catalog, Product, Garage, Cart, Checkout, Order, Blog, Seo)
│   │   └── Admin/          # Admin Controllers (Auth, Dashboard, Product, Order, Category, Brand, Vehicle, Coupon, Blog, etc.)
│   └── Views/              # Storefront & Admin templates, partials, and printable invoices
├── config/
│   ├── config.php          # Global application & database settings
│   └── .htaccess           # Apache deny rule
├── database/
│   ├── database.sql        # Complete schema, foreign keys, indexes & realistic Pakistan demo seed
│   └── .htaccess           # Apache deny rule
├── public/                 # Web assets & upload directories
│   ├── assets/             # Compiled CSS, JS, brand SVG logo
│   └── uploads/            # Sanitized user/admin uploads with script execution disabled
├── storage/                # File cache, rate limits, and error logs
│   ├── cache/
│   ├── logs/
│   └── .htaccess           # Apache deny rule
├── src/
│   └── input.css           # Tailwind custom layer components
├── tailwind.config.js       # Industrial design tokens
├── build-css.bat           # Windows CSS builder
├── build-css.sh            # Linux / cPanel CSS builder
├── index.php               # Front controller & bootstrap
├── .htaccess               # Production Apache security headers, GZIP, and clean URL rewrites
├── PROGRESS.md             # Development phase tracker
├── README.md               # Architecture documentation
└── README_DEPLOY.md        # Comprehensive local & cPanel deployment guide
```

---

## 🔒 Security Architecture

1. **SQL Injection Immunity:** 100% of database interactions run through PDO prepared statements with strict parameter binding.
2. **CSRF Mitigation:** Timing-attack safe tokens checked on every state-changing POST request.
3. **Session Hardening:** `SameSite=Lax`, `HttpOnly`, `use_strict_mode=1`, periodic ID regeneration, and user-agent signature verification.
4. **Malware Defense in Uploads:**
   - Files are validated against `finfo` MIME types (not client extensions).
   - Random 32-character hexadecimal filenames.
   - Re-encoded through GD into WebP, stripping potential polyglot script payloads.
   - `public/uploads/.htaccess` disables all script handlers (`php_flag engine off`, blocks `.php`, `.phar`, `.sh`, `.cgi`).
5. **Rate Limiting:** Protects admin sign-in, order tracking, and part sourcing inquiries against automated scraping and brute-force attacks.

---

## ⚖️ License
Proprietary software developed for PakAutoParts Network.

