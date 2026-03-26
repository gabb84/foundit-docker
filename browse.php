<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

$search = "";
$category = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

if(isset($_GET['category'])){
    $category = mysqli_real_escape_string($conn, $_GET['category']);
}

$query = "SELECT * FROM items WHERE status='available' AND approval_status='approved'";

if($search != ""){
    $query .= " AND item_name LIKE '%$search%'";
}

if($category != ""){
    $query .= " AND category='$category'";
}

$query .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse | FoundIt</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/browse.css">
</head>

<body>

<div class="app">

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

<div class="content">

    <div class="page-title">Browse</div>

    <form method="GET" class="search">
        <input type="text" name="search" placeholder="Search item list">
        <button type="submit" class="search-icon">&#128269</button>
    </form>

    <div class="category-header">
        <h2>Categories</h2>
        <a href="browse.php?category=Others" class="others">Others</a>
    </div>

    <div class="categories">
        <a href="browse.php?category=Bottle">
            <div class="category-box"><img src="image/bottle.jpg"><span>BOTTLE</span></div>
        </a>
        <a href="browse.php?category=Umbrella">
            <div class="category-box"><img src="image/payong.jpg"><span>UMBRELLA</span></div>
        </a>
        <a href="browse.php?category=Bag">
            <div class="category-box"><img src="image/bag.jpg"><span>BAG</span></div>
        </a>
        <a href="browse.php?category=ID">
            <div class="category-box"><img src="image/id.jpg"><span>ID</span></div>
        </a>
        <a href="browse.php?category=Wallet">
            <div class="category-box"><img src="image/pitaka.jpg"><span>WALLET</span></div>
        </a>
        <a href="browse.php?category=Fan">
            <div class="category-box"><img src="image/fan.jpg"><span>FAN</span></div>
        </a>
    </div>

    <div class="latest-header">
        <h2>Latest Posts</h2>
    </div>

<?php if(mysqli_num_rows($result) > 0): ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>

        <div class="post-card">
            <img src="uploads/<?php echo $row['image']; ?>" class="post-image">
            <div class="post-details">
                <h3><?php echo $row['item_name']; ?></h3>
                <p>
                    Item ID: FI-<?php echo $row['id']; ?><br>
                    Location: <?php echo $row['location_found']; ?><br>
                    Date: <?php echo $row['date_found']; ?>
                    <?php if(!empty($row['is_edited']) && $row['is_edited'] == 1 && !empty($row['edited_at'])): ?>
                    <br><span style="font-size:0.76rem;color:#888;">✏️ Edited: <?php echo date("F d, Y \\a\\t g:i A", strtotime($row['edited_at'])); ?></span>
                    <?php endif; ?>
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                    <button class="claim-btn"
                        onclick="location.href='claim.php?item_id=<?php echo $row['id']; ?>'">
                        Claim it!
                    </button>
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['posted_by']): ?>
                        <?php if($row['is_edited'] == 0): ?>
                        <button class="claim-btn" style="background:#f59e0b;"
                            onclick="location.href='edit_post.php?item_id=<?php echo $row['id']; ?>'">
                            Edit
                        </button>
                        <?php else: ?>
                        <button class="claim-btn" style="background:#ccc;cursor:not-allowed;" disabled title="Already edited once">
                            Edit
                        </button>
                        <?php endif; ?>
                        <button class="claim-btn" style="background:#e53e3e;"
                            onclick="if(confirm('Are you sure you want to delete this post?')) location.href='delete_post.php?item_id=<?php echo $row['id']; ?>'">
                            Delete
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php endwhile; ?>

<?php else: ?>
    <p>No items available.</p>
<?php endif; ?>

</div>
</div>

<script src="js/global.js"></script>

</body>
</html>
