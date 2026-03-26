<?php
session_start();
include("includes/config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

if($item_id <= 0){
    header("Location: profile.php");
    exit();
}

// Fetch item — must belong to this user
$stmt = mysqli_prepare($conn, "SELECT * FROM items WHERE id=? AND posted_by=?");
mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if(!$item){
    echo "<script>alert('Item not found or access denied.'); window.location='profile.php';</script>";
    exit();
}

// Block if already edited once
if($item['is_edited'] == 1){
    echo "<script>alert('You have already edited this post. Each post can only be edited once.'); window.location='profile.php';</script>";
    exit();
}

$errors = [];

// Handle POST (save edit)
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $item_name      = trim($_POST['item_name']);
    $category       = trim($_POST['category']);
    if($category == 'Others' && !empty(trim($_POST['other_category']))){
        $category = trim($_POST['other_category']);
    }
    $description    = trim($_POST['description']);
    $location_found = trim($_POST['location_found']);
    $date_found     = trim($_POST['date_found']);

    if(empty($item_name))      $errors[] = "Item name is required.";
    if(empty($category))       $errors[] = "Category is required.";
    if(empty($description))    $errors[] = "Description is required.";
    if(empty($location_found)) $errors[] = "Location is required.";
    if(empty($date_found))     $errors[] = "Date found is required.";

    // Handle questions
    $raw_questions = isset($_POST['questions']) ? $_POST['questions'] : [];
    $questions = [];
    foreach($raw_questions as $q){
        $q = trim($q);
        if($q !== '') $questions[] = $q;
    }
    if(empty($questions)){
        $errors[] = "At least one verification question is required.";
    }

    // Handle image (keep old if none uploaded)
    $image_name = $item['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
    }

    if(empty($errors)){
        $edited_at = date('Y-m-d H:i:s');

        $upd = mysqli_prepare($conn,
            "UPDATE items SET
                item_name=?, category=?, description=?,
                location_found=?, date_found=?, image=?,
                is_edited=1, edited_at=?,
                approval_status='pending'
            WHERE id=? AND posted_by=?"
        );
        mysqli_stmt_bind_param($upd, "sssssssii",
            $item_name, $category, $description,
            $location_found, $date_found, $image_name,
            $edited_at,
            $item_id, $user_id
        );
        mysqli_stmt_execute($upd);

        mysqli_query($conn, "DELETE FROM verification_questions WHERE item_id = '$item_id'");

        // Insert new questions
        foreach($questions as $q){
            $stmt_q = mysqli_prepare($conn, "
                INSERT INTO verification_questions (item_id, question)
                VALUES (?, ?)
            ");
            mysqli_stmt_bind_param($stmt_q, "is", $item_id, $q);
            mysqli_stmt_execute($stmt_q);
        }

        echo "<script>
            alert('Your post has been updated and is awaiting admin re-approval. It will be hidden from browse until approved.');
            window.location='profile.php';
        </script>";
        exit();
    }
}

$existing_questions = [];

$q_result = mysqli_query($conn, "
    SELECT question 
    FROM verification_questions 
    WHERE item_id = '$item_id'
");

while($row = mysqli_fetch_assoc($q_result)){
    $existing_questions[] = $row['question'];
}

// Check if category is a preset value
$preset_categories = ['Bottle','Umbrella','Bag','ID','Wallet','Fan','Others'];
$is_other_category = !in_array($item['category'], $preset_categories);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Post | FoundIt</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/list.css">
<style>
.edit-notice {
    background: #fff8e1;
    border-left: 4px solid #f59e0b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.88rem;
    color: #7c5c00;
    line-height: 1.5;
}
.edit-notice strong {
    display: block;
    margin-bottom: 4px;
    font-size: 0.93rem;
}
.error-box {
    background: #fff5f5;
    border-left: 4px solid #e53e3e;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.88rem;
    color: #c53030;
}
.error-box ul { margin: 6px 0 0 16px; padding: 0; }
</style>
</head>
<body>

<header>
    <img src="image/logo.png" class="logo">
    <div class="hamburger" onclick="toggleMenu()">
        <div></div><div></div><div></div>
    </div>
</header>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="profile-header">
        <div class="profile-content">
            <a href="index.php">
                <img src="image/user.png" class="profile-pic">
                <div class="profile-name"><?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
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

<h1>Edit Post</h1>

<div class="container">

    <div class="edit-notice">
        <strong>⚠️ One-time edit only</strong>
        You can only edit this post once. After saving, it will be hidden from browse and sent for admin re-approval. Make sure all changes are correct before submitting.
    </div>

    <?php if(!empty($errors)): ?>
    <div class="error-box">
        <strong>Please fix the following:</strong>
        <ul>
            <?php foreach($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="section-title">Public Information</div>

    <form method="POST" enctype="multipart/form-data">

        <div class="field-group">
            <label for="item_name">Item Name</label>
            <input type="text" id="item_name" name="item_name"
                value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
        </div>

        <div class="field-group">
            <label for="category">Category</label>
            <select name="category" id="category" onchange="toggleOtherCategory()" required>
                <option value="">Select Category</option>
                <?php foreach(['Bottle','Umbrella','Bag','ID','Wallet','Fan','Others'] as $cat): ?>
                <option value="<?php echo $cat; ?>"
                    <?php echo ((!$is_other_category && $item['category']==$cat) || ($is_other_category && $cat=='Others')) ? 'selected' : ''; ?>>
                    <?php echo $cat; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="otherCategoryBox" class="field-group" style="display:<?php echo $is_other_category ? 'block' : 'none'; ?>;">
            <label for="other_category">Specify Category</label>
            <input type="text" id="other_category" name="other_category"
                value="<?php echo $is_other_category ? htmlspecialchars($item['category']) : ''; ?>"
                placeholder="e.g. Accessories">
        </div>

        <div class="field-group">
            <label for="location_found">Location Found</label>
            <input type="text" id="location_found" name="location_found"
                value="<?php echo htmlspecialchars($item['location_found']); ?>" required>
        </div>

        <div class="field-group">
            <label for="date_found">Date Found</label>
            <input type="date" id="date_found" name="date_found"
                value="<?php echo htmlspecialchars($item['date_found']); ?>" required>
        </div>

        <div class="field-group">
            <label for="description">Item Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
        </div>

        <div class="field-group">
            <span class="upload-label">Upload New Image (leave blank to keep current)</span>
            <?php if($item['image']): ?>
                <p style="font-size:0.82rem;color:#666;margin:4px 0 8px;">
                    Current image: <em><?php echo htmlspecialchars($item['image']); ?></em>
                </p>
            <?php endif; ?>
            <div class="drag-area" id="dragArea">
                <input type="file" name="image" id="fileInput" hidden accept="image/*">
                <p><strong>Drag & drop images, or click to upload</strong></p>
                <p class="hint">Supported formats: JPG, PNG, GIF</p>
            </div>
        </div>

        <div class="section-title">Verification Questions</div>

        <div id="questionsContainer">
            <?php foreach($existing_questions as $i => $q): ?>
            <div class="question-row">
                <div class="question-header">
                    <p>Question <?php echo $i+1; ?></p>
                </div>
                <input type="text" name="questions[]"
                    value="<?php echo htmlspecialchars($q); ?>"
                    placeholder="Enter verification question" required>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="add-question-btn" onclick="addQuestion()">+ Add Question</button>

        <button type="submit">Save Changes</button>

    </form>
</div>

<script src="js/global.js"></script>
<script src="js/list.js"></script>
<script>
// Re-count questions on load so addQuestion() numbering is correct
document.addEventListener('DOMContentLoaded', function(){
    const rows = document.querySelectorAll('#questionsContainer .question-row');
    rows.forEach((row, i) => {
        const label = row.querySelector('.question-header p');
        if(label) label.textContent = 'Question ' + (i + 1);
    });
});
</script>

</body>
</html>
