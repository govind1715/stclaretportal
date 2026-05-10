<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_phone'])) {
    header('Location: user_login.php');
    exit();
}

$email = $_SESSION['user_email'];
$phone = $_SESSION['user_phone'];

// Fetch all applications for this user
$sql = "SELECT a.*, jp.title as position_title, d.name as department_name, d.code as department_code
        FROM applications a
        JOIN job_positions jp ON a.position_id = jp.id
        JOIN departments d ON jp.department_id = d.id
        WHERE a.email = ? AND a.phone = ?
        ORDER BY a.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $email, $phone);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Applications - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Applications</h2>
        <a href="user_logout.php" class="btn btn-secondary">Logout</a>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Resume</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['position_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['department_name'] . ' (' . $row['department_code'] . ')'); ?></td>
                        <td><span class="badge bg-<?php echo $row['status'] === 'pending' ? 'warning' : ($row['status'] === 'approved' ? 'success' : 'danger'); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td><a href="uploads/<?php echo $row['resume']; ?>" target="_blank" class="btn btn-sm btn-primary">View Resume</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No applications found for your account.</div>
    <?php endif; ?>
</div>
</body>
</html> 