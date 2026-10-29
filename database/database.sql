-- ==============================================================================
-- PakAutoParts - Complete Database Schema & Seed Data (Pakistan Auto Market)
-- Designed for MySQL 8.0 / MariaDB 10.4+ with Foreign Keys & FULLTEXT Search
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `url_redirects`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `blog_posts`;
DROP TABLE IF EXISTS `blog_categories`;
DROP TABLE IF EXISTS `stock_notifications`;
DROP TABLE IF EXISTS `part_requests`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `order_status_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `shipping_rates`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `product_price_tiers`;
DROP TABLE IF EXISTS `product_fitment`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `vehicles`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------------------------
-- 1. USERS & ROLES
-- ------------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('owner', 'manager', 'staff') NOT NULL DEFAULT 'staff',
  `failed_logins` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `lockout_until` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. CATEGORIES
-- ------------------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `parent_id` INT UNSIGNED NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_cat_parent` (`parent_id`),
  INDEX `idx_cat_active_sort` (`is_active`, `sort_order`),
  CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. BRANDS
-- ------------------------------------------------------------------------------
CREATE TABLE `brands` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `logo` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `is_popular` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_brands_popular` (`is_popular`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. VEHICLES (Pakistan Market: Toyota, Suzuki, Honda, KIA, Bike models)
-- ------------------------------------------------------------------------------
CREATE TABLE `vehicles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `make` VARCHAR(60) NOT NULL,
  `model` VARCHAR(80) NOT NULL,
  `year_start` SMALLINT UNSIGNED NOT NULL,
  `year_end` SMALLINT UNSIGNED NOT NULL,
  `engine` VARCHAR(80) NOT NULL,
  `fuel_type` ENUM('Petrol', 'Diesel', 'Hybrid', 'Electric', 'CNG') NOT NULL DEFAULT 'Petrol',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_vehicles_make_model` (`make`, `model`),
  INDEX `idx_vehicles_years` (`year_start`, `year_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. PRODUCTS
-- ------------------------------------------------------------------------------
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `sku` VARCHAR(60) NOT NULL UNIQUE,
  `oem_number` VARCHAR(100) NOT NULL,
  `cross_references` VARCHAR(255) NULL,
  `brand_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `condition_type` ENUM('Genuine', 'OEM', 'Aftermarket', 'Used') NOT NULL DEFAULT 'Genuine',
  `warranty` VARCHAR(100) NOT NULL DEFAULT '7-Day Return / 1-Month Warranty',
  `short_description` VARCHAR(300) NULL,
  `description` LONGTEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `compare_at_price` DECIMAL(10,2) NULL,
  `cost_price` DECIMAL(10,2) NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `low_stock_threshold` SMALLINT UNSIGNED NOT NULL DEFAULT 3,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `weight_kg` DECIMAL(6,2) NOT NULL DEFAULT 1.00,
  `meta_title` VARCHAR(200) NULL,
  `meta_description` VARCHAR(300) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_prod_brand` (`brand_id`),
  INDEX `idx_prod_category` (`category_id`),
  INDEX `idx_prod_sku_oem` (`sku`, `oem_number`),
  INDEX `idx_prod_price` (`price`),
  INDEX `idx_prod_status` (`is_active`, `is_featured`, `stock_quantity`),
  FULLTEXT KEY `ft_product_search` (`name`, `sku`, `oem_number`, `cross_references`, `short_description`),
  CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_prod_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. PRODUCT IMAGES
-- ------------------------------------------------------------------------------
CREATE TABLE `product_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `thumbnail_url` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  INDEX `idx_pimg_product` (`product_id`, `is_primary`, `sort_order`),
  CONSTRAINT `fk_pimg_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. PRODUCT FITMENT (Compatibility Matrix)
-- ------------------------------------------------------------------------------
CREATE TABLE `product_fitment` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `notes` VARCHAR(150) NULL,
  UNIQUE KEY `uk_fitment` (`product_id`, `vehicle_id`),
  INDEX `idx_fitment_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_fitment_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fitment_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. PRODUCT PRICE TIERS (Wholesale for Mechanics & Workshops)
-- ------------------------------------------------------------------------------
CREATE TABLE `product_price_tiers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `min_qty` SMALLINT UNSIGNED NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  INDEX `idx_tiers_product` (`product_id`, `min_qty`),
  CONSTRAINT `fk_tiers_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. COUPONS & DISCOUNTS
-- ------------------------------------------------------------------------------
CREATE TABLE `coupons` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(40) NOT NULL UNIQUE,
  `discount_type` ENUM('fixed', 'percent') NOT NULL DEFAULT 'percent',
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_spend` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) NULL,
  `usage_limit` INT UNSIGNED NULL,
  `usage_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `start_date` DATE NULL,
  `expiry_date` DATE NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  INDEX `idx_coupon_code_active` (`code`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 10. SHIPPING RATES BY CITY
-- ------------------------------------------------------------------------------
CREATE TABLE `shipping_rates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `city` VARCHAR(80) NOT NULL UNIQUE,
  `province` VARCHAR(60) NOT NULL,
  `standard_rate` DECIMAL(10,2) NOT NULL DEFAULT 250.00,
  `cod_fee` DECIMAL(10,2) NOT NULL DEFAULT 50.00,
  `free_threshold` DECIMAL(10,2) NOT NULL DEFAULT 5000.00,
  `delivery_days` VARCHAR(40) NOT NULL DEFAULT '2-3 Working Days'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 11. ORDERS
