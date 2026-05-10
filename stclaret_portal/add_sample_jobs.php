<?php
require_once 'includes/config.php';

// Sample job positions for each department
$sample_jobs = [
    [
        'title' => 'Assistant Professor - Computer Science',
        'department_id' => 1, // MCA
        'description' => 'We are seeking an Assistant Professor in Computer Science to join our MCA department. The ideal candidate should have expertise in programming, algorithms, and software development.',
        'requirements' => '• Ph.D. in Computer Science or related field\n• Minimum 3 years of teaching experience\n• Strong programming skills\n• Research publications preferred'
    ],
    [
        'title' => 'Lecturer - Business Administration',
        'department_id' => 3, // BBA
        'description' => 'Join our BBA department as a Lecturer. The position involves teaching business administration courses and mentoring students.',
        'requirements' => '• Master\'s degree in Business Administration\n• Minimum 2 years of teaching experience\n• Industry experience preferred\n• Strong communication skills'
    ],
    [
        'title' => 'Assistant Professor - Commerce',
        'department_id' => 8, // BCOM
        'description' => 'Looking for an Assistant Professor in Commerce to teach undergraduate courses and guide students in their academic journey.',
        'requirements' => '• Ph.D. in Commerce or related field\n• Minimum 3 years of teaching experience\n• Strong knowledge of accounting and finance\n• Research experience preferred'
    ],
    [
        'title' => 'Lecturer - Science',
        'department_id' => 6, // BSC
        'description' => 'Join our Science department as a Lecturer. The position involves teaching various science subjects and conducting laboratory sessions.',
        'requirements' => '• Master\'s degree in Science\n• Minimum 2 years of teaching experience\n• Laboratory experience required\n• Strong subject knowledge'
    ],
    [
        'title' => 'Assistant Professor - Arts',
        'department_id' => 4, // BA
        'description' => 'We are seeking an Assistant Professor in Arts to join our BA department. The ideal candidate should have expertise in humanities and social sciences.',
        'requirements' => '• Ph.D. in Arts or related field\n• Minimum 3 years of teaching experience\n• Strong research background\n• Excellent communication skills'
    ]
];

try {
    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO job_positions (title, department_id, description, requirements, status) VALUES (?, ?, ?, ?, 'open')");
    
    // Insert each sample job
    foreach ($sample_jobs as $job) {
        $stmt->bind_param('siss', 
            $job['title'],
            $job['department_id'],
            $job['description'],
            $job['requirements']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting job: " . $stmt->error);
        }
    }
    
    echo "Sample jobs added successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 