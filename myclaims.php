<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

include("includes/config.php");

$user_id = $_SESSION['user_id'];

$query = "
SELECT claims.*, items.item_name, items.id AS item_code
FROM claims
JOIN items ON claims.item_id = items.id
WHERE claims.user_id='$user_id'
ORDER BY claims.created_at DESC
";

$result = mysqli_query($conn, $query);

$activeListings_query = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM items
WHERE posted_by='$user_id' AND status='available'
");
$activeListings = mysqli_fetch_assoc($activeListings_query)['total'];

$pendingClaims_query = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM claims
JOIN items ON claims.item_id = items.id
WHERE items.posted_by='$user_id' AND claims.status='pending'
");
$pendingClaims = mysqli_fetch_assoc($pendingClaims_query)['total'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Claims</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/myclaims.css">
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
    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>
</div>

<div class="container">

<h1>My Claims</h1>

<div class="profile-top">
    <img src="image/user.png" class="profile-main-pic">
    <h2><?php echo htmlspecialchars($_SESSION['fullname']); ?></h2>
    <p class="email"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
</div>

    <div class="stats">
        Active Listings: <?php echo $activeListings; ?> |
        Pending Claims: <?php echo $pendingClaims; ?>
    </div>

<div class="button-row">
    <a href="profile.php" class="gray-btn">My Listed Items</a>
    <a href="viewclaims.php" class="gray-btn">View Claims</a>
    <a href="myclaims.php" class="primary-btn">My Claims</a>
</div>

<p class="subtitle">Track the status of items you have attempted to claim.</p>

<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="claim-card">
            <p><strong>Item ID:</strong> FI-<?php echo htmlspecialchars($row['item_code']); ?></p>
            <p><strong>Item Name:</strong> <?php echo htmlspecialchars($row['item_name']); ?></p>
            <p><strong>Status:</strong>
                <span class="highlight">
                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                </span>
            </p>
            <p><strong>Date Submitted:</strong> <?php echo date("F d, Y", strtotime($row['created_at'])); ?></p>

            <?php if($row['status'] == 'pending'): ?>
                <p>The finder is currently <span class="highlight">reviewing</span> your claim.</p>
            <?php elseif($row['status'] == 'approved'): ?>
                <p>The finder has <span style="color: green; font-weight: bold;">approved</span> your claim!</p>
                <p>You may now coordinate through HAU email.</p>
            <?php elseif($row['status'] == 'rejected'): ?>
                <p style="color: #f44336;">This claim was not approved by the finder.</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center; margin-top:20px; color:#777;">You have not submitted any claims yet.</p>
<?php endif; ?>

</div>

<script src="js/global.js"></script>

</body>
</html>
