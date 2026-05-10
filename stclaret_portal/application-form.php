<?php
require_once 'includes/header.php';

// Check if position ID is provided
if (!isset($_GET['position'])) {
    header('Location: careers.php');
    exit();
}

$position_id = (int)$_GET['position'];

// Get position details with department information
$stmt = $conn->prepare("
    SELECT jp.*, d.name as department_name, d.code as department_code 
    FROM job_positions jp 
    JOIN departments d ON jp.department_id = d.id 
    WHERE jp.id = ? AND jp.status = 'open'
");
$stmt->bind_param('i', $position_id);
$stmt->execute();
$position = $stmt->get_result()->fetch_assoc();

if (!$position) {
    header('Location: careers.php');
    exit();
}

// Handle form submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'qualification', 'experience', 'skills'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Validate phone
    if (!preg_match('/^[0-9+\-\s()]{10,}$/', $_POST['phone'])) {
        $errors[] = 'Invalid phone number format';
    }
    
    // Handle file upload
    if (!isset($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Resume is required';
    } else {
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['resume']['type'], $allowed_types)) {
            $errors[] = 'Only PDF and Word documents are allowed';
        }
        
        if ($_FILES['resume']['size'] > $max_size) {
            $errors[] = 'File size should not exceed 5MB';
        }
    }
    
    if (empty($errors)) {
        // Create uploads directory if it doesn't exist
        if (!file_exists(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $filepath = UPLOAD_PATH . $filename;
        
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $filepath)) {
            // Combine first and last name
            $name = trim($_POST['first_name'] . ' ' . $_POST['last_name']);
            $education = $_POST['qualification'];
            $skills = $_POST['skills'];
            $additional_info = $_POST['additional_info'] ?? '';
            // Insert application into database
            $stmt = $conn->prepare("INSERT INTO applications (position_id, name, email, phone, address, education, experience, skills, additional_info, resume, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param('isssssssss', 
                $position_id,
                $name,
                $_POST['email'],
                $_POST['phone'],
                $_POST['address'],
                $education,
                $_POST['experience'],
                $skills,
                $additional_info,
                $filename
            );
            
            if ($stmt->execute()) {
                $success = 'Your application has been submitted successfully!';
            } else {
                $error = 'Error submitting application. Please try again.';
            }
        } else {
            $error = 'Error uploading resume. Please try again.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Application Form</h2>
                    <h5 class="mb-3">Position: <?php echo htmlspecialchars($position['title']); ?></h5>
                    <h6 class="text-muted mb-4">
                        Department: <?php echo htmlspecialchars($position['department_name'] . ' (' . $position['department_code'] . ')'); ?>
                    </h6>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="qualification" class="form-label">Qualifications *</label>
                            <textarea class="form-control" id="qualification" name="qualification" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="experience" class="form-label">Experience *</label>
                            <textarea class="form-control" id="experience" name="experience" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills *</label>
                            <textarea class="form-control" id="skills" name="skills" rows="2" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_info" class="form-label">Additional Information</label>
                            <textarea class="form-control" id="additional_info" name="additional_info" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="resume" class="form-label">Resume (PDF/DOC/DOCX) *</label>
                            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted">Maximum file size: 5MB</small>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require_once 'includes/footer.php'; ?> 