<?php
require_once '../includes/config.php';

// Check if admin user already exists
$result = $conn->query("SELECT id FROM users WHERE username = 'admin'");
if ($result->num_rows > 0) {
    echo "Admin user already exists. Please use the reset password page to change the password.";
    exit();
}

// Create admin user with hashed password
$username = 'admin';
$password = 'admin123'; // Default password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$email = 'admin@stclaret.edu';
$role = 'admin';

$stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $username, $hashed_password, $email, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error creating admin user: " . $conn->error;
}
?> 