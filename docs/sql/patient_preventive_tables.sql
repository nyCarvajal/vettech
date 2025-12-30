-- SQL definitions for preventive care tables (tenant-scoped schema)
-- These statements mirror the Laravel tenant migrations

-- Vaccination records
CREATE TABLE `patient_immunizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint unsigned NOT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `applied_at` date NOT NULL DEFAULT (CURRENT_DATE),
  `vaccine_name` varchar(255) NOT NULL,
  `contains_rabies` tinyint(1) NOT NULL DEFAULT 0,
  `item_id` bigint unsigned DEFAULT NULL,
  `item_manual` varchar(255) DEFAULT NULL,
  `batch_lot` varchar(255) NOT NULL,
  `dose` varchar(255) DEFAULT NULL,
  `next_due_at` date DEFAULT NULL,
  `vet_user_id` bigint unsigned DEFAULT NULL,
  `notes` text,
  `status` enum('applied','scheduled','overdue') NOT NULL DEFAULT 'applied',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_immunizations_paciente_id_applied_at_index` (`paciente_id`,`applied_at`),
  KEY `patient_immunizations_next_due_at_index` (`next_due_at`),
  CONSTRAINT `patient_immunizations_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `patient_immunizations_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `historia_clinicas` (`id`),
  CONSTRAINT `patient_immunizations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `patient_immunizations_vet_user_id_foreign` FOREIGN KEY (`vet_user_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Deworming records
CREATE TABLE `patient_dewormings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint unsigned NOT NULL,
  `consulta_id` bigint unsigned DEFAULT NULL,
  `type` enum('internal','external') NOT NULL,
  `applied_at` date NOT NULL DEFAULT (CURRENT_DATE),
  `item_id` bigint unsigned DEFAULT NULL,
  `item_manual` varchar(255) DEFAULT NULL,
  `dose` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `duration_days` int DEFAULT NULL,
  `next_due_at` date DEFAULT NULL,
  `vet_user_id` bigint unsigned DEFAULT NULL,
  `notes` text,
  `status` enum('applied','scheduled','overdue') NOT NULL DEFAULT 'applied',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_dewormings_paciente_id_applied_at_index` (`paciente_id`,`applied_at`),
  KEY `patient_dewormings_next_due_at_index` (`next_due_at`),
  CONSTRAINT `patient_dewormings_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `patient_dewormings_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `historia_clinicas` (`id`),
  CONSTRAINT `patient_dewormings_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `patient_dewormings_vet_user_id_foreign` FOREIGN KEY (`vet_user_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
