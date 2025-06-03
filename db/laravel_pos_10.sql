-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 02, 2025 at 03:28 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brands_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `level` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_level_is_active_index` (`parent_id`,`level`,`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `parent_id`, `level`) VALUES
(3, 'اللحوم والأسماك', 'اللحوم المذبوحة حديثا والمحفوظة فى درجة حرارة التبريد لمدة لا تزيد عن 7 أيام', 1, '2025-05-20 09:22:41', '2025-05-20 16:51:05', NULL, NULL, 1),
(4, 'منتجات الألبان والبيض', 'منتجات الألبان والبيض وما فى مستواها من القشدة والزبادى واأشباه الجين', 1, '2025-05-20 16:52:10', '2025-05-22 14:28:14', NULL, NULL, 1),
(5, 'الزبادى', 'منتجات ألبان سائلة أو ذات قوام كريمي خفيف ونكهة حموضة منخفضة', 1, '2025-05-20 16:53:21', '2025-05-22 17:42:49', NULL, 4, 2),
(6, 'الحليب بأنواعه', NULL, 1, '2025-05-20 16:57:34', '2025-05-21 07:06:02', '2025-05-21 07:06:02', NULL, 1),
(7, 'الحليب بأنواعه', NULL, 1, '2025-05-20 16:58:03', '2025-05-21 07:04:49', '2025-05-21 07:04:49', 4, 2),
(8, 'الحليب بأنواعه', NULL, 1, '2025-05-21 07:06:22', '2025-05-21 07:06:50', '2025-05-21 07:06:50', 4, 2),
(9, 'الحليب بأنواعه', 'كل أنواع الحليب السائل', 1, '2025-05-21 07:07:16', '2025-05-22 17:39:57', NULL, 4, 2),
(10, 'زبادى كامل الدسم', 'الزبادى السادة', 1, '2025-05-21 08:10:47', '2025-05-21 08:10:47', NULL, 5, 3),
(11, 'زبادى قليل الدسم', 'الزبادى السادة', 1, '2025-05-21 08:11:23', '2025-05-21 08:11:23', NULL, 5, 3),
(12, 'الأجبان', 'منتجات الألبان من الجبن بأنواعه', 1, '2025-05-21 08:18:03', '2025-05-21 08:18:03', NULL, 4, 2),
(13, 'زبادى منزوع الدسم', 'زبادى منزوع الدسم', 1, '2025-05-21 08:21:22', '2025-05-21 08:21:22', NULL, 5, 3),
(14, 'زبادى بالفواكه', 'زبادى بالفواكه', 1, '2025-05-21 08:22:04', '2025-05-21 08:22:04', NULL, 5, 3),
(15, 'اللبن الحليب', 'اللبن الحليب السائل عمر يوم الى 5 أيام', 1, '2025-05-22 17:45:15', '2025-05-22 17:45:15', NULL, 9, 3),
(16, 'الطيور', NULL, 1, '2025-05-25 14:32:35', '2025-05-25 14:32:35', NULL, 3, 2),
(17, 'الدواجن', NULL, 1, '2025-05-25 14:33:42', '2025-05-25 14:33:42', NULL, 16, 3),
(18, 'الطيور', NULL, 1, '2025-05-25 14:33:42', '2025-05-25 14:33:42', NULL, 16, 3);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('retail','wholesale') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'retail',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `type`, `balance`, `created_at`, `updated_at`, `deleted_at`, `is_active`) VALUES
(1, 'محمد على الشهراني', 'atefakl90@gmail.com', '0548676841', 'Alnoor', 'wholesale', 60.00, '2025-05-16 17:06:02', '2025-05-16 18:27:20', NULL, 1),
(2, 'محمد ابو ابراهيم', 'atefakl70@gmail.com', '0548676842', 'Alnoor', 'retail', 0.00, '2025-05-16 17:11:47', '2025-05-16 17:11:47', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `display_name`, `file_name`, `original_name`, `mime_type`, `extension`, `size`, `path`, `category`, `alt_text`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'بيتزا', '1748461176_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/product_image/1748461176_oop-design-patterns.png', NULL, 'بيتزا', 1, '2025-05-28 16:39:36', '2025-05-28 16:39:36'),
(2, 'هلا هلا', '1748461289_my-sig.png', 'My Sig.png', 'image/png', 'png', 667260, 'products/3/other/1748461289_my-sig.png', NULL, 'هلا هلا', 1, '2025-05-28 16:41:29', '2025-05-28 16:41:29'),
(3, 'المنتج', '1748461685_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/other/1748461685_oop-design-patterns.png', NULL, 'المنتج', 1, '2025-05-28 16:48:05', '2025-05-28 16:48:05'),
(4, 'هلا هلا', '1748461780_mahmoud-tharwat.pdf', 'Mahmoud Tharwat.pdf', 'application/pdf', 'pdf', 90303, 'products/3/other/1748461780_mahmoud-tharwat.pdf', NULL, 'هلا هلا', 1, '2025-05-28 16:49:40', '2025-05-28 16:49:40'),
(5, 'المنتج', '1748462138_mahmoud-tharwat.pdf', 'Mahmoud Tharwat.pdf', 'application/pdf', 'pdf', 90303, 'products/3/gallery_image/1748462138_mahmoud-tharwat.pdf', NULL, 'المنتج', 1, '2025-05-28 16:55:38', '2025-05-28 16:55:38'),
(6, 'هلا هلا', '1748462482_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/other/1748462482_oop-design-patterns.png', NULL, 'هلا هلا', 1, '2025-05-28 17:01:22', '2025-05-28 17:01:22'),
(8, 'ملف جديد', '1748530424_396743142-831056165692405-5146066344081851925-n.jpg', '396743142_831056165692405_5146066344081851925_n.jpg', 'image/jpeg', 'jpg', 109201, 'products/3/gallery_image/1748530424_396743142-831056165692405-5146066344081851925-n.jpg', NULL, 'ملف جديد للمعرض', 1, '2025-05-29 11:53:44', '2025-05-29 11:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','partially_paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_items_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_16_135237_create_permission_tables', 1),
(5, '2025_05_16_135807_create_products_table', 1),
(6, '2025_05_16_135808_create_categories_table', 1),
(7, '2025_05_16_135808_create_customers_table', 1),
(8, '2025_05_16_135809_create_invoices_table', 1),
(9, '2025_05_16_135812_create_invoice_items_table', 1),
(10, '2025_05_16_185000_modify_permission_string_lengths', 1),
(11, '2025_05_17_000000_create_payments_table', 2),
(12, '2025_05_19_000001_create_brands_table', 3),
(13, '2025_05_19_000001_update_categories_table', 3),
(14, '2025_05_19_000002_create_suppliers_table', 4),
(15, '2025_06_02_074136_create_sales_table', 5),
(16, '2025_06_02_074342_create_sale_items_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE IF NOT EXISTS `offers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `offer_price` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offers_product_id_foreign` (`product_id`),
  KEY `offers_product_id_is_active_start_date_end_date_index` (`product_id`,`is_active`,`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT '0.00',
  `retail_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_quantity` int DEFAULT '1',
  `is_service` tinyint(1) DEFAULT '0',
  `service_duration` int DEFAULT NULL COMMENT 'Duration in minutes',
  `quantity` int NOT NULL DEFAULT '0',
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL COMMENT 'Weight in kg',
  `dimensions` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: LxWxH in cm',
  `alert_quantity` int NOT NULL DEFAULT '0',
  `reorder_level` int DEFAULT '5',
  `category_id` bigint UNSIGNED NOT NULL,
  `sub_category_id` bigint UNSIGNED DEFAULT NULL,
  `parent_category_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `tax_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_tax_id_foreign` (`tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `barcode`, `sku`, `price`, `tax_rate`, `retail_price`, `wholesale_price`, `wholesale_quantity`, `is_service`, `service_duration`, `quantity`, `unit`, `weight`, `dimensions`, `alert_quantity`, `reorder_level`, `category_id`, `sub_category_id`, `parent_category_id`, `unit_id`, `tax_id`, `supplier_id`, `brand_id`, `description`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'زبادى لبنيتا 120 جم بطعم الفراولة', 'PRO-10001', '628707017502', 'MZST120', 1.00, 15.00, 1.40, 1.38, 12, 0, 60, 5995, 'قطعة', 0.120, NULL, 5000, 4000, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/1747951054_coe-manufacturing-png-4.png', 1, '2025-05-22 15:57:34', '2025-06-02 05:26:05', NULL),
(3, 'بط بكينى 750/1', 'PRD-111-1001', '628707017512', 'pkn-cool-800-1', 10.00, 15.00, 24.00, 22.00, 6, 0, 60, 100, 'قطعة', 800.000, NULL, 15, 10, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'products/gallery/1748298057_OOP Design Patterns.png', 1, '2025-05-25 12:03:40', '2025-05-30 15:24:40', NULL),
(4, 'زبادى لبنيتا 170 جم بالفراولة', 'PRO-14001', '628700000001', 'SKU14001', 5.00, 10.00, 6.00, 5.50, 10, 0, 30, 500, 'قطعة', 0.500, NULL, 100, 50, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/1.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:28:21', NULL),
(5, 'زبادى لبنيتا 170 جم بالموز', 'PRO-14002', '628700000002', 'SKU14002', 6.00, 10.00, 7.00, 6.50, 10, 0, 25, 400, 'قطعة', 0.400, NULL, 80, 40, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/2.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:29:03', NULL),
(6, 'زبادى لبنيتا 120 جم بالموز', 'PRO-14003', '628700000003', 'SKU14003', 7.00, 10.00, 8.00, 7.50, 10, 0, 20, 300, 'قطعة', 0.300, NULL, 60, 30, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/3.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:29:24', NULL),
(7, 'زبادى لبنيتا 120 جم بالمانجو', 'PRO-14004', '628700000004', 'SKU14004', 8.00, 10.00, 9.00, 8.50, 10, 0, 15, 199, 'قطعة', 0.200, NULL, 40, 20, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/4.png', 1, '2025-05-31 14:52:01', '2025-06-02 05:12:09', NULL),
(8, 'بط بكينى 1500/1', 'PRO-17001', '628700001001', 'SKU17001', 15.00, 12.00, 16.00, 15.50, 8, 0, 40, 700, 'قطعة', 0.700, NULL, 120, 60, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/5.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:30:22', NULL),
(9, 'بط بكينى 1500/2', 'PRO-17002', '628700001002', 'SKU17002', 16.00, 12.00, 17.00, 16.50, 8, 0, 35, 600, 'قطعة', 0.600, NULL, 90, 45, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/6.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:30:48', NULL),
(10, 'بط بكينى 1750/2', 'PRO-17003', '628700001003', 'SKU17003', 17.00, 12.00, 18.00, 17.50, 8, 0, 30, 496, 'قطعة', 0.500, NULL, 70, 35, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/7.png', 1, '2025-05-31 14:52:01', '2025-06-02 05:26:05', NULL),
(11, 'بط بكينى 2000/2', 'PRO-17004', '628700001004', 'SKU17004', 18.00, 12.00, 19.00, 18.50, 8, 0, 25, 396, 'قطعة', 0.400, NULL, 50, 25, 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/8.png', 1, '2025-05-31 14:52:01', '2025-06-02 05:26:05', NULL),
(12, 'بوادي - حمام 1000/2', 'PRO-20001', '628700002001', 'SKU20001', 20.00, 13.00, 22.00, 21.00, 5, 0, 50, 900, 'قطعة', 0.900, NULL, 140, 70, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/9.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:35:28', NULL),
(13, 'بوادي - حمام 1400/3', 'PRO-20002', '628700002002', 'SKU20002', 21.00, 13.00, 23.00, 22.00, 5, 0, 45, 800, 'قطعة', 0.800, NULL, 110, 55, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/10.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:35:53', NULL),
(14, 'هجرة حمام - 1000/2', 'PRO-20003', '628700002003', 'SKU20003', 22.00, 13.00, 24.00, 23.00, 5, 0, 40, 700, 'قطعة', 0.700, NULL, 90, 45, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/11.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:36:26', NULL),
(15, 'هجرة حمام - 1400/3', 'PRO-20004', '628700002004', 'SKU20004', 23.00, 13.00, 25.00, 24.00, 5, 0, 35, 600, 'قطعة', 0.600, NULL, 70, 35, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/12.png', 1, '2025-05-31 14:52:01', '2025-05-31 14:36:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_files`
--

DROP TABLE IF EXISTS `product_files`;
CREATE TABLE IF NOT EXISTS `product_files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `file_id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `the_order` int UNSIGNED DEFAULT NULL,
  `category` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_files_product` (`product_id`),
  KEY `fk_product_files_file` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `user_id`, `total_amount`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 46.00, '', '2025-06-02 05:12:09', '2025-06-02 05:12:09'),
(2, 2, 1, 118.00, '', '2025-06-02 05:26:05', '2025-06-02 05:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `sub_total`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 1, 19.00, 19.00, '2025-06-02 05:12:09', '2025-06-02 05:12:09'),
(2, 1, 10, 1, 18.00, 18.00, '2025-06-02 05:12:09', '2025-06-02 05:12:09'),
(3, 1, 7, 1, 9.00, 9.00, '2025-06-02 05:12:09', '2025-06-02 05:12:09'),
(4, 2, 10, 3, 18.00, 54.00, '2025-06-02 05:26:05', '2025-06-02 05:26:05'),
(5, 2, 11, 3, 19.00, 57.00, '2025-06-02 05:26:05', '2025-06-02 05:26:05'),
(6, 2, 2, 5, 1.40, 7.00, '2025-06-02 05:26:05', '2025-06-02 05:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('wRgYDBDNFuyeplDJSN2SXC4OvzOBIJdCnTZAmp8U', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoieXFrdXlKOWsySGJBVGFEU3FmdkZJOUN1a0NxTmlpbVl2SkxaQlpXUiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vd3d3LmxhcmF2ZWwucG9zL3JlcG9ydHMvc2FsZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc0ODg0NjE5Mjt9fQ==', 1748852951),
('MWKVMDv9HaP2iplSvIaQVEsNj2UfBCaNS3YxuAnV', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRFlQcTh1cjRNTU01Rlh1ZVhvWGFiWHV0MHNoTG9YaWxtb2NpTzY3MiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI2OiJodHRwOi8vd3d3LmxhcmF2ZWwucG9zL3BvcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzQ4ODczNDM3O319', 1748877958);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tax_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
CREATE TABLE IF NOT EXISTS `taxes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(8,2) NOT NULL COMMENT 'Tax rate in percentage',
  `type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `applies_to_all` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `code`, `rate`, `type`, `is_active`, `is_default`, `description`, `applies_to_all`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'بدون ضريبة', 'TAX_FREE', 0.00, 'percentage', 1, 1, 'لا توجد ضريبة', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(2, 'ضريبة القيمة المضافة', 'VAT', 15.00, 'percentage', 1, 0, 'ضريبة القيمة المضافة 15%', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(3, 'ضريبة المبيعات', 'SALES_TAX', 10.00, 'percentage', 1, 0, 'ضريبة المبيعات 10%', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(4, 'ضريبة سياحية', 'TOURISM_TAX', 5.00, 'percentage', 1, 0, 'ضريبة الخدمة السياحية', 0, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(5, 'ضريبة بيئية', 'ENV_TAX', 1.00, 'percentage', 1, 0, 'ضريبة حماية البيئة', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax_product`
--

DROP TABLE IF EXISTS `tax_product`;
CREATE TABLE IF NOT EXISTS `tax_product` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tax_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tax_product_tax_id_product_id_unique` (`tax_id`,`product_id`),
  KEY `tax_product_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'الاختصار مثل كجم، لتر، م، إلخ',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_created_by_foreign` (`created_by`),
  KEY `units_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `short_name`, `description`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'قطعة', 'قطعة', 'الوحدة الأساسية للعد', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(2, 'كيلوجرام', 'كجم', 'وحدة قياس الوزن', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(3, 'جرام', 'جم', 'وحدة قياس الوزن', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(4, 'لتر', 'لتر', 'وحدة قياس الحجم', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(5, 'ملليلتر', 'مل', 'وحدة قياس الحجم', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(6, 'متر', 'م', 'وحدة قياس الطول', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(7, 'سنتيمتر', 'سم', 'وحدة قياس الطول', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(8, 'متر مربع', 'م²', 'وحدة قياس المساحة', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(9, 'متر مكعب', 'م³', 'وحدة قياس الحجم', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(10, 'علبة', 'علبة', 'وحدة التغليف', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(11, 'كرتونة', 'كرتونة', 'وحدة التغليف', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(12, 'زجاجة', 'زجاجة', 'وحدة التغليف', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(13, 'علبة صغيرة', 'علبة صغيرة', 'وحدة التغليف', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL),
(14, 'علبة كبيرة', 'علبة كبيرة', 'وحدة التغليف', 1, NULL, NULL, '2025-05-23 12:04:40', '2025-05-23 12:04:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Atef Akl', 'atefakl80@gmail.com', NULL, '$2y$12$gyilrNQrWjcgz4VVWHldxec9LLGS31i.zFuPcFJ6cdtIShmgDT4eC', 'YpNdN7Swd2Fvts2YsMyczAYdpv3841jSAL9rIyZdiR76F0CEm3QXcn5TVutK', '2025-05-16 15:58:31', '2025-05-16 15:58:31');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_files`
--
ALTER TABLE `product_files`
  ADD CONSTRAINT `fk_product_files_file` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_files_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tax_product`
--
ALTER TABLE `tax_product`
  ADD CONSTRAINT `tax_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tax_product_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
