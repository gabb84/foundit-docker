<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn, "SELECT fullname, email FROM users WHERE ID='$user_id'");

$items_query = mysqli_query($conn,"
SELECT items.*,
(SELECT COUNT(*) FROM claims WHERE claims.item_id = items.id) AS total_claims
FROM items
WHERE posted_by='$user_id'
ORDER BY created_at DESC
");

$user = mysqli_fetch_assoc($user_query);

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
<title>Profile</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/profile.css">
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

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="admin/dashboard.php" class="menu-item"><img src="image/admin.png"> Admin Panel</a>
    <?php endif; ?>

    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>
</div>

<div class="container">
    <h1>Profile</h1>

    <img src="image/user.png" class="profile-pic">

    <div class="user-name"><?php echo $user['fullname']; ?></div>
    <div class="user-email"><?php echo $user['email']; ?></div>

    <div class="stats">
        Active Listings: <?php echo $activeListings; ?> |
        Pending Claims: <?php echo $pendingClaims; ?>
    </div>

    <div class="button-row">
        <a href="profile.php" class="primary-btn">My Listed Items</a>
        <a href="viewclaims.php" class="gray-btn">View Claims</a>
        <a href="myclaims.php" class="gray-btn">My Claims</a>
    </div>

    <p class="description">Manage your found items and review claims.</p>

    <?php if(mysqli_num_rows($items_query) > 0): ?>

        <?php while($item = mysqli_fetch_assoc($items_query)): ?>

        <div class="card">
            <p><strong>ID:</strong> FI-<?php echo $item['id']; ?></p>
            <p><strong>Item Name:</strong> <?php echo htmlspecialchars($item['item_name']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($item['status']); ?></p>
            <p><strong>Approval:</strong> <?php echo ucfirst($item['approval_status']); ?></p>
            <p><strong>Claims:</strong> <?php echo $item['total_claims']; ?></p>

            <?php if(!empty($item['is_edited']) && $item['is_edited'] == 1 && !empty($item['edited_at'])): ?>
            <p style="font-size:0.78rem;color:#888;margin-top:4px;">
                ✏️ Edited: <?php echo date("F d, Y \\a\\t g:i A", strtotime($item['edited_at'])); ?>
            </p>
            <?php endif; ?>

            <div class="card-buttons">
                <a href="viewclaims.php?item_id=<?php echo $item['id']; ?>" class="view-btn">View Claims</a>

                <?php if($item['is_edited'] == 0): ?>
                <a href="edit_post.php?item_id=<?php echo $item['id']; ?>" class="edit-btn">Edit</a>
                <?php else: ?>
                <span class="edit-btn-disabled" title="You have already edited this post once.">Edit</span>
                <?php endif; ?>

                <a href="delete_post.php?item_id=<?php echo $item['id']; ?>"
                   class="delete-btn"
                   onclick="return confirm('Are you sure you want to delete this post? This cannot be undone.');">
                   Delete
                </a>
            </div>
        </div>

        <?php endwhile; ?>

    <?php else: ?>
        <p style="text-align:center;margin-top:20px;">You have not posted any items yet.</p>
    <?php endif; ?>

</div>

<script src="js/global.js"></script>

</body>
</html>