-- ------------------------------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(30) NOT NULL UNIQUE,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(120) NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `delivery_city` VARCHAR(80) NOT NULL,
  `delivery_address` TEXT NOT NULL,
  `notes` TEXT NULL,
  `payment_method` ENUM('cod', 'bank_transfer', 'jazzcash', 'easypaisa', 'whatsapp') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('pending', 'verified', 'failed') NOT NULL DEFAULT 'pending',
  `payment_proof_image` VARCHAR(255) NULL,
  `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cod_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `coupon_code` VARCHAR(40) NULL,
  `status` ENUM('Pending', 'Confirmed', 'Dispatched', 'Delivered', 'Cancelled', 'Returned') NOT NULL DEFAULT 'Pending',
  `courier_name` VARCHAR(80) NULL,
  `tracking_number` VARCHAR(80) NULL,
  `whatsapp_notified_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_order_phone` (`customer_phone`),
  INDEX `idx_order_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 12. ORDER ITEMS
-- ------------------------------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `sku` VARCHAR(60) NOT NULL,
  `oem_number` VARCHAR(100) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `quantity` SMALLINT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  INDEX `idx_oi_order` (`order_id`),
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 13. ORDER STATUS HISTORY
-- ------------------------------------------------------------------------------
CREATE TABLE `order_status_history` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `status` VARCHAR(40) NOT NULL,
  `comment` VARCHAR(255) NULL,
  `created_by` VARCHAR(100) NOT NULL DEFAULT 'System',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 14. PRODUCT REVIEWS
-- ------------------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(120) NULL,
  `customer_phone` VARCHAR(30) NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_reviews_prod_appr` (`product_id`, `is_approved`),
  CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 15. PART REQUESTS ("Can't find your part?")
-- ------------------------------------------------------------------------------
CREATE TABLE `part_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(120) NULL,
  `vehicle_info` VARCHAR(150) NOT NULL,
  `part_name` VARCHAR(150) NOT NULL,
  `part_number` VARCHAR(80) NULL,
  `description` TEXT NULL,
  `photo` VARCHAR(255) NULL,
  `status` ENUM('New', 'Quoted', 'Fulfilled', 'Closed') NOT NULL DEFAULT 'New',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_pr_phone` (`phone`),
  INDEX `idx_pr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 16. STOCK NOTIFICATIONS
-- ------------------------------------------------------------------------------
CREATE TABLE `stock_notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `customer_email` VARCHAR(120) NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `is_notified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_snotif_prod` (`product_id`, `is_notified`),
  CONSTRAINT `fk_snotif_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 17. BLOG CATEGORIES & POSTS
-- ------------------------------------------------------------------------------
CREATE TABLE `blog_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(220) NOT NULL,
  `slug` VARCHAR(240) NOT NULL UNIQUE,
  `excerpt` TEXT NULL,
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255) NULL,
  `category_id` INT UNSIGNED NULL,
  `tags` VARCHAR(200) NULL,
  `reading_time_min` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `focus_keyword` VARCHAR(100) NULL,
  `meta_title` VARCHAR(200) NULL,
  `meta_description` VARCHAR(300) NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `published_at` DATETIME NULL,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_bp_pub` (`is_published`, `published_at`),
  CONSTRAINT `fk_bp_category` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 18. SETTINGS KEY-VALUE STORE
-- ------------------------------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 19. AUDIT LOGS
-- ------------------------------------------------------------------------------
CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(60) NOT NULL,
  `entity_type` VARCHAR(60) NOT NULL,
  `entity_id` INT UNSIGNED NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_audit_user` (`user_id`),
  INDEX `idx_audit_action` (`action`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 20. URL REDIRECTS (301 SEO redirects)
-- ------------------------------------------------------------------------------
CREATE TABLE `url_redirects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `old_slug` VARCHAR(255) NOT NULL UNIQUE,
  `new_slug` VARCHAR(255) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL DEFAULT 'product',
  `redirect_code` SMALLINT UNSIGNED NOT NULL DEFAULT 301,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- REALISTIC SEED DATA FOR PAKISTAN AUTO SPARE PARTS MARKET
-- ==============================================================================

-- 1. Default Administrator (password: Admin@12345)
-- Hash generated via password_hash('Admin@12345', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'System Administrator', 'admin@pakautoparts.pk', '$2y$10$wT8Kx5uQyY4v3EcmS7Ff9eQ/d4q.U/v9Y2qC4yR3X1W8J2G8zJ9Gy', 'owner');

