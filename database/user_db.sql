-- =====================================================
-- DATABASE CREATION (MySQL Integration)
-- Database: user_db
-- =====================================================


CREATE DATABASE IF NOT EXISTS user_db;
USE user_db;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    login_attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better query performance
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at)
);

-- default admin user (password: Admin@123)
INSERT INTO users (first_name, last_name, email, password, gender, role, status) VALUES 
(
    'Admin',
    'User',
    'admin@example.com',
    '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    'Male',
    'admin',
    'active'
);

-- Note: Yung hash po sa password sa taas ay placeholder lamang

-- Insert a default user (password: User@1234)
INSERT INTO users (first_name, last_name, email, password, gender, role, status) VALUES 
(
    'Juan',
    'Dela Cruz',
    'juan@example.com',
    '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    'Male',
    'user',
    'active'
);

-- Display table structure
DESCRIBE users;
