<?php
require_once 'includes/header.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$department_id = isset($_GET['department']) ? (int)$_GET['department'] : 0;

// Build the query
$sql = "SELECT jp.*, d.name as department_name, d.code as department_code 
        FROM job_positions jp 
        JOIN departments d ON jp.department_id = d.id 
        WHERE jp.status = 'open'";
$params = array();

if (!empty($search)) {
    $sql .= " AND (jp.title LIKE ? OR jp.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($department_id > 0) {
    $sql .= " AND jp.department_id = ?";
    $params[] = $department_id;
}

$sql .= " ORDER BY jp.created_at DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get departments for filter
$departments = $conn->query("SELECT * FROM departments ORDER BY name");
?>

<!-- Page Header -->
<div class="bg-light py-5">
    <div class="container">
        <h1 class="text-center mb-4">Career Opportunities</h1>
        <p class="text-center lead">Join our team of dedicated educators and make a difference in students' lives</p>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" placeholder="Search positions..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="department">
                        <option value="">All Departments</option>
                        <?php while($dept = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo $department_id === (int)$dept['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name'] . ' (' . $dept['code'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Job Listings -->
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card job-card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?php echo htmlspecialchars($row['department_name'] . ' (' . $row['department_code'] . ')'); ?>
                            </h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                            <h6 class="mt-3">Requirements:</h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['requirements'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">Posted: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                <a href="application-form.php?position=<?php echo $row['id']; ?>" class="btn btn-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No open positions found matching your criteria.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 