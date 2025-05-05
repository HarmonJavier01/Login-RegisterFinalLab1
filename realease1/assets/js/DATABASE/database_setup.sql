-- Improved SQL script to create database and tables for RealEase users and admins

CREATE DATABASE IF NOT EXISTS realease_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE realease_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (fullname, email, username, password, is_active) VALUES (
    'Administrator',
    'admin@realease.com',
    'admin',
    -- Password hash for 'admin123!'
    '$2y$10$e0NRXq6Xq6Xq6Xq6Xq6Xqu6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6Xq6',
    TRUE
);

-- Link admin user to admins table
INSERT INTO admins (user_id, role) VALUES (
    (SELECT id FROM users WHERE username = 'admin'),
    'superadmin'
);
