<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

include("includes/config.php");

$sent = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!str_contains($email, "@student.hau.edu.ph")) {
        $error = "Please use your HAU email.";
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO contacts (user_id, name, email, subject, message)
             VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issss",
            $_SESSION['user_id'], $name, $email, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            $sent = true;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us — FoundIt</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/contactus.css">
</head>
<body>

<header>
    <img src="image/logo.png" class="logo">
    <div class="hamburger" onclick="toggleMenu()">
        <div></div><div></div><div></div>
    </div>
</header>

<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="profile-header">
        <div class="profile-content">
            <a href="menu.php">
                <img src="image/user.png" class="profile-pic">
                <div class="profile-name"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
            </a>
        </div>
    </div>
    <a href="home.php"     class="menu-item"><img src="image/home.png">    Home</a>
    <a href="browse.php"   class="menu-item"><img src="image/lost.png">    Browse</a>
    <a href="list.php"     class="menu-item"><img src="image/list.png">    List</a>
    <a href="claim.php"    class="menu-item"><img src="image/found.png">   Claim</a>
    <a href="profile.php"  class="menu-item"><img src="image/profile.png"> Profile</a>
    <a href="contactus.php" class="menu-item"><img src="image/contact.png"> Contact Us</a>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="admin/dashboard.php" class="menu-item"><img src="image/admin.png"> Admin Panel</a>
    <?php endif; ?>
    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>
</div>

<!-- TOAST -->
<?php if($sent): ?>
<div class="toast" id="toast">
    <span class="toast-icon">&#10003;</span>
    Message sent to the admin!
</div>
<?php endif; ?>

<div class="page-wrapper">

    <!-- INFO PANEL -->
    <div class="info-panel">
        <img src="image/logo.png" class="info-logo" alt="FoundIt">
        <p class="info-desc">
            FoundIt is a campus-exclusive lost &amp; found system developed by BSIT students of
            Holy Angel University as an academic project.
        </p>
        <div class="info-row">
            <span class="info-icon">&#9993;</span>
            <span>foundit@hau.edu.ph</span>
        </div>
        <div class="info-row">
            <span class="info-icon">&#128205;</span>
            <span>Holy Angel University, Angeles City</span>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">

        <h1>Get in Touch</h1>
        <p class="form-subtitle">We&apos;ll get back to you through your HAU email.</p>

        <?php if($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="contactForm">

            <div class="field-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                       placeholder="e.g. Juan Dela Cruz"
                       value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>" required>
            </div>

            <div class="field-group">
                <label for="email">HAU Email</label>
                <input type="email" id="email" name="email"
                       placeholder="you@student.hau.edu.ph"
                       value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
            </div>

            <div class="field-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                    <option value="" disabled selected>Select a subject</option>
                    <option value="General Inquiry">General Inquiry</option>
                    <option value="Report a Problem">Report a Problem</option>
                    <option value="Claim Dispute">Claim Dispute</option>
                    <option value="Account Issue">Account Issue</option>
                    <option value="Feedback / Suggestion">Feedback / Suggestion</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="field-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5"
                          placeholder="Write your message here..." required></textarea>
            </div>

            <button type="submit" class="send-btn">Send Message</button>

        </form>
    </div>

</div>

<script src="js/global.js"></script>
<script src="js/contactus.js"></script>

</body>
</html>