-- 2. Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
(1, 'Engine Components', 'engine-components', 'Pistons, timing belts, spark plugs, valves, gasket kits, and mounts.', 1, 1),
(2, 'Brake System', 'brake-system', 'High-performance brake pads, disc rotors, master cylinders, and brake fluid.', 2, 1),
(3, 'Suspension & Steering', 'suspension-steering', 'Shock absorbers, ball joints, control arms, tie rod ends, and bushings.', 3, 1),
(4, 'Filters & Maintenance', 'filters-maintenance', 'Genuine oil filters, air filters, cabin AC filters, and fuel filters.', 4, 1),
(5, 'Electrical & Ignition', 'electrical-ignition', 'Alternators, starter motors, ignition coils, spark plugs, and sensors.', 5, 1),
(6, 'Lighting & Bulbs', 'lighting-bulbs', 'Headlights, tail lights, fog lights, LED bulbs, and indicators.', 6, 1),
(7, 'Body Parts & Mirrors', 'body-parts-mirrors', 'Bumpers, side mirrors, door handles, fenders, and grilles.', 7, 1),
(8, 'Oils & Lubricants', 'oils-lubricants', 'Engine oils, transmission fluids, brake fluids, and coolants.', 8, 1),
(9, 'Tyres & Wheels', 'tyres-wheels', 'High-durability radial tyres, alloy rims, and wheel bearings.', 9, 1),
(10, 'Car & Bike Accessories', 'accessories', 'Floor mats, seat covers, horns, wipers, and emergency tool kits.', 10, 1);

-- 3. Brands
INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `is_popular`) VALUES
(1, 'Toyota Genuine', 'toyota-genuine', 'Original OEM Japanese components manufactured for Toyota vehicles.', 1),
(2, 'Honda Genuine', 'honda-genuine', 'Factory-certified genuine parts directly engineered for Honda cars and bikes.', 1),
(3, 'Suzuki Genuine', 'suzuki-genuine', 'Authentic Pak Suzuki and Suzuki Motor Corporation certified parts.', 1),
(4, 'Denso', 'denso', 'World-leading Japanese manufacturer of spark plugs, radiators, and sensors.', 1),
(5, 'NGK Spark Plugs', 'ngk', 'Premium ignition components, laser iridium spark plugs and oxygen sensors.', 1),
(6, 'Bosch', 'bosch', 'German engineered brake pads, wiper blades, horns, and fuel injectors.', 1),
(7, 'AISIN', 'aisin', 'Top OEM Japanese supplier of clutch kits, water pumps, and transmission oil.', 1),
(8, 'KYB (Kayaba)', 'kyb', 'World-renowned Japanese shock absorbers and suspension components.', 1),
(9, 'Guard Filters', 'guard-filters', 'Pakistan\'s premier and ISO-certified engine and cabin filtration brand.', 1),
(10, 'Shell Lubricants', 'shell', 'High-performance synthetic engine oils engineered for extreme Asian climates.', 1);

-- 4. Vehicles (Pakistan Market)
INSERT INTO `vehicles` (`id`, `make`, `model`, `year_start`, `year_end`, `engine`, `fuel_type`) VALUES
(1, 'Toyota', 'Corolla (XLi/GLi/Grande)', 2014, 2020, '1.3L / 1.6L / 1.8L 1ZR/2ZR-FE', 'Petrol'),
(2, 'Toyota', 'Corolla (Altis / Grande)', 2008, 2014, '1.6L / 1.8L 1ZZ/2ZR', 'Petrol'),
(3, 'Toyota', 'Yaris', 2020, 2024, '1.3L 1NR-FE / 1.5L 2NR-FE', 'Petrol'),
(4, 'Honda', 'Civic (X / Turbo / Oriel)', 2016, 2021, '1.8L i-VTEC / 1.5L Turbo', 'Petrol'),
(5, 'Honda', 'Civic (Reborn)', 2006, 2012, '1.8L R18A i-VTEC', 'Petrol'),
(6, 'Honda', 'City (Aspire / i-DSI)', 2008, 2021, '1.3L / 1.5L L13Z / L15A', 'Petrol'),
(7, 'Suzuki', 'Alto (660cc)', 2019, 2024, '658cc R06A', 'Petrol'),
(8, 'Suzuki', 'Cultus (New Shape)', 2017, 2024, '1.0L K10B', 'Petrol'),
(9, 'Suzuki', 'Wagon R', 2014, 2024, '1.0L K10B', 'Petrol'),
(10, 'Suzuki', 'Mehran (Euro II)', 2012, 2019, '796cc F8B', 'Petrol'),
(11, 'Suzuki', 'Swift', 2010, 2021, '1.3L M13A', 'Petrol'),
(12, 'KIA', 'Sportage', 2019, 2024, '2.0L Nu MPI', 'Petrol'),
(13, 'Hyundai', 'Tucson', 2020, 2024, '2.0L Smartstream MPI', 'Petrol'),
(14, 'Honda', 'CD70 Motorcycle', 2012, 2024, '72cc 4-Stroke OHC', 'Petrol'),
(15, 'Honda', 'CG125 Motorcycle', 2012, 2024, '124cc 4-Stroke OHV', 'Petrol'),
(16, 'Yamaha', 'YBR125 Motorcycle', 2015, 2024, '124cc 4-Stroke SOHC', 'Petrol');

