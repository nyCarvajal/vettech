-- MySQL DDL for hospitalization module
-- Aligns with migration: database/migrations/2025_09_01_000000_create_hospitalization_tables.php

-- Optional cages table (created only if not present)
CREATE TABLE IF NOT EXISTS `cages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_stays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `owner_id` bigint unsigned NOT NULL,
  `cage_id` bigint unsigned DEFAULT NULL,
  `admitted_at` datetime NOT NULL,
  `discharged_at` datetime DEFAULT NULL,
  `status` enum('active','discharged') NOT NULL DEFAULT 'active',
  `severity` enum('stable','observation','critical') NOT NULL DEFAULT 'stable',
  `primary_dx` text,
  `plan` text,
  `diet` text,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_stays_patient_id_index` (`patient_id`),
  KEY `hospital_stays_status_index` (`status`),
  KEY `hospital_stays_admitted_at_index` (`admitted_at`),
  CONSTRAINT `hospital_stays_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  CONSTRAINT `hospital_stays_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`),
  CONSTRAINT `hospital_stays_cage_id_foreign` FOREIGN KEY (`cage_id`) REFERENCES `cages` (`id`),
  CONSTRAINT `hospital_stays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stay_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `day_number` int unsigned NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hospital_days_stay_id_date_unique` (`stay_id`,`date`),
  CONSTRAINT `hospital_days_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stay_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned DEFAULT NULL,
  `type` enum('medication','procedure','feeding','fluid','other') NOT NULL,
  `source` enum('inventory','manual') NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `manual_name` varchar(255) DEFAULT NULL,
  `dose` varchar(80) DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `schedule_json` json DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime DEFAULT NULL,
  `instructions` text,
  `status` enum('active','stopped') NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_orders_stay_id_index` (`stay_id`),
  KEY `hospital_orders_type_index` (`type`),
  KEY `hospital_orders_status_index` (`status`),
  CONSTRAINT `hospital_orders_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_orders_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `hospital_days` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hospital_orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `hospital_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_administrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `stay_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `administered_at` datetime DEFAULT NULL,
  `dose_given` varchar(80) DEFAULT NULL,
  `status` enum('done','skipped','late') NOT NULL DEFAULT 'done',
  `notes` text,
  `administered_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_administrations_stay_id_index` (`stay_id`),
  KEY `hospital_administrations_day_id_index` (`day_id`),
  KEY `hospital_administrations_administered_at_index` (`administered_at`),
  CONSTRAINT `hospital_administrations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `hospital_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_administrations_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_administrations_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `hospital_days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_administrations_administered_by_foreign` FOREIGN KEY (`administered_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_vitals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stay_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `measured_at` datetime NOT NULL,
  `temp` decimal(4,1) DEFAULT NULL,
  `hr` smallint unsigned DEFAULT NULL,
  `rr` smallint unsigned DEFAULT NULL,
  `spo2` decimal(5,2) DEFAULT NULL,
  `bp` varchar(30) DEFAULT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `pain_scale` tinyint unsigned DEFAULT NULL,
  `hydration` varchar(30) DEFAULT NULL,
  `mucous` varchar(30) DEFAULT NULL,
  `crt` varchar(30) DEFAULT NULL,
  `notes` text,
  `measured_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_vitals_stay_id_index` (`stay_id`),
  KEY `hospital_vitals_day_id_index` (`day_id`),
  KEY `hospital_vitals_measured_at_index` (`measured_at`),
  CONSTRAINT `hospital_vitals_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_vitals_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `hospital_days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_vitals_measured_by_foreign` FOREIGN KEY (`measured_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_progress_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stay_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `logged_at` datetime NOT NULL,
  `shift` enum('manana','tarde','noche') DEFAULT NULL,
  `content` text NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_progress_notes_stay_id_index` (`stay_id`),
  KEY `hospital_progress_notes_day_id_index` (`day_id`),
  KEY `hospital_progress_notes_logged_at_index` (`logged_at`),
  CONSTRAINT `hospital_progress_notes_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_progress_notes_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `hospital_days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_progress_notes_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hospital_charges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stay_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned DEFAULT NULL,
  `source` enum('service','inventory','manual') NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `qty` int NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_charges_stay_id_index` (`stay_id`),
  KEY `hospital_charges_day_id_index` (`day_id`),
  CONSTRAINT `hospital_charges_stay_id_foreign` FOREIGN KEY (`stay_id`) REFERENCES `hospital_stays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hospital_charges_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `hospital_days` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hospital_charges_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `hospital_charges_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
