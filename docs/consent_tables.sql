-- SQL schema for informed consent module

CREATE TABLE consent_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NULL,
    category VARCHAR(255) NULL,
    body_html LONGTEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    allowed_placeholders JSON NULL,
    required_signers JSON NULL,
    requires_pet TINYINT(1) NOT NULL DEFAULT 1,
    requires_owner TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_consent_templates_tenant_active (tenant_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consent_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    code VARCHAR(255) NOT NULL UNIQUE,
    status VARCHAR(255) NOT NULL DEFAULT 'draft',
    template_id BIGINT UNSIGNED NOT NULL,
    owner_id BIGINT UNSIGNED NULL,
    pet_id BIGINT UNSIGNED NULL,
    owner_snapshot JSON NULL,
    pet_snapshot JSON NULL,
    merged_body_html LONGTEXT NOT NULL,
    merged_plain_text LONGTEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    signed_at TIMESTAMP NULL,
    canceled_reason TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_consent_documents_tenant_status (tenant_id, status),
    CONSTRAINT fk_consent_documents_template FOREIGN KEY (template_id) REFERENCES consent_templates(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consent_signatures (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    consent_document_id BIGINT UNSIGNED NOT NULL,
    signer_role VARCHAR(255) NOT NULL,
    signer_name VARCHAR(255) NOT NULL,
    signer_document VARCHAR(255) NULL,
    signature_image_path VARCHAR(255) NOT NULL,
    signed_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(255) NULL,
    user_agent TEXT NULL,
    method VARCHAR(255) NOT NULL,
    geo_hint VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_consent_signatures_tenant_doc (tenant_id, consent_document_id),
    CONSTRAINT fk_consent_signatures_doc FOREIGN KEY (consent_document_id) REFERENCES consent_documents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consent_public_links (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    consent_document_id BIGINT UNSIGNED NOT NULL,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NULL,
    max_uses INT UNSIGNED NOT NULL DEFAULT 1,
    uses INT UNSIGNED NOT NULL DEFAULT 0,
    last_used_at TIMESTAMP NULL,
    revoked_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_consent_public_links_tenant_exp (tenant_id, expires_at),
    CONSTRAINT fk_consent_public_links_doc FOREIGN KEY (consent_document_id) REFERENCES consent_documents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consent_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    consent_document_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    mime VARCHAR(255) NULL,
    size_bytes BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_consent_attachments_doc FOREIGN KEY (consent_document_id) REFERENCES consent_documents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