-- 5. Products (12 High-detail Demo Spare Parts with Real Specs)
INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `oem_number`, `cross_references`, `brand_id`, `category_id`, `condition_type`, `warranty`, `short_description`, `description`, `price`, `compare_at_price`, `cost_price`, `stock_quantity`, `low_stock_threshold`, `is_featured`, `is_active`, `weight_kg`, `meta_title`, `meta_description`) VALUES
(1, 'Toyota Genuine Oil Filter Element with O-Ring', 'toyota-oil-filter-04152-yzza6', 'TOY-FLT-001', '04152-YZZA6', '04152-37010, 04152-YZZA7, HU6006Z', 1, 4, 'Genuine', 'Guaranteed Genuine Toyota', 'Original Toyota oil filter element with synthetic micro-fiber media and heat-resistant gasket ring for Corolla and Yaris.', '<p>Ensure peak engine longevity with this 100% Genuine Toyota Oil Filter Element. Specially engineered with multi-density synthetic microfibers to capture 99% of particulate debris, carbon sludge, and microscopic metal shavings down to 20 microns without restricting oil pressure flow during cold starts in Pakistani summers and winters.</p><ul><li>Part Number: 04152-YZZA6</li><li>Direct factory fitment for Toyota Corolla (2014-2020) and Yaris</li><li>Includes high-temperature nitrile O-ring and drain adapter</li><li>Recommended change interval: 5,000 km with synthetic oil</li></ul>', 1850.00, 2200.00, 1400.00, 48, 5, 1, 1, 0.25, 'Toyota Corolla Oil Filter 04152-YZZA6 Price in Pakistan | PakAutoParts', 'Buy 100% Genuine Toyota Oil Filter 04152-YZZA6 for Corolla & Yaris in Pakistan. Fast COD nationwide delivery with warranty.'),

(2, 'Denso Iridium Power Spark Plugs (Set of 4)', 'denso-iridium-spark-plugs-ik20', 'DEN-SPK-002', 'IK20-5304', 'BKR6EIX, 90919-01210, SK20R11', 4, 5, 'Genuine', '6-Month Warranty', 'High-performance 0.4mm ultra-fine iridium center electrode spark plugs for improved acceleration, fuel efficiency, and instant cold starting.', '<p>Denso Iridium Power IK20 spark plugs deliver the world\'s smallest 0.4mm laser-welded iridium tip, requiring less ignition voltage while generating a significantly hotter spark kernel. Experience instant throttle response, stabilized idling, and up to 5% better mileage on local RON 92 petrol.</p><ul><li>Electrode Diameter: 0.4mm Iridium</li><li>Thread Size: 14mm x 19mm Reach</li><li>Hex Size: 16mm (5/8 inch)</li><li>Compatible with Honda Civic Reborn, Toyota Corolla, and City</li></ul>', 7600.00, 8500.00, 6000.00, 24, 4, 1, 1, 0.35, 'Denso Iridium Spark Plugs IK20 Price in Pakistan | PakAutoParts', 'Order original Denso IK20 Iridium Power spark plugs pack of 4. Best fuel economy and ignition response for Honda & Toyota cars.'),

(3, 'AISIN Japan Front Ceramic Brake Pads Set', 'aisin-front-brake-pads-toyota-corolla', 'AIS-BRK-003', 'ASN-2064', '04465-02220, 04465-12610, D1210', 7, 2, 'OEM', '6-Month No-Noise Warranty', 'Ultra-low dust Japanese ceramic brake pad formulation providing quiet, consistent stopping power without rotor scoring.', '<p>Manufactured by AISIN Corporation (Toyota\'s tier-1 OEM braking supplier in Japan), these ceramic composite brake pads provide outstanding thermal dissipation, eliminating brake fade on motorways and city traffic. Designed specifically for low rotor wear, zero brake squeal, and minimal wheel dust.</p><ul><li>Material: Premium Japanese Ceramic Formulation</li><li>Backing Plate: Powder-coated steel with anti-rattle shims</li><li>Position: Front Left & Right (4 pads set)</li><li>Certified for Toyota Corolla GLi / Altis / Grande (2008-2020)</li></ul>', 5800.00, 6800.00, 4400.00, 32, 5, 1, 1, 1.60, 'AISIN Front Brake Pads for Toyota Corolla Price in Pakistan', 'Buy Japanese AISIN ceramic front brake pads for Toyota Corolla. Smooth, silent braking with complete anti-squeak shims.'),

(4, 'KYB Excel-G Front Shock Absorber Pair (Left + Right)', 'kyb-excel-g-front-shocks-honda-civic-x', 'KYB-SHK-004', '339414-339415', '51611-TBA-A01, 51621-TBA-A01', 8, 3, 'Genuine', '1-Year Warranty', 'Nitrogen gas-pressurized twin-tube struts engineered to restore factory ride stability, cornering grip, and road bump dampening.', '<p>KYB Excel-G shock absorbers are valved to compensate for worn suspension components, restoring your Honda Civic X to brand-new showroom ride quality. The multi-lip oil seal, Teflon coated piston band, and hard-chromed piston rod deliver superior longevity against rough road conditions in Pakistan.</p><ul><li>Type: Twin-Tube Gas Pressure Strut</li><li>Package Includes: 1x Front Right Strut + 1x Front Left Strut</li><li>Restores original vehicle control and steering responsiveness</li><li>Vehicle: Honda Civic 2016-2021 (All Variants)</li></ul>', 38500.00, 43000.00, 32000.00, 8, 2, 1, 1, 8.50, 'KYB Excel-G Front Shocks for Honda Civic X Price in Pakistan', 'Genuine KYB Japan front shock absorbers pair for Honda Civic Turbo & Oriel (2016-2021). Restore smooth, stable suspension handling.'),

