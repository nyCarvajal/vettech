-- Schema for the veterinary procedures module (MySQL-compatible)

CREATE TABLE `procedures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `patient_id` bigint unsigned DEFAULT NULL,
  `owner_id` bigint unsigned DEFAULT NULL,
  `patient_snapshot` json NOT NULL,
  `owner_snapshot` json DEFAULT NULL,
  `type` enum('surgery','procedure') NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','canceled') NOT NULL DEFAULT 'scheduled',
  `scheduled_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `responsible_vet_name` varchar(255) DEFAULT NULL,
  `responsible_vet_license` varchar(255) DEFAULT NULL,
  `assistants` json DEFAULT NULL,
  `preop_notes` longtext,
  `intraop_notes` longtext,
  `postop_notes` longtext,
  `observations` longtext,
  `anesthesia_plan` longtext,
  `anesthesia_notes` longtext,
  `anesthesia_monitoring` longtext,
  `pain_management` longtext,
  `complications` longtext,
  `diagnosis_pre` varchar(255) DEFAULT NULL,
  `diagnosis_post` varchar(255) DEFAULT NULL,
  `lab_results_summary` longtext,
  `consent_document_id` bigint unsigned DEFAULT NULL,
  `cost_total` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'COP',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `procedures_code_unique` (`code`),
  KEY `procedures_tenant_patient_owner_index` (`tenant_id`,`patient_id`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procedure_anesthesia_medications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `procedure_id` bigint unsigned NOT NULL,
  `drug_name` varchar(255) NOT NULL,
  `dose` varchar(255) DEFAULT NULL,
  `dose_unit` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `procedure_anesthesia_medications_procedure_id_foreign` (`procedure_id`),
  CONSTRAINT `procedure_anesthesia_medications_procedure_id_foreign` FOREIGN KEY (`procedure_id`) REFERENCES `procedures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procedure_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `procedure_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `size_bytes` bigint unsigned NOT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `procedure_attachments_procedure_id_foreign` (`procedure_id`),
  CONSTRAINT `procedure_attachments_procedure_id_foreign` FOREIGN KEY (`procedure_id`) REFERENCES `procedures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procedure_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `procedure_id` bigint unsigned NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `payload` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `procedure_events_procedure_id_foreign` (`procedure_id`),
  CONSTRAINT `procedure_events_procedure_id_foreign` FOREIGN KEY (`procedure_id`) REFERENCES `procedures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
