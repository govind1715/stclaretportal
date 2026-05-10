<?php
require_once '../includes/config.php';

// Drop existing tables if they exist
$conn->query("DROP TABLE IF EXISTS applications");
$conn->query("DROP TABLE IF EXISTS job_positions");
$conn->query("DROP TABLE IF EXISTS departments");
$conn->query("DROP TABLE IF EXISTS users");

// SQL to create tables
$sql = "
-- Create departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create job_positions table
CREATE TABLE IF NOT EXISTS job_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    department_id INT NOT NULL,
    description TEXT,
    requirements TEXT,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create applications table
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    education TEXT NOT NULL,
    experience TEXT NOT NULL,
    skills TEXT NOT NULL,
    additional_info TEXT,
    resume VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES job_positions(id)
);

-- Insert default departments
INSERT INTO departments (name, code) VALUES 
('Master of Computer Applications', 'MCA'),
('Master of Business Administration', 'MBA'),
('Master of Science', 'MSC'),
('Bachelor of Arts', 'BA'),
('Bachelor of Commerce', 'BCOM'),
('Bachelor of Business Administration', 'BBA'),
('Travel and Tourism Management', 'TTM'),
('Bachelor of Science', 'BSC');

-- Insert default admin user
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin@stclaret.edu', 'admin');
";

// Execute the SQL
if ($conn->multi_query($sql)) {
    echo "Database tables created successfully!<br>";
    echo "Default departments and admin user have been set up.<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error creating tables: " . $conn->error;
}
?> 