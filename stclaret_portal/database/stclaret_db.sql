-- Create database
CREATE DATABASE IF NOT EXISTS stclaret_portal;
USE stclaret_portal;

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert departments
INSERT IGNORE INTO departments (name, code) VALUES
('Master of Computer Applications', 'MCA'),
('Bachelor of Computer Applications', 'BCA'),
('Bachelor of Business Administration', 'BBA'),
('Bachelor of Arts', 'BA'),
('Travel and Tourism Management', 'TTM'),
('Bachelor of Science', 'BSC'),
('Master of Science', 'MSC'),
('Bachelor of Commerce', 'BCOM'),
('Master of Business Administration', 'MBA');

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Job Positions table
CREATE TABLE IF NOT EXISTS job_positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Applications table
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    qualification TEXT NOT NULL,
    experience TEXT NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    status ENUM('pending', 'reviewed', 'shortlisted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_positions(id)
);

-- Insert default admin user if not exists
INSERT IGNORE INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$8K1p/a0dR1xqM8K1p/a0dR1xqM8K1p/a0dR1xqM8K1p/a0dR1xqM', 'admin@stclaret.edu', 'admin'); 