-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 29 مايو 2025 الساعة 04:44
-- إصدار الخادم: 8.3.0
-- PHP Version: 8.2.18

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
-- بنية الجدول `brands`
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
-- بنية الجدول `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `level` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
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
(17, 'الدواجن', NULL, 1, '2025-05-25 14:33:42', '2025-05-25 14:33:42', NULL, 16, 3);

-- --------------------------------------------------------

--
-- بنية الجدول `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('retail','wholesale') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'retail',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `type`, `balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'محمد على الشهراني', 'atefakl90@gmail.com', '0548676841', 'Alnoor', 'wholesale', 60.00, '2025-05-16 17:06:02', '2025-05-16 18:27:20', NULL),
(2, 'محمد ابو ابراهيم', 'atefakl70@gmail.com', '0548676842', 'Alnoor', 'retail', 0.00, '2025-05-16 17:11:47', '2025-05-16 17:11:47', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `files`
--

INSERT INTO `files` (`id`, `display_name`, `file_name`, `original_name`, `mime_type`, `extension`, `size`, `path`, `category`, `alt_text`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'بيتزا', '1748461176_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/product_image/1748461176_oop-design-patterns.png', NULL, 'بيتزا', 1, '2025-05-28 16:39:36', '2025-05-28 16:39:36'),
(2, 'هلا هلا', '1748461289_my-sig.png', 'My Sig.png', 'image/png', 'png', 667260, 'products/3/other/1748461289_my-sig.png', NULL, 'هلا هلا', 1, '2025-05-28 16:41:29', '2025-05-28 16:41:29'),
(3, 'المنتج', '1748461685_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/other/1748461685_oop-design-patterns.png', NULL, 'المنتج', 1, '2025-05-28 16:48:05', '2025-05-28 16:48:05'),
(4, 'هلا هلا', '1748461780_mahmoud-tharwat.pdf', 'Mahmoud Tharwat.pdf', 'application/pdf', 'pdf', 90303, 'products/3/other/1748461780_mahmoud-tharwat.pdf', NULL, 'هلا هلا', 1, '2025-05-28 16:49:40', '2025-05-28 16:49:40'),
(5, 'المنتج', '1748462138_mahmoud-tharwat.pdf', 'Mahmoud Tharwat.pdf', 'application/pdf', 'pdf', 90303, 'products/3/gallery_image/1748462138_mahmoud-tharwat.pdf', NULL, 'المنتج', 1, '2025-05-28 16:55:38', '2025-05-28 16:55:38'),
(6, 'هلا هلا', '1748462482_oop-design-patterns.png', 'OOP Design Patterns.png', 'image/png', 'png', 1057786, 'products/3/other/1748462482_oop-design-patterns.png', NULL, 'هلا هلا', 1, '2025-05-28 17:01:22', '2025-05-28 17:01:22');

-- --------------------------------------------------------

--
-- بنية الجدول `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','partially_paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `subtotal`, `tax`, `discount`, `total`, `status`, `paid_amount`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INV-2505-00001', 1, 60.00, 0.00, 0.00, 60.00, 'paid', 60.00, NULL, 1, '2025-05-16 18:27:20', '2025-05-20 09:07:46', '2025-05-20 09:07:46');

-- --------------------------------------------------------

--
-- بنية الجدول `invoice_items`
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

--
-- إرجاع أو استيراد بيانات الجدول `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_id`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 20.00, 60.00, '2025-05-16 18:27:20', '2025-05-16 18:27:20');

-- --------------------------------------------------------

--
-- بنية الجدول `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `media`
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
) ;

-- --------------------------------------------------------

--
-- بنية الجدول `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `migrations`
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
(10, '2025_05_16_185000_modify_permission_string_lengths', 1);

-- --------------------------------------------------------

