<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Claim Submitted</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/approved.css">
</head>

<body>

<header>
    <img src="image/logo.png" class="logo">
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
</header>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">

    <div class="profile-header">
        <div class="profile-content">
            <a href="index.php">
                <img src="image/user.png" class="profile-pic">
                <div class="profile-name">Francine Panganiban</div>
                <div class="profile-email">fastodomingo@student.hau.edu.ph</div>
            </a>
        </div>
    </div>

    <a href="home.php" class="menu-item"><img src="image/home.png"> Home</a>
    <a href="browse.php" class="menu-item"><img src="image/lost.png"> Browse</a>
    <a href="list.php" class="menu-item"><img src="image/list.png"> List</a>
    <a href="claim.php" class="menu-item"><img src="image/found.png"> Claim</a>
    <a href="profile.php" class="menu-item"><img src="image/profile.png"> Profile</a>
    <a href="contactus.php" class="menu-item"><img src="image/contact.png"> Contact Us</a>
    <a href="landingpage.php" class="menu-item"><img src="image/out.png"> Log Out</a>

</div>

<div class="container">

<h1>Claim</h1>

<img src="image/approved.png" class="check-icon" alt="Success">

<div class="big-message">
Your claim has been sent to the finder for review.
</div>

<div class="info-text">
You will receive a notification once the finder makes a decision.<br><br>
If approved, the finder will contact you through your
<a href="https://outlook.office365.com/mail/?realm=hau.gr&vd=autodiscover">HAU email</a>.
</div>

<a href="browse.php" class="return-btn">Return to Browse</a>

</div>

<script src="js/global.js"></script>

</body>
</html>
