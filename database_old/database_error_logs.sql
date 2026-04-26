-- Error Logs table for system error tracking
CREATE TABLE error_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    error_type ENUM('file_upload', 'database', 'email', 'encryption', 'validation', 'system') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    error_message TEXT NOT NULL,
    stack_trace TEXT NULL,
    request_data JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_error_type (error_type),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
);