--
-- بنية الجدول `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `offers`
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
-- بنية الجدول `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT '0.00',
  `retail_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_quantity` int DEFAULT '1',
  `is_service` tinyint(1) DEFAULT '0',
  `service_duration` int DEFAULT NULL COMMENT 'Duration in minutes',
  `quantity` int NOT NULL DEFAULT '0',
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL COMMENT 'Weight in kg',
  `dimensions` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Format: LxWxH in cm',
  `alert_quantity` int NOT NULL DEFAULT '0',
  `reorder_level` int DEFAULT '5',
  `category_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `tax_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_tax_id_foreign` (`tax_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--


INSERT INTO `products` (`id`, `name`, `code`, `barcode`, `sku`, `price`, `tax_rate`, `retail_price`, `wholesale_price`, `wholesale_quantity`, `is_service`, `service_duration`, `quantity`, `unit`, `weight`, `dimensions`, `alert_quantity`, `reorder_level`, `category_id`, `unit_id`, `tax_id`, `supplier_id`, `brand_id`, `description`, `image`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'زبادى لبنيتا 120 جم بطعم الفراولة', 'PRO-10001', '628707017502', 'MZST120', 1.00, 15.00, 1.40, 1.38, 12, 0, 60, 6000, 'قطعة', 0.120, NULL, 5000, 4000, 14, NULL, NULL, NULL, NULL, NULL, 'uploads/products/gallery/1747951054_coe-manufacturing-png-4.png', 1, '2025-05-22 18:57:34', '2025-05-22 18:57:34', NULL),
(3, 'بط بكينى 750/1', 'PRD-111-1001', '628707017512', 'pkn-cool-800-1', 10.00, 15.00, 24.00, 22.00, 6, 0, 60, 100, 'قطعة', 800.000, NULL, 15, 10, 17, NULL, NULL, NULL, NULL, NULL, 'products/gallery/1748298057_OOP Design Patterns.png', 1, '2025-05-25 15:03:40', '2025-05-30 18:24:40', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `product_files`
--

DROP TABLE IF EXISTS `product_files`;
CREATE TABLE IF NOT EXISTS `product_files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `file_id` bigint UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `the_order` int UNSIGNED DEFAULT NULL,
  `category` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_files_product` (`product_id`),
  KEY `fk_product_files_file` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `product_files`
--

INSERT INTO `product_files` (`id`, `product_id`, `file_id`, `type`, `created_at`, `updated_at`, `the_order`, `category`, `is_active`) VALUES
(1, 3, 5, NULL, '2025-05-28 16:55:38', '2025-05-28 16:55:38', 1, 'gallery_image', 1),
(2, 3, 6, NULL, '2025-05-28 17:01:22', '2025-05-28 17:01:22', 1, 'other', 1);

-- --------------------------------------------------------

--
-- بنية الجدول `product_images`
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

--
-- إرجاع أو استيراد بيانات الجدول `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'products/gallery/ywxPer0hy7cDosnDvDJ2XssNqX6yFqtV8OwT8qQV.jpg', '2025-05-26 15:25:37', '2025-05-26 19:15:19', '2025-05-26 19:15:19'),
(2, 3, 'products/gallery/gwspNPg7dZhF4N6qLpBl5KgJOBlBw7lo3z0kuSUu.jpg', '2025-05-26 17:34:17', '2025-05-26 19:06:42', '2025-05-26 19:06:42'),
(3, 3, 'products/gallery/22BSqhqCSqjLbJeORm1o1xWwqSiYdVFvVb2XcNw7.jpg', '2025-05-26 17:35:19', '2025-05-26 19:06:55', '2025-05-26 19:06:55'),
(4, 3, 'products/gallery/4zXQcse89oWYnC59xlVKgE6RPJaTOCNBZdaStXjA.jpg', '2025-05-26 17:38:50', '2025-05-26 19:06:51', '2025-05-26 19:06:51'),
(5, 3, 'C:\\wamp64\\www\\pos\\public\\uploads/products/gallery\\1748292393_380417054_695191532656986_4828841855295824703_n.jpg', '2025-05-26 17:46:33', '2025-05-26 19:05:42', '2025-05-26 19:05:42'),
(6, 3, 'products/gallery/1748298243_My Sig.png', '2025-05-26 19:24:03', '2025-05-26 19:24:03', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `role_has_permissions`
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
-- بنية الجدول `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('RjBLylfFScD7lahPDinlhHjBO8FWHfUmZ4N59VMV', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVVMZEo2NFZpTlU3MVRNUHJBdkgwd3dHUEdOWGJJbW1VQ1BOQWRJWiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMTA6Imh0dHA6Ly93d3cucG9zLmxvY2FsL3Byb2R1Y3RzLzMvZWRpdD9fdG9rZW49Wlc1aDB1ZUV3aGFqTmxxOE1idmp6dFNaOTU5YXNyQTFOZ2hkbjA5MyZhbHRfdGV4dD0mY2F0ZWdvcnk9cHJvZHVjdF9pbWFnZSZkaXNwbGF5X25hbWU9JmZpbGU9T09QJTIwRGVzaWduJTIwUGF0dGVybnMucG5nJmlzX2FjdGl2ZT0xJnJlbGF0ZWRfaWQ9MyZyZWxhdGVkX3R5cGU9cHJvZHVjdCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxMDoiaHR0cDovL3d3dy5wb3MubG9jYWwvcHJvZHVjdHMvMy9lZGl0P190b2tlbj1aVzVoMHVlRXdoYWpObHE4TWJ2anp0U1o5NTlhc3JBMU5naGRuMDkzJmFsdF90ZXh0PSZjYXRlZ29yeT1wcm9kdWN0X2ltYWdlJmRpc3BsYXlfbmFtZT0mZmlsZT1PT1AlMjBEZXNpZ24lMjBQYXR0ZXJucy5wbmcmaXNfYWN0aXZlPTEmcmVsYXRlZF9pZD0zJnJlbGF0ZWRfdHlwZT1wcm9kdWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1748459316),
('70KjXJhGClM86u1cT98mIbsdTNvWhH86LBqtGhMW', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUWxZR1NQSmNPUGU4VTh6RDE1dmlWUjhVVnBmc1hYeG1BcVhQVGN4ayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly93d3cucG9zLmxvY2FsL3Byb2R1Y3RzLzMvZWRpdCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzQ4MjkzMzQ0O319', 1748293541),
('nMaFyRGMnpxvjpriKsUUJ1MumjbLS3hecd7qjgnB', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ092aUlBWE8zY3dKdjV1RWVFTWd0U2pqNmpFZnZXd0dheUNOQ0I4WiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly93d3cucG9zLmxvY2FsL3Byb2R1Y3RzLzMvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzQ4MjkzNjM0O319', 1748299224),
('ovJmujL0kVmmRTxiwBV4tKf2ma7oWs3SRi7Pf6ry', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSWs3anZOMGpFalJCRW5EdFk4dmVtMDZleVBuQjhGZkl3MXk3dEs3NCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovL3d3dy5wb3MubG9jYWwvcHJvZHVjdHMvMy9lZGl0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly93d3cucG9zLmxvY2FsL3Byb2R1Y3RzLzMvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1748293650),
('1eXFqxDxBeixzxlvhqhTliAjQcjZiidXcc3egPTr', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlJIenhUWUFDcHRNV2hDWUt4dFMycUVwUUtYVzFYM3J4N1B0aDZmMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly93d3cucG9zLmxvY2FsL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1748293650),
('Q6R7CFnuaG9pbmUY8YvUoXiS5PsSZD2zATrdD9Ka', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRHAxOWtYWHJZcXRqOExmSGFESjZhMk5JZ2pmR3Fnbkw3ZXd0eXR1WiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vd3d3LnBvcy5sb2NhbC9wcm9kdWN0cy8zL2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc0ODM0MjI2MDt9fQ==', 1748347459),
('xHUTOQ9yn6HFiRXkU8Qus2Bn43RPEMmGmf92e3zI', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWlc1aDB1ZUV3aGFqTmxxOE1idmp6dFNaOTU5YXNyQTFOZ2hkbjA5MyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vd3d3LnBvcy5sb2NhbC9wcm9kdWN0cy8zL2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc0ODQ1Nzk3Njt9fQ==', 1748462482),
('SQoE0MGDSt2nqbHwhYdm87dmn9wpkckJowRli6Qq', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQ0NZcWxObEtJbFdqUGNOcGR3bEJ2WkR0T1I0ZVc5UTRvbENWa1JERSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMDQ6Imh0dHA6Ly93d3cucG9zLmxvY2FsL3Byb2R1Y3RzLzMvZWRpdD9fdG9rZW49Wlc1aDB1ZUV3aGFqTmxxOE1idmp6dFNaOTU5YXNyQTFOZ2hkbjA5MyZhbHRfdGV4dD0mY2F0ZWdvcnk9cHJvZHVjdF9pbWFnZSZkaXNwbGF5X25hbWU9JmZpbGU9TWFobW91ZCUyMFRoYXJ3YXQucGRmJmlzX2FjdGl2ZT0xJnJlbGF0ZWRfaWQ9MyZyZWxhdGVkX3R5cGU9cHJvZHVjdCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIwNDoiaHR0cDovL3d3dy5wb3MubG9jYWwvcHJvZHVjdHMvMy9lZGl0P190b2tlbj1aVzVoMHVlRXdoYWpObHE4TWJ2anp0U1o5NTlhc3JBMU5naGRuMDkzJmFsdF90ZXh0PSZjYXRlZ29yeT1wcm9kdWN0X2ltYWdlJmRpc3BsYXlfbmFtZT0mZmlsZT1NYWhtb3VkJTIwVGhhcndhdC5wZGYmaXNfYWN0aXZlPTEmcmVsYXRlZF9pZD0zJnJlbGF0ZWRfdHlwZT1wcm9kdWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1748459068);

-- --------------------------------------------------------

--
-- بنية الجدول `suppliers`
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
-- بنية الجدول `taxes`
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
-- إرجاع أو استيراد بيانات الجدول `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `code`, `rate`, `type`, `is_active`, `is_default`, `description`, `applies_to_all`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'بدون ضريبة', 'TAX_FREE', 0.00, 'percentage', 1, 1, 'لا توجد ضريبة', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(2, 'ضريبة القيمة المضافة', 'VAT', 15.00, 'percentage', 1, 0, 'ضريبة القيمة المضافة 15%', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(3, 'ضريبة المبيعات', 'SALES_TAX', 10.00, 'percentage', 1, 0, 'ضريبة المبيعات 10%', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(4, 'ضريبة سياحية', 'TOURISM_TAX', 5.00, 'percentage', 1, 0, 'ضريبة الخدمة السياحية', 0, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL),
(5, 'ضريبة بيئية', 'ENV_TAX', 1.00, 'percentage', 1, 0, 'ضريبة حماية البيئة', 1, NULL, NULL, '2025-05-23 12:10:31', '2025-05-23 12:10:31', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `tax_product`
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
-- بنية الجدول `units`
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
-- إرجاع أو استيراد بيانات الجدول `units`
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
-- بنية الجدول `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Atef Akl', 'atefakl80@gmail.com', NULL, '$2y$12$gyilrNQrWjcgz4VVWHldxec9LLGS31i.zFuPcFJ6cdtIShmgDT4eC', NULL, '2025-05-16 15:58:31', '2025-05-16 15:58:31');

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `product_files`
--
ALTER TABLE `product_files`
  ADD CONSTRAINT `fk_product_files_file` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_files_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `tax_product`
--
ALTER TABLE `tax_product`
  ADD CONSTRAINT `tax_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tax_product_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
