<?php
require_once 'includes/header.php';

// Fetch featured job positions with department information
$sql = "SELECT jp.*, d.name as department_name, d.code as department_code 
        FROM job_positions jp 
        JOIN departments d ON jp.department_id = d.id 
        WHERE jp.status = 'open' 
        ORDER BY jp.created_at DESC 
        LIMIT 3";
$result = $conn->query($sql);
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 mb-4">Join Our Academic Excellence</h1>
        <p class="lead mb-4">Be part of St. Claret College's mission to shape future leaders through quality education</p>
        <a href="careers.php" class="btn btn-light btn-lg">View Open Positions</a>
    </div>
</section>

<!-- Featured Positions -->
<section class="featured-positions py-5">
    <div class="container">
        <h2 class="text-center mb-5">Featured Positions</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card job-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?php echo htmlspecialchars($row['department_name'] . ' (' . $row['department_code'] . ')'); ?>
                                </h6>
                                <p class="card-text"><?php echo substr(htmlspecialchars($row['description']), 0, 150) . '...'; ?></p>
                                <a href="application-form.php?position=<?php echo $row['id']; ?>" class="btn btn-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No open positions at the moment.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Why Join Us -->
<section class="why-join-us py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Why Join St. Claret College?</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-graduation-cap fa-3x mb-3 text-primary"></i>
                <h4>Academic Excellence</h4>
                <p>Be part of an institution committed to delivering quality education and research.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                <h4>Professional Growth</h4>
                <p>Access to continuous learning and development opportunities.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-handshake fa-3x mb-3 text-primary"></i>
                <h4>Work-Life Balance</h4>
                <p>Enjoy a supportive work environment with competitive benefits.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>Email: scc@claretcollege.edu.in<br>
                Phone: +91 6361718834</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="text-white">About Us</a></li>
                    <li><a href="careers.php" class="text-white">Careers</a></li>
                    <li><a href="contact.php" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Follow Us</h5>
                <div class="social-links">
                    <a href="https://www.facebook.com/stclarets/" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/St_Claret" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.claretcollege.edu.in/about-scc" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <hr class="mt-4">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> St. Claret College. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 