(5, 'Guard Engine Air Filter for Suzuki Alto 660cc', 'guard-air-filter-suzuki-alto-660cc', 'GRD-AIR-005', '13780-74P00', 'WA-1036, A-987', 9, 4, 'Aftermarket', '100% Quality Guaranteed', 'Precision-pleated high-airflow synthetic air filter designed to block fine dust while maintaining optimal intake flow for Suzuki Alto R06A engine.', '<p>Dust storms and highway smog in Pakistan drastically degrade engine performance and fuel efficiency. Guard Filters\' micro-pore non-woven media traps abrasive dirt down to 5 microns, protecting the Alto 660cc engine\'s turbo and intake manifold.</p><ul><li>OEM Part Reference: 13780-74P00</li><li>Direct drop-in replacement in factory airbox</li><li>Tested for high dust retention capacity</li><li>Compatible: Suzuki Alto 8th Gen (2019-2024 VXR/VXL/AGS)</li></ul>', 1150.00, 1400.00, 800.00, 65, 8, 1, 1, 0.40, 'Suzuki Alto 660cc Air Filter Price in Pakistan | Guard Filters', 'Buy high-efficiency air filter for Suzuki Alto 660cc 2019-2024. Guard filter ensures maximum engine protection and fuel economy.'),

(6, 'Bosch Clear Advantage All-Weather Beam Wiper Blades Pair', 'bosch-clear-advantage-wiper-blades-pair', 'BOS-WPR-006', 'BOS-CA-2414', '3397006501, AR601S', 6, 10, 'Genuine', '1-Season Warranty', 'Aerodynamic frameless beam wiper blades engineered with graphite-treated dual rubber edge for streak-free wiping in heavy monsoon rain.', '<p>Bosch Clear Advantage wipers feature an aerodynamic wind spoiler that prevents blade lift-off at motorway speeds (up to 130 km/h). The precision-tensioned internal steel spring distributes uniform pressure across the curved windshield for crystal clear visibility in extreme downpours.</p><ul><li>Sizes: 24 inch (Driver) + 14 inch (Passenger)</li><li>Universal J-Hook / U-Hook connector included</li><li>Resistant to intense summer UV heat cracking</li><li>Fits: Toyota Corolla, Honda Civic, Suzuki Swift, Kia Sportage</li></ul>', 2900.00, 3500.00, 2100.00, 40, 5, 1, 1, 0.50, 'Bosch Clear Advantage Wiper Blades Pair Price in Pakistan', 'All-weather graphite-coated beam wiper blades by Bosch. High visibility in heavy rain and dust. Fits Corolla, Civic, Sportage.'),

(7, 'Shell Helix Ultra 5W-40 Fully Synthetic Engine Oil (4 Litres)', 'shell-helix-ultra-5w40-synthetic-4l', 'SHL-OIL-007', 'SHL-HU-5W40-4L', 'API SP, ACEA A3/B4, MB 229.5', 10, 8, 'Genuine', 'Verified Shell Hologram Pack', 'PurePlus Technology fully synthetic motor oil formulated from natural gas to keep engine pistons 65% cleaner than industry benchmarks.', '<p>Shell Helix Ultra 5W-40 with Active Cleansing Technology actively locks away harmful deposits and reduces engine friction to deliver ultimate performance and fuel economy. Provides superior resistance to oil degradation even in peak 48°C Pakistani summer temperatures.</p><ul><li>Grade: SAE 5W-40 API SP / CF</li><li>Volume: 4 Litres Canister with scratch verification code</li><li>Protects against Low-Speed Pre-Ignition (LSPI) in turbocharged engines</li><li>Recommended for: Civic Turbo, Corolla Altis, Sportage, Tucson, Swift</li></ul>', 14200.00, 15800.00, 12200.00, 18, 3, 1, 1, 3.80, 'Shell Helix Ultra 5W-40 4L Engine Oil Price in Pakistan', 'Buy 100% original Shell Helix Ultra 5W-40 fully synthetic engine oil with verifiable scratch code. Ultimate engine protection.'),

(8, 'Suzuki Genuine Front Brake Disc Rotors Pair for Cultus & WagonR', 'suzuki-front-brake-disc-rotors-cultus-wagonr', 'SUZ-ROT-008', '55211-M68P00', 'DF7489, 55211-68K00', 3, 2, 'Genuine', '6-Month Factory Warranty', 'High-carbon cast iron brake discs dynamically balanced to prevent steering vibration, brake pulsation, and premature wear.', '<p>Don\'t compromise on safety. These Pak Suzuki certified front brake discs provide smooth, warp-resistant braking under heavy urban stop-and-go driving. Precision-ground friction faces ensure instantaneous brake pad bed-in.</p><ul><li>Part Number: 55211-M68P00</li><li>Material: G3000 Grade High-Carbon Cast Iron</li><li>Quantity: Pair (Left & Right Disc)</li><li>Fits: Suzuki Cultus (2017-2024) and Suzuki Wagon R (2014-2024)</li></ul>', 12500.00, 14500.00, 9800.00, 12, 2, 0, 1, 7.20, 'Suzuki Cultus & Wagon R Front Brake Discs Price in Pakistan', 'Original Suzuki Genuine front brake disc rotors pair for new Cultus and Wagon R. Factory guaranteed fitment and stopping power.'),

