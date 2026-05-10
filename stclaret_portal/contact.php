<?php
require_once 'includes/header.php';

// Initialize variables
$name = $email = $message = '';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Send email
        $to = 'careers@stclaret.edu';
        $subject = 'Contact Form Submission from ' . $name;
        $body = "Name: $name\nEmail: $email\nMessage:\n$message";
        $headers = "From: $email\r\nReply-To: $email";
        if (mail($to, $subject, $body, $headers)) {
            $success = 'Thank you for contacting us! We will get back to you soon.';
            $name = $email = $message = '';
        } else {
            $error = 'Sorry, there was a problem sending your message. Please try again later.';
        }
    }
}
?>
<div class="container py-5">
    <h1 class="mb-4 text-center">Contact Us</h1>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h5>St. Claret College</h5>
                <p>Email: careers@stclaret.edu<br>
                Phone: +91-XXX-XXXXXXX<br>
                Address: St. Claret College, [Your Address Here]</p>
                <hr>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <h6>Send us a message:</h6>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your Message" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?> 