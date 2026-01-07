-- Followups module tables (Laravel migration equivalent)
-- Compatible with MySQL 8 / MariaDB 10.6+

CREATE TABLE `followups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` BIGINT UNSIGNED NULL,
    `code` VARCHAR(255) NOT NULL,
    `patient_id` BIGINT UNSIGNED NULL,
    `owner_id` BIGINT UNSIGNED NULL,
    `consultation_id` BIGINT UNSIGNED NULL,
    `patient_snapshot` JSON NULL,
    `owner_snapshot` JSON NULL,
    `followup_at` DATETIME NOT NULL,
    `performed_by` VARCHAR(255) NULL,
    `performed_by_license` VARCHAR(255) NULL,
    `reason` VARCHAR(255) NULL,
    `improved_status` ENUM('yes','no','partial','unknown') NOT NULL DEFAULT 'unknown',
    `improved_score` TINYINT NULL,
    `observations` LONGTEXT NULL,
    `plan` LONGTEXT NULL,
    `next_followup_at` DATETIME NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `followups_code_unique` (`code`),
    CONSTRAINT `followups_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
    CONSTRAINT `followups_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL,
    CONSTRAINT `followups_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `encounters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `followup_vitals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `followup_id` BIGINT UNSIGNED NOT NULL,
    `temperature_c` DECIMAL(4,1) NULL,
    `heart_rate_bpm` INT NULL,
    `respiratory_rate_rpm` INT NULL,
    `weight_kg` DECIMAL(6,2) NULL,
    `hydration` ENUM('normal','mild_dehydration','moderate','severe','unknown') NOT NULL DEFAULT 'unknown',
    `mucous_membranes` ENUM('pink','pale','icteric','cyanotic','hyperemic','unknown') NOT NULL DEFAULT 'unknown',
    `capillary_refill_time_sec` DECIMAL(3,1) NULL,
    `pain_score_0_10` TINYINT NULL,
    `blood_pressure_sys` INT NULL,
    `blood_pressure_dia` INT NULL,
    `blood_pressure_map` INT NULL,
    `o2_saturation_percent` INT NULL,
    `notes` LONGTEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `followup_vitals_followup_id_foreign` FOREIGN KEY (`followup_id`) REFERENCES `followups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `followup_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `followup_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `mime` VARCHAR(255) NOT NULL,
    `size_bytes` BIGINT UNSIGNED NOT NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `followup_attachments_followup_id_foreign` FOREIGN KEY (`followup_id`) REFERENCES `followups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