(9, 'Honda Genuine Engine Mount / Mounting (Front Right)', 'honda-civic-reborn-engine-mount-right', 'HON-MNT-009', '50820-SNA-033', '50820-SNA-P01, 50820-SVB-A03', 2, 1, 'Genuine', '6-Month Warranty', 'Hydraulic fluid-filled rubber engine mount engineered to absorb engine vibration and eliminate chassis shuddering during gear shifts.', '<p>The notorious right engine mount failure on Honda Civic Reborn causes violent steering wheel tremors and cabin rattle when stopped in \'D\' (Drive) gear. This genuine hydraulic fluid damper restores silky smooth cabin serenity.</p><ul><li>OEM Part: 50820-SNA-033</li><li>Fluid-filled internal hydraulic chamber</li><li>Heavy-duty stamped steel bracket</li><li>Fits: Honda Civic Reborn (2006-2012 1.8L Automatic & Manual)</li></ul>', 16800.00, 19500.00, 13500.00, 10, 2, 1, 1, 2.40, 'Honda Civic Reborn Right Engine Mount 50820-SNA-033 Price in Pakistan', 'Fix cabin vibration with Genuine Honda Civic Reborn right hydraulic engine mounting. Genuine OEM quality guaranteed.'),

(10, 'Denso Japanese Cabin AC Air Filter (Carbon Activated)', 'denso-cabin-ac-filter-toyota-honda', 'DEN-ACF-010', 'DCC1009', '87139-30040, 80292-TG0-T01', 4, 4, 'OEM', 'Fresh Air Guarantee', '4-stage electrostatically charged activated charcoal cabin filter that traps PM2.5 smog particles, pollens, diesel fumes, and foul odors.', '<p>Breathe clean air inside your vehicle. Denso\'s activated carbon layer neutralizes hazardous sulfur and nitrogen oxides commonly found in heavy city traffic, while keeping your car\'s AC cooling coil clean from dust clogging.</p><ul><li>Traps 98% of smog, dust, soot, and allergens</li><li>Enhances blower fan airflow efficiency</li><li>Recommended replacement: every 10,000 km or 6 months</li><li>Compatible: Corolla (2008-2020), Yaris, Civic, City, Alto</li></ul>', 1650.00, 2000.00, 1100.00, 50, 6, 0, 1, 0.30, 'Denso Cabin AC Air Filter for Toyota & Honda Price in Pakistan', 'Activated charcoal carbon AC cabin air filter by Denso. Cleans cabin air from smog, smoke, and odors. Fits Corolla, Civic, City.'),

(11, 'Honda Genuine Complete Clutch Kit for CG125 Motorcycle', 'honda-cg125-complete-clutch-kit', 'HON-CLT-011', '22201-107-000', 'CG-CLUTCH-GEN', 2, 1, 'Genuine', 'Genuine Atlas Honda Pack', 'Atlas Honda genuine friction plates and steel separator clutch assembly for crisp power transfer and zero slippage on CG125.', '<p>Restore maximum wheel torque and smooth gear transitions on your Honda CG125. High-tensile friction pads resist clutch burn out during heavy loads and stop-and-go city riding.</p><ul><li>Includes: 5x Friction Plates + 4x Steel Drive Plates</li><li>Engineered for 124cc OHV 4-Stroke engine</li><li>Direct Atlas Honda certified factory packaging</li><li>Vehicle: Honda CG125 & CG125 Dream (2012-2024)</li></ul>', 2450.00, 2800.00, 1850.00, 35, 5, 0, 1, 0.65, 'Atlas Honda Genuine Clutch Plates Kit for CG125 Price in Pakistan', 'Buy original Atlas Honda clutch plate set for CG125. Maximum torque transmission with zero clutch slip.'),

(12, 'NGK Laser Iridium Spark Plug for Honda CD70 & 70cc Bikes', 'ngk-c7hsa-iridium-spark-plug-cd70', 'NGK-SPK-012', 'CR7HIX-7544', 'C7HSA, CR7HSA', 5, 5, 'Genuine', '1-Year Reliability', '0.6mm laser-welded iridium spark plug providing lightning fast starts, smoother low-RPM idling, and carbon buildup resistance for 70cc engines.', '<p>Upgrade your standard copper spark plug to NGK Laser Iridium. Delivers instantaneous cold kicks, prevents spark plug fouling in winter mornings, and extends service life up to 30,000 km.</p><ul><li>Tip: Laser-welded Iridium center electrode with platinum ground pad</li><li>Thread Size: 10mm x 12.7mm</li><li>Hex: 16mm</li><li>Fits: Honda CD70, United 70, Road Prince 70, Yamaha YBR125</li></ul>', 2100.00, 2500.00, 1550.00, 55, 10, 0, 1, 0.10, 'NGK Laser Iridium Spark Plug for CD70 Bike Price in Pakistan', 'Original NGK CR7HIX Iridium spark plug for Honda CD70 & 70cc motorbikes. One-kick starting and enhanced fuel mileage.');

