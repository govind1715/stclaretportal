<?php
require_once '../includes/config.php';




// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get application ID
$application_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$application_id) {
    header('Location: applications.php');
    exit();
}

// Get application details
$sql = "SELECT a.*, jp.title as position_title, jp.description as position_description,
               d.name as department_name, d.code as department_code
        FROM applications a 
        JOIN job_positions jp ON a.position_id = jp.id 
        JOIN departments d ON jp.department_id = d.id 
        WHERE a.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $application_id);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();

if (!$application) {
    header('Location: applications.php');
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    
    $stmt = $conn->prepare("UPDATE applications SET status = ?, notes = ? WHERE id = ?");
    $stmt->bind_param('ssi', $status, $notes, $application_id);
    $stmt->execute();

    header("Location: view_application.php?id=$application_id&success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="applications.php">Applications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="positions.php">Job Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="departments.php">Departments</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Application Details</h1>
            <a href="applications.php" class="btn btn-secondary">Back to Applications</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Application status updated successfully.</div>
        <?php endif; ?>

        <div class="row">
            <!-- Application Details -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Applicant Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($application['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Applied On:</strong> <?php echo date('F d, Y', strtotime($application['created_at'])); ?></p>
                                <p><strong>Current Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $application['status'] === 'pending' ? 'warning' : 
                                            ($application['status'] === 'approved' ? 'success' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Address</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['address'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6>Education</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['education'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6>Experience</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['experience'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6>Skills</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['skills'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6>Additional Information</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['additional_info'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6>Resume</h6>
                            <a href="../uploads/<?php echo $application['resume']; ?>" 
                               class="btn btn-primary" target="_blank">
                                <i class="fas fa-download"></i> Download Resume
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Position Details and Status Update -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Position Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($application['position_title']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($application['department_name']); ?> 
                           (<?php echo htmlspecialchars($application['department_code']); ?>)</p>
                        <div class="mb-3">
                            <h6>Job Description</h6>
                            <p><?php echo nl2br(htmlspecialchars($application['position_description'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $application['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4"><?php echo htmlspecialchars($application['notes'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 