-- Database Name: ewu_lost_found
-- Updated: December 2024 - Added user authentication

CREATE DATABASE IF NOT EXISTS ewu_lost_found;
USE ewu_lost_found;

-- =============================================
-- USERS TABLE
-- =============================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255) DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- LOST ITEMS TABLE
-- =============================================
DROP TABLE IF EXISTS lost_items;
CREATE TABLE lost_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    last_location VARCHAR(255) NOT NULL,
    date_lost DATE NOT NULL,
    image VARCHAR(255),
    student_name VARCHAR(255) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    status ENUM('pending', 'claimed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- FOUND ITEMS TABLE
-- =============================================
DROP TABLE IF EXISTS found_items;
CREATE TABLE found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    found_location VARCHAR(255) NOT NULL,
    date_found DATE NOT NULL,
    image VARCHAR(255),
    finder_name VARCHAR(255) NOT NULL,
    finder_id VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    status ENUM('pending', 'claimed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================
CREATE INDEX idx_lost_category ON lost_items(category);
CREATE INDEX idx_lost_status ON lost_items(status);
CREATE INDEX idx_lost_date ON lost_items(date_lost);
CREATE INDEX idx_found_category ON found_items(category);
CREATE INDEX idx_found_status ON found_items(status);
CREATE INDEX idx_found_date ON found_items(date_found);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_student_id ON users(student_id);

-- =============================================
-- DEFAULT ADMIN USER (password: admin123)
-- Change this password immediately after setup!
-- =============================================
INSERT INTO users (student_id, email, password_hash, full_name, is_admin, is_verified) 
VALUES ('ADMIN-001', 'admin@ewubd.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', TRUE, TRUE);