-- 6. Product Fitment Compatibility Mapping
INSERT INTO `product_fitment` (`product_id`, `vehicle_id`, `notes`) VALUES
(1, 1, 'Fits all 1.3L, 1.6L and 1.8L variants (2014-2020)'),
(1, 2, 'Fits all 1.6L and 1.8L Altis variants'),
(1, 3, 'Fits both 1.3L and 1.5L Yaris engines'),
(2, 1, 'Recommended gap: 1.1mm'),
(2, 4, 'Pre-gapped for 1.8L i-VTEC'),
(2, 5, 'Exact factory heat range specification'),
(2, 6, 'Optimal ignition performance'),
(3, 1, 'Front axle only; includes wear indicator'),
(3, 2, 'Direct OE replacement'),
(3, 3, 'Confirmed compatibility'),
(4, 4, 'Fits all 1.8L Oriel and 1.5L Turbo variants'),
(5, 7, 'Exact airbox drop-in fit for Suzuki Alto 660cc'),
(6, 1, 'Driver 24\" + Passenger 14\"'),
(6, 4, 'Driver 24\" + Passenger 14\"'),
(6, 12, 'Driver 24\" + Passenger 14\"'),
(7, 1, 'Recommended oil viscosity for high mileage'),
(7, 4, 'Exceptional protection against LSPI in turbo'),
(7, 12, 'Meets MPI engine requirements'),
(8, 8, 'Fits Cultus 2017+ VXR/VXL/AGS models'),
(8, 9, 'Fits all Wagon R 2014+ models'),
(9, 5, 'Hydraulic fluid filled right side mount'),
(10, 1, 'Installed behind glove box'),
(10, 4, 'Quick 5-minute install'),
(10, 6, 'Perfect cabin fit'),
(11, 15, 'Complete 5-plate clutch assembly for CG125'),
(12, 14, 'Fits all 70cc 4-stroke Chinese & Japanese bikes'),
(12, 16, 'Compatible plug reach for YBR125');

-- 7. Product Price Tiers (Wholesale for Workshops & Mechanics)
INSERT INTO `product_price_tiers` (`product_id`, `min_qty`, `unit_price`) VALUES
(1, 3, 1750.00),
(1, 10, 1600.00),
(2, 2, 7200.00),
(2, 5, 6800.00),
(3, 2, 5400.00),
(3, 5, 5000.00),
(4, 2, 36500.00),
(5, 5, 1050.00),
(5, 20, 950.00),
(6, 3, 2600.00),
(7, 3, 13500.00),
(8, 2, 11800.00),
(10, 5, 1450.00),
(11, 3, 2250.00),
(12, 5, 1900.00);

