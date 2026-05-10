<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Create connection without database
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Drop existing database if exists
    $conn->query("DROP DATABASE IF EXISTS stclaret_portal");
    
    // Read SQL file
    $sql = file_get_contents('database/stclaret_db.sql');
    
    // Execute multi query
    if ($conn->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
    
    if ($conn->error) {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
    
    echo "Database and tables created successfully!<br>";
    echo "You can now <a href='add_sample_jobs.php'>add sample jobs</a> or <a href='index.php'>go to homepage</a>.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 