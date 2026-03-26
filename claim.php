<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

if(!isset($_GET['item_id'])){
    header("Location: browse.php");
    exit();
}

$item_id = intval($_GET['item_id']);
$user_id = $_SESSION['user_id'];

$item_query = mysqli_query($conn, "SELECT * FROM items WHERE id='$item_id'");
$item = mysqli_fetch_assoc($item_query);

if(!$item){
    header("Location: browse.php");
    exit();
}

$questions = [];

$q_result = mysqli_query($conn, "
    SELECT question 
    FROM verification_questions 
    WHERE item_id = '$item_id'
");

while($row = mysqli_fetch_assoc($q_result)){
    $questions[] = $row['question'];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Collect answers — trim only, let the prepared statement handle escaping
    $raw_answers = isset($_POST['answers']) ? $_POST['answers'] : [];
    $answers = [];
    foreach($raw_answers as $a){
        $answers[] = trim($a);
    }
    $answers_json = json_encode($answers);   // plain JSON, no manual escaping
    $comment = trim($_POST['comment'] ?? '');

    $stmt = mysqli_prepare($conn, "INSERT INTO claims
    (item_id, user_id, answers, comment, status)
    VALUES (?, ?, ?, ?, 'pending')");

    mysqli_stmt_bind_param($stmt, "iiss", $item_id, $user_id, $answers_json, $comment);
    mysqli_stmt_execute($stmt);

    header("Location: approved.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Claim Item</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/claim.css">
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

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="admin/dashboard.php" class="menu-item"><img src="image/admin.png"> Admin Panel</a>
    <?php endif; ?>

    <a href="logout.php" class="menu-item"><img src="image/out.png"> Log Out</a>
</div>

<div class="container">

<h1>Claim</h1>

<div class="search-box">
    <input type="text" placeholder="Enter Item ID here...">
</div>

<div class="summary">
    <h3>Item Summary</h3>
    <p><strong>Item Name:</strong> <?php echo htmlspecialchars($item['item_name']); ?></p>
    <p><strong>Location Found:</strong> <?php echo htmlspecialchars($item['location_found']); ?></p>
    <p><strong>Date Found:</strong> <?php echo htmlspecialchars($item['date_found']); ?></p>
</div>

<div class="instructions">
    To claim this item, please answer the verification questions below accurately.<br><br>
    Your responses will be reviewed by the finder.<br><br>
    Submitting this form <strong>does not automatically approve</strong> your claim.
</div>

<form method="POST">

<?php if(!empty($questions)): ?>
    <?php foreach($questions as $landingpage => $question): ?>
    <div class="question">
        <label>Question <?php echo $landingpage + 1; ?>: <?php echo htmlspecialchars($question); ?></label>
        <textarea name="answers[]" rows="3" required></textarea>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="color:#888; text-align:center; margin-bottom:20px;">No verification questions were set for this item.</p>
<?php endif; ?>

<div class="comment-section">
    <label class="comment-label">Additional Comment</label>
    <textarea name="comment" class="comment-textarea" placeholder="Optional: Add extra details or anything else you'd like the finder to know..."></textarea>
</div>

<button type="submit" class="submit-btn">Submit Claim</button>

</form>

</div>

<script src="js/global.js"></script>

</body>
</html>