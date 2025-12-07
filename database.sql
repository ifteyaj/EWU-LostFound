-- Database Name: ewu_lost_found

CREATE DATABASE IF NOT EXISTS ewu_lost_found;
USE ewu_lost_found;

DROP TABLE IF EXISTS lost_items;
CREATE TABLE lost_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    last_location VARCHAR(255) NOT NULL,
    date_lost DATE NOT NULL,
    image VARCHAR(255),
    student_name VARCHAR(255) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS found_items;
CREATE TABLE found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    found_location VARCHAR(255) NOT NULL,
    date_found DATE NOT NULL,
    image VARCHAR(255),
    finder_name VARCHAR(255) NOT NULL,
    finder_id VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
