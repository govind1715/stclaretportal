<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle add new position
$add_success = $add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_position'])) {
    $title = trim($_POST['title'] ?? '');
    $department_id = intval($_POST['department_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $status = $_POST['status'] === 'closed' ? 'closed' : 'open';
    if ($title && $department_id && $description && $requirements) {
        $stmt = $conn->prepare("INSERT INTO job_positions (title, department_id, description, requirements, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sisss', $title, $department_id, $description, $requirements, $status);
        if ($stmt->execute()) {
            $add_success = 'Job position added successfully!';
        } else {
            $add_error = 'Error adding job position.';
        }
        $stmt->close();
    } else {
        $add_error = 'Please fill in all fields.';
    }
}

// Fetch all job positions with department info
$sql = "SELECT jp.*, d.name as department_name, d.code as department_code FROM job_positions jp JOIN departments d ON jp.department_id = d.id ORDER BY jp.created_at DESC";
$result = $conn->query($sql);
// Fetch all departments for the dropdown
$departments = $conn->query("SELECT id, name, code FROM departments ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Positions - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container py-4">
    <h1 class="mb-4">Manage Job Positions</h1>
    <?php if ($add_success): ?>
        <div class="alert alert-success"><?php echo $add_success; ?></div>
    <?php elseif ($add_error): ?>
        <div class="alert alert-danger"><?php echo $add_error; ?></div>
    <?php endif; ?>
    <button class="btn btn-primary mb-3" data-bs-toggle="collapse" data-bs-target="#addPositionForm">Add New Position</button>
    <div class="collapse mb-4" id="addPositionForm">
        <div class="card card-body">
            <form method="post" action="">
                <input type="hidden" name="add_position" value="1">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php while($dept = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name'] . ' (' . $dept['code'] . ')'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="requirements" class="form-label">Requirements</label>
                    <textarea class="form-control" id="requirements" name="requirements" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Add Position</button>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['department_name'] . ' (' . $row['department_code'] . ')'); ?></td>
                    <td><span class="badge bg-<?php echo $row['status'] === 'open' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-info">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 