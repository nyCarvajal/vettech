-- SQL to create the learning_stroke_progress table
CREATE TABLE learning_stroke_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    session_date DATE NOT NULL,
    session_number INT UNSIGNED DEFAULT NULL,
    technique VARCHAR(150) DEFAULT NULL,
    progress_percent TINYINT UNSIGNED DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_learning_stroke_progress_patient_id (patient_id),
    INDEX idx_learning_stroke_progress_session_date (session_date)
);

-- Rollback
DROP TABLE IF EXISTS learning_stroke_progress;
