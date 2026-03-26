<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home Page</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/home.css">
</head>

<body>

<header>
    <div class="header-right">
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="notif-dropdown" id="notifDropdown">
    <?php
    $claims_query = mysqli_query($conn,"
    SELECT users.fullname, items.item_name
    FROM claims
    JOIN items ON claims.item_id = items.id
    JOIN users ON claims.user_id = users.ID
    WHERE items.posted_by='".$_SESSION['user_id']."'
    AND claims.status='pending'
    ORDER BY claims.created_at DESC
    LIMIT 5
    ");

    if(mysqli_num_rows($claims_query) > 0){
        while($row = mysqli_fetch_assoc($claims_query)){
            echo "<div class='notif-item'>
            <strong>".$row['fullname']."</strong> claimed your <strong>".$row['item_name']."</strong>
            </div>";
        }
    } else {
        echo "<div class='notif-empty'>No new notifications</div>";
    }
    ?>
    </div>
</header>

<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">

    <div class="profile-header">
        <div class="profile-content">
            <a href="index.php">
                <img src="image/user.png" class="profile-pic">
                <div class="profile-name"><?php echo $_SESSION['fullname']; ?></div>
                <div class="profile-email"><?php echo $_SESSION['email']; ?></div>
            </a>
        </div>
    </div>

    <a href="home.php" class="menu-item"><img src="image/home.png"> Home</a>
    <a href="browse.php" class="menu-item"><img src="image/lost.png"> Browse</a>
    <a href="list.php" class="menu-item"><img src="image/list.png"> List</a>
    <a href="claim.php" class="menu-item"><img src="image/found.png"> Claim</a>
    <a href="profile.php" class="menu-item"><img src="image/profile.png"> Profile</a>
    <a href="contactus.php" class="menu-item"><img src="image/contact.png"> Contact Us</a>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="admin/dashboard.php" class="menu-item"><img src="image/admin.png"> Admin Panel</a>
    <?php endif; ?>

    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>

</div>

<div class="top-logo">
    <img src="image/menulogo.png">
</div>

<div class="bottom-section">

    <div class="info-image">
        <img src="image/info.png">
    </div>

    <div class="bottom-text">
        FoundIt is a campus-exclusive lost and found web application developed for Holy Angel University.
        It was created to provide a centralized and secure platform where students and staff can report lost items,
        post found belongings, and reconnect with their rightful owners efficiently.
        By requiring HAU email authentication, the system ensures that only legitimate members of the university
        community can access and use the platform.
    </div>

    <div class="why-section">
        <div class="why-title">
            <img src="image/why.png" alt="Why FoundIt Was Created">
        </div>
        <div class="why-container">
            <div class="why-text">
                Losing personal belongings on campus is common, and traditional lost-and-found processes are often unorganized, slow, and difficult to track. FoundIt addresses these challenges by offering a structured digital system that improves accessibility, security, and communication. The platform features a hidden verification mechanism that helps validate ownership before claims are approved, reducing false claims and enhancing trust among users. In addition, a built-in notification system keeps users informed about claim requests and updates in real time.
            </div>
        </div>
    </div>

</div>

<div class="how-wrapper">

    <div class="how-title-box">HOW IT WORKS</div>

    <div class="how-item">
        <h4>List</h4>
        <p>
            If you find a lost item within Holy Angel University, you can post it in the List tab by providing the item's public details and setting hidden verification questions. Once submitted, the system automatically generates a unique Item ID (e.g., FI-001), and the item becomes visible in the Browse tab.
        </p>
    </div>

    <div class="how-item">
        <h4>Browse</h4>
        <p>
            Students who have lost an item can explore the Browse tab to view all available found items. Each listing includes a unique ID and a claim button. Users may either click the claim button directly or manually enter the item ID in the Claim tab.
        </p>
    </div>

    <div class="how-item">
        <h4>Claim & Verify</h4>
        <p>
            To claim an item, the user must answer the hidden verification questions created by the finder. All claims are sent to the finder for review. The finder can accept, reject, or notify the claimant that they will reach out through HAU email. Multiple claims are allowed, but only one can be approved at a time. If necessary, the finder may reopen a claim before the item is archived.
        </p>
    </div>

</div>

<div class="container">

    <div class="section">
        <div class="mv">
            <div>
                <h2>Mission</h2>
                <p>
                    To provide Holy Angel University with a secure and structured digital lost-and-found platform
                    that promotes accountability, honesty, and efficient item recovery within the campus community.
                </p>
            </div>
            <div class="divider"></div>
            <div>
                <h2>Vision</h2>
                <p>
                    To build a trusted campus environment where lost belongings are returned quickly and safely
                    through a verification-based and community-driven system.
                </p>
            </div>
        </div>
    </div>

    <div class="team-section">
        <h2>Our Team</h2>
        <p>
            FoundIt is a web-based system developed by BSIT students of Holy Angel University as part of an academic project.
            The team collaborated in system planning, development, database design, and user interface implementation to create
            a functional and secure campus lost-and-found platform.
        </p>
        <div class="roles">
            <b>Project Leader</b> – Sto. Domingo, Francine Kimea A.<br>
            <b>Backend Developer</b> – Bondoc, Gabriel Joaquin C.<br>
            <b>Frontend Developer</b> – Garlin, Lauren A.<br>
            <b>Documentation Specialist</b> – Cruz, Jermae Fendi N.
        </div>
        <img src="image/founder.jpg" alt="Team Photo" class="team-img">
    </div>

</div>

<div class="footer-section">
    <img src="image/logo.png" alt="FoundIt Logo" class="footer-logo">
    <p class="footer-desc">A campus-exclusive lost and found system for Holy Angel University.</p>
    <div class="footer-links">
        <a href="index.php">Index</a> |
        <a href="browse.php">Browse</a> |
        <a href="list.php">List</a> |
        <a href="claim.php">Claim</a> |
        <a href="profile.php">Profile</a> |
        <a href="contactus.php">Contact Us</a>
    </div>
    <div class="footer-contact">
        ✉ foundit@hau.edu.ph<br>
        &#128205; Holy Angel University, Angeles City<br>
        © 2026 FoundIt | BSIT Academic Project
    </div>
</div>

<script src="js/global.js"></script>
<script src="js/home.js"></script>

</body>
</html>
