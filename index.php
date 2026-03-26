<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Index Page</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/index.css">
</head>

<body>

<div class="overlay"></div>

<header>
    <img src="image/menulogo.png" class="logo">
</header>

<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">

    <div class="profile-header">
        <div class="profile-content">
            <div>
                <img src="image/user.png" class="profile-pic">
                <div class="profile-name">
                    <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : ''; ?>
                </div>
                <div class="profile-email">
                    <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>
                </div>
            </div>
        </div>
    </div>

    <a href="home.php" class="menu-item"><img src="image/home.png"> Home</a>
    <a href="browse.php" class="menu-item"><img src="image/lost.png"> Browse</a>
    <a href="list.php" class="menu-item"><img src="image/list.png"> List</a>
    <a href="claim.php" class="menu-item"><img src="image/found.png"> Claim</a>
    <a href="profile.php" class="menu-item"><img src="image/profile.png"> Profile</a>
    <a href="contactus.php" class="menu-item"><img src="image/contact.png"> Contact Us</a>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin/dashboard.php" class="menu-item"><img src="image/admin.png"> Admin Panel</a>
    <?php endif; ?>

    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>

</div>

<div class="content">
    <div>
        <img src="image/menufoundit.png" class="hero-image" alt="Lost Something Banner">
        <p>Post lost items, browse found belongings, and get back what's yours—fast and easy!</p>

        <p class="cta-text">Log in or Sign up to get started</p>

        <div class="cta-buttons">
            <a href="landingpage.php" class="btn login-btn">LOG IN</a>
            <a href="signup.php" class="btn signup-btn">SIGN UP</a>
        </div>
    </div>
</div>

<script src="js/landingpage.js"></script>

</body>
</html>
