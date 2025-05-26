-- إنشاء جدول العروض (offers) بمعايير فنية متبعة في أنظمة ERP
CREATE TABLE `offers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL COMMENT 'كود العرض الفريد',
    `name` VARCHAR(255) NOT NULL COMMENT 'اسم العرض',
    `description` TEXT NULL COMMENT 'وصف تفصيلي للعرض',
    `type` ENUM('percentage', 'fixed', 'buy_x_get_y', 'bundle') NOT NULL DEFAULT 'percentage' COMMENT 'نوع العرض: نسبة مئوية، قيمة ثابتة، اشتر X واحصل على Y، حزمة',
    `value` DECIMAL(10,2) NULL COMMENT 'قيمة العرض (نسبة مئوية أو قيمة ثابتة)',
    `min_qty` INT UNSIGNED NULL COMMENT 'الحد الأدنى للكمية المطلوبة للاستفادة من العرض',
    `max_qty` INT UNSIGNED NULL COMMENT 'الحد الأقصى للكمية المسموح بها للاستفادة من العرض',
    `min_amount` DECIMAL(10,2) NULL COMMENT 'الحد الأدنى لقيمة الطلب للاستفادة من العرض',
    `max_discount` DECIMAL(10,2) NULL COMMENT 'الحد الأقصى لقيمة الخصم',
    `start_date` DATETIME NOT NULL COMMENT 'تاريخ بداية العرض',
    `end_date` DATETIME NULL COMMENT 'تاريخ نهاية العرض (NULL = غير محدد)',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'حالة العرض: 1=نشط، 0=غير نشط',
    `priority` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'أولوية العرض (الأعلى = الأهم)',
    `usage_limit` INT UNSIGNED NULL COMMENT 'الحد الأقصى لعدد مرات استخدام العرض',
    `usage_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'عدد مرات استخدام العرض',
    `is_exclusive` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'هل العرض حصري (لا يمكن جمعه مع عروض أخرى)',
    `applies_to` ENUM('all', 'products', 'categories', 'customers', 'customer_groups') NOT NULL DEFAULT 'all' COMMENT 'نطاق تطبيق العرض',
    `created_by` BIGINT UNSIGNED NULL COMMENT 'معرف المستخدم الذي أنشأ العرض',
    `updated_by` BIGINT UNSIGNED NULL COMMENT 'معرف المستخدم الذي قام بآخر تحديث للعرض',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ الإنشاء',
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاريخ آخر تحديث',
    `deleted_at` TIMESTAMP NULL COMMENT 'تاريخ الحذف (للحذف الناعم)',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `offers_code_unique` (`code`),
    INDEX `offers_type_index` (`type`),
    INDEX `offers_start_date_end_date_index` (`start_date`, `end_date`),
    INDEX `offers_is_active_index` (`is_active`),
    INDEX `offers_applies_to_index` (`applies_to`),
    INDEX `offers_created_by_index` (`created_by`),
    INDEX `offers_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول العروض والخصومات';

-- إنشاء جدول العلاقة بين العروض والمنتجات
CREATE TABLE `offer_product` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `offer_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف العرض',
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف المنتج',
    `discount_value` DECIMAL(10,2) NULL COMMENT 'قيمة الخصم المخصصة لهذا المنتج (إذا كانت مختلفة عن قيمة العرض الأساسية)',
    `min_qty` INT UNSIGNED NULL COMMENT 'الحد الأدنى للكمية المطلوبة لهذا المنتج',
    `max_qty` INT UNSIGNED NULL COMMENT 'الحد الأقصى للكمية المسموح بها لهذا المنتج',
    `is_free_item` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'هل هذا المنتج مجاني كجزء من العرض (للعروض من نوع buy_x_get_y)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `offer_product_offer_id_product_id_unique` (`offer_id`, `product_id`),
    INDEX `offer_product_product_id_index` (`product_id`),
    CONSTRAINT `offer_product_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `offer_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='العلاقة بين العروض والمنتجات';

-- إنشاء جدول العلاقة بين العروض والفئات
CREATE TABLE `offer_category` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `offer_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف العرض',
    `category_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف الفئة',
    `discount_value` DECIMAL(10,2) NULL COMMENT 'قيمة الخصم المخصصة لهذه الفئة (إذا كانت مختلفة عن قيمة العرض الأساسية)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `offer_category_offer_id_category_id_unique` (`offer_id`, `category_id`),
    INDEX `offer_category_category_id_index` (`category_id`),
    CONSTRAINT `offer_category_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `offer_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='العلاقة بين العروض والفئات';

-- إنشاء جدول العلاقة بين العروض والعملاء
CREATE TABLE `offer_customer` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `offer_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف العرض',
    `customer_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف العميل',
    `usage_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'عدد مرات استخدام العميل للعرض',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `offer_customer_offer_id_customer_id_unique` (`offer_id`, `customer_id`),
    INDEX `offer_customer_customer_id_index` (`customer_id`),
    CONSTRAINT `offer_customer_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `offer_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='العلاقة بين العروض والعملاء';

-- إنشاء جدول سجل استخدام العروض
CREATE TABLE `offer_usage_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `offer_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف العرض',
    `order_id` BIGINT UNSIGNED NOT NULL COMMENT 'معرف الطلب',
    `customer_id` BIGINT UNSIGNED NULL COMMENT 'معرف العميل',
    `discount_amount` DECIMAL(10,2) NOT NULL COMMENT 'قيمة الخصم المطبق',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `offer_usage_log_offer_id_index` (`offer_id`),
    INDEX `offer_usage_log_order_id_index` (`order_id`),
    INDEX `offer_usage_log_customer_id_index` (`customer_id`),
    CONSTRAINT `offer_usage_log_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='سجل استخدام العروض';
