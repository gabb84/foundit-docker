<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ACCEPT CLAIM */
if(isset($_GET['accept'])){
    $claim_id = intval($_GET['accept']);

    $claim = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT claims.*, items.posted_by
        FROM claims
        JOIN items ON claims.item_id = items.id
        WHERE claims.id='$claim_id'
    "));

    if($claim && $claim['posted_by'] == $user_id){
        $item_id = $claim['item_id'];
        mysqli_query($conn,"UPDATE claims SET status='approved' WHERE id='$claim_id'");
        mysqli_query($conn,"UPDATE items SET status='claimed' WHERE id='$item_id'");
        mysqli_query($conn,"UPDATE claims SET status='rejected' WHERE item_id='$item_id' AND id!='$claim_id'");
    }
}

/* REJECT CLAIM */
if(isset($_GET['reject'])){
    $claim_id = intval($_GET['reject']);

    $claim = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT claims.*, items.posted_by
        FROM claims
        JOIN items ON claims.item_id = items.id
        WHERE claims.id='$claim_id'
    "));

    if($claim && $claim['posted_by'] == $user_id){
        mysqli_query($conn,"UPDATE claims SET status='rejected' WHERE id='$claim_id'");
    }
}

/* GET CLAIMS FOR ITEMS POSTED BY USER */
$query = "
SELECT claims.*, items.item_name, items.id AS item_code,
users.fullname, users.email
FROM claims
JOIN items ON claims.item_id = items.id
JOIN users ON claims.user_id = users.ID
WHERE items.posted_by='$user_id'
ORDER BY claims.created_at DESC
";

$result = mysqli_query($conn, $query);

$claims_by_item = [];
while($row = mysqli_fetch_assoc($result)){
    $claims_by_item[$row['item_id']][] = $row;
}

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
<title>View Claims</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/viewclaims.css">
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

<h1>View Claims</h1>

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
    <a href="viewclaims.php" class="primary-btn">View Claims</a>
    <a href="myclaims.php" class="gray-btn">My Claims</a>
</div>

<p class="subtitle">Review claims submitted for your listed items.</p>

<?php if(isset($message)): ?>
<div class="success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if(empty($claims_by_item)): ?>
    <p style="text-align:center;margin-top:30px;">No claims yet for your listed items.</p>
<?php endif; ?>

<?php foreach($claims_by_item as $item_id => $claims):
        $q_result = mysqli_query($conn, "
            SELECT question 
            FROM verification_questions 
            WHERE item_id = '$item_id'
        ");

        $questions = [];
        while($q = mysqli_fetch_assoc($q_result)){
            $questions[] = $q['question'];
        }
        ?>

<div class="claim-card">
    <h3>Claims for FI-<?php echo $claims[0]['item_code']; ?></h3>

    <?php foreach($claims as $claim): ?>
        <p><strong>Claimant:</strong> <?php echo htmlspecialchars($claim['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($claim['email']); ?></p>
        <p><strong>Submitted:</strong> <?php echo date("F d, Y", strtotime($claim['created_at'])); ?></p>

        <br>
        <p><strong>Answers:</strong></p>
        <?php
        $item_questions = json_decode($claims[0]['questions'] ?? '[]', true);
        $claim_answers  = json_decode($claim['answers'] ?? '[]', true);
        if(!empty($claim_answers)){
            foreach($claim_answers as $ai => $ans){
                $q_label = isset($item_questions[$ai]) ? htmlspecialchars($item_questions[$ai]) : 'Question '.($ai+1);
                echo '<p><em>'.$q_label.'</em><br>- '.htmlspecialchars($ans).'</p>';
            }
        } else {
            echo '<p style="color:#888;">No answers recorded.</p>';
        }
        ?>

        <?php if($claim['status'] == "pending"): ?>
            <div class="button-group">
                <a href="?accept=<?php echo $claim['id']; ?>" class="accept">Accept</a>
                <a href="?reject=<?php echo $claim['id']; ?>" class="reject">Reject</a>
                <a href="https://outlook.live.com/mail/0/deeplink/compose?to=<?php echo urlencode($claim['email']); ?>&subject=<?php echo urlencode('FoundIt Claim for Item FI-'.$claim['item_code']); ?>"
                   class="message" target="_blank">Message</a>
            </div>
        <?php else: ?>
            <p><strong>Status:</strong>
                <?php if($claim['status'] == "approved"): ?>
                    <span style="color:green; font-weight:bold;">Approved</span>
                <?php elseif($claim['status'] == "rejected"): ?>
                    <span style="color:red; font-weight:bold;">Rejected</span>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if (count($claims) > 1 && $claim !== end($claims)): ?>
            <hr style="margin: 20px 0; border: 0; border-top: 1px dashed #ccc;">
        <?php endif; ?>

    <?php endforeach; ?>
</div>
<?php endforeach; ?>

</div>

<script src="js/global.js"></script>

</body>
</html>