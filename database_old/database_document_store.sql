-- Document Store table for encrypted file storage
CREATE TABLE document_store (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    document_type ENUM('enrollment', 'assessment', 'iep', 'learning_material', 'submission', 'meeting') NOT NULL,
    classification ENUM('public', 'internal', 'confidential', 'restricted') NOT NULL DEFAULT 'internal',
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    encryption_key_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_document_type (document_type),
    INDEX idx_classification (classification),
    INDEX idx_created_at (created_at)
);