-- 8. Coupons
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_spend`, `max_discount`, `usage_limit`, `usage_count`, `start_date`, `expiry_date`, `is_active`) VALUES
(1, 'PAKSAVE10', 'percent', 10.00, 3000.00, 1500.00, 500, 12, '2026-01-01', '2026-12-31', 1),
(2, 'FIRST500', 'fixed', 500.00, 5000.00, 500.00, 1000, 45, '2026-01-01', '2026-12-31', 1);

-- 9. Shipping Rates by City
INSERT INTO `shipping_rates` (`id`, `city`, `province`, `standard_rate`, `cod_fee`, `free_threshold`, `delivery_days`) VALUES
(1, 'Lahore', 'Punjab', 150.00, 30.00, 4000.00, 'Same Day / Next Day'),
(2, 'Karachi', 'Sindh', 250.00, 50.00, 5000.00, '2-3 Working Days'),
(3, 'Islamabad', 'Federal', 200.00, 40.00, 4500.00, '1-2 Working Days'),
(4, 'Rawalpindi', 'Punjab', 200.00, 40.00, 4500.00, '1-2 Working Days'),
(5, 'Faisalabad', 'Punjab', 200.00, 40.00, 4500.00, '1-2 Working Days'),
(6, 'Multan', 'Punjab', 220.00, 45.00, 5000.00, '2 Working Days'),
(7, 'Peshawar', 'KPK', 250.00, 50.00, 5000.00, '2-3 Working Days'),
(8, 'Quetta', 'Balochistan', 350.00, 70.00, 7000.00, '3-5 Working Days'),
(9, 'Gujranwala', 'Punjab', 180.00, 40.00, 4500.00, '1-2 Working Days'),
(10, 'Sialkot', 'Punjab', 180.00, 40.00, 4500.00, '1-2 Working Days'),
(11, 'Rest of Pakistan', 'All', 280.00, 60.00, 6000.00, '3-5 Working Days');

-- 10. Sample Customer Reviews
INSERT INTO `reviews` (`id`, `product_id`, `customer_name`, `customer_email`, `customer_phone`, `rating`, `comment`, `is_approved`) VALUES
(1, 1, 'Tariq Mehmood', 'tariq.lahore@gmail.com', '03008456123', 5, 'Received 100% genuine Toyota element in original red box with QR code. Fitted perfectly in my 2017 Corolla GLi. Fast delivery via TCS in Lahore!', 1),
(2, 2, 'Farhan Zaidi', 'farhan.khi@yahoo.com', '03212349876', 5, 'Replaced standard copper plugs in Civic Reborn with these Denso Iridiums. Engine idling is super smooth now, and throttle lag has disappeared completely.', 1),
(3, 3, 'Asad Ullah Khan', 'asad.isb@hotmail.com', '03335123456', 5, 'Japanese AISIN pads are completely noise free. No black dust on my alloy rims even after 1,000 km motorway drive. Highly recommended seller!', 1),
(4, 5, 'Usman Bilal', 'usman.alto@gmail.com', '03454789123', 5, 'Guard filter fits Alto 660cc airbox snugly. Quality is equivalent to Japanese imported filter at one-third the price.', 1);

-- 11. Blog Categories & Posts
INSERT INTO `blog_categories` (`id`, `name`, `slug`) VALUES
(1, 'Maintenance Guides', 'maintenance-guides'),
(2, 'Buying Advice', 'buying-advice'),
(3, 'Counterfeit Warnings', 'counterfeit-warnings');

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `category_id`, `tags`, `reading_time_min`, `focus_keyword`, `meta_title`, `meta_description`, `is_published`, `published_at`) VALUES
(1, 'How to Spot Fake vs Genuine Toyota Spare Parts in Pakistan', 'how-to-spot-fake-toyota-parts-pakistan', 'A comprehensive step-by-step visual guide to identifying counterfeit Toyota oil filters, brake pads, and spark plugs in local Pakistani markets.', '<h2>The Epidemic of Counterfeit Auto Parts</h2><p>Auto markets like Montgomery Road Lahore, Sultan Ka Khoo Rawalpindi, and Plaza Karachi are flooded with counterfeit Toyota and Honda spare parts. Installing a cheap copy can lead to catastrophic engine seizure or brake failure.</p><h3>1. Packaging Fonts and Hologram Quality</h3><p>Genuine Toyota packaging utilizes crisp, dark red typography with consistent kerning. Fake boxes often have blurry barcode labels, faded ink, and flimsy cardboard.</p><h3>2. The Oil Filter Micron Test</h3><p>Counterfeit oil filters use cheap porous paper that collapses under high oil pump pressure, allowing unfiltered metal grit to circulate through your engine bearings. Genuine filters use synthetic non-woven pleated media with an internal bypass safety valve.</p><h3>3. QR Verification and Holographic Tape</h3><p>Always inspect the tamper-evident holographic security seal. Genuine seals reflect rainbow spectrums and cannot be peeled off cleanly without leaving a honeycomb pattern.</p>', '/assets/images/blog-toyota-fake.webp', 3, 'Toyota, Spare Parts, Counterfeit, Guide', 5, 'fake vs genuine toyota parts pakistan', 'How to Spot Fake Toyota Spare Parts in Pakistan | PakAutoParts', 'Learn how to identify fake vs original Toyota spare parts in Pakistan. Avoid engine damage with our comprehensive guide.', 1, '2026-10-15 10:00:00'),

(2, 'Best Engine Oil for Cars in Pakistan: Viscosity and Summer Driving Guide', 'best-engine-oil-for-cars-in-pakistan', 'Choosing the correct engine oil grade (0W-20 vs 5W-30 vs 10W-40) for Pakistani summers, extreme heat, and local RON 92 fuel quality.', '<h2>Why Extreme Heat Demands Superior Engine Lubrication</h2><p>When ambient temperatures reach 45°C in Multan, Lahore, and Sukkur, engine oil temperatures can exceed 115°C inside the cylinder head. Low quality mineral oils rapidly oxidize, turning into abrasive black sludge.</p><h3>Modern Japanese Cars (Alto, Yaris, Civic Turbo)</h3><p>Modern engines with variable valve timing (Dual VVT-i, VTEC) feature ultra-tight mechanical clearances. They require 0W-20 or 5W-30 fully synthetic motor oils with API SP certification to prevent Low-Speed Pre-Ignition (LSPI).</p><h3>High Mileage Vehicles (Above 120,000 km)</h3><p>If your engine has accumulated over 120,000 kilometers and shows slight oil consumption between changes, transitioning to a high-shear 5W-40 synthetic oil provides superior piston ring sealing and hydraulic cushion.</p>', '/assets/images/blog-oil-guide.webp', 1, 'Engine Oil, Shell, Maintenance, Summer', 6, 'best engine oil for cars in pakistan', 'Best Engine Oil for Cars in Pakistan (2026 Guide) | PakAutoParts', 'Find the best engine oil for your car in Pakistan. Learn when to use 0W-20, 5W-30 or 5W-40 in harsh summer heat.', 1, '2026-10-20 14:30:00');

-- 12. System Settings
INSERT INTO `settings` (`setting_group`, `setting_key`, `setting_value`) VALUES
('general', 'site_name', 'PakAutoParts'),
('general', 'site_tagline', 'Pakistan\'s Premier Auto Spare Parts Network'),
('general', 'site_logo', '/assets/images/logo.svg'),
('general', 'contact_phone', '0300-1234567'),
('general', 'contact_whatsapp', '923001234567'),
('general', 'contact_email', 'support@pakautoparts.pk'),
('general', 'contact_address', 'Plaza #14, Auto Market, Montgomery Road, Lahore, Pakistan'),
('general', 'announcement_banner', '⚡ Free Nationwide Shipping on all orders above Rs. 5,000! Cash on Delivery available.'),
('payment', 'bank_name', 'Meezan Bank Limited'),
('payment', 'bank_title', 'PAK AUTO PARTS TRADERS'),
('payment', 'bank_account_no', '02010108923412'),
('payment', 'bank_iban', 'PK45MEZN0002010108923412'),
('payment', 'jazzcash_title', 'Muhammad Usman (PakAutoParts)'),
('payment', 'jazzcash_no', '03001234567'),
('payment', 'easypaisa_title', 'Muhammad Usman (PakAutoParts)'),
('payment', 'easypaisa_no', '03001234567'),
('shipping', 'default_shipping_fee', '250'),
('shipping', 'cod_fee', '50'),
('shipping', 'free_shipping_min', '5000');

