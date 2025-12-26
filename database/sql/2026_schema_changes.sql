-- SQL changes corresponding to Laravel migrations added on 2026-01-01

-- Add historia_clinica_id relationship to prescriptions
ALTER TABLE `prescriptions`
    ADD COLUMN `historia_clinica_id` BIGINT UNSIGNED NULL AFTER `encounter_id`;
ALTER TABLE `prescriptions`
    ADD CONSTRAINT `prescriptions_historia_clinica_id_foreign`
        FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas`(`id`);

-- Create exam_referrals table for remisiones de examenes
CREATE TABLE `exam_referrals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `historia_clinica_id` BIGINT UNSIGNED NULL,
    `patient_id` BIGINT UNSIGNED NOT NULL,
    `doctor_name` VARCHAR(255) NULL,
    `tests` TEXT NULL,
    `notes` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `exam_referrals_historia_clinica_id_foreign`
        FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas`(`id`),
    CONSTRAINT `exam_referrals_patient_id_foreign`
        FOREIGN KEY (`patient_id`) REFERENCES `pacientes`(`id`),
    CONSTRAINT `exam_referrals_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Allow manual, non-billable prescription items
ALTER TABLE `prescription_items` DROP FOREIGN KEY `prescription_items_product_id_foreign`;
ALTER TABLE `prescription_items`
    MODIFY `product_id` BIGINT UNSIGNED NULL,
    ADD `manual_name` VARCHAR(255) NULL AFTER `product_id`,
    ADD `is_manual` TINYINT(1) NOT NULL DEFAULT 0 AFTER `manual_name`,
    ADD `billable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_manual`;
ALTER TABLE `prescription_items`
    ADD CONSTRAINT `prescription_items_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL;
