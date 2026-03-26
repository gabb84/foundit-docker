<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: landingpage.php");
    exit();
}

include("includes/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    if($category == "Others" && !empty($_POST['other_category'])){
        $category = mysqli_real_escape_string($conn, $_POST['other_category']);
    }
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location_found = mysqli_real_escape_string($conn, $_POST['location_found']);
    $date_found = mysqli_real_escape_string($conn, $_POST['date_found']);
    $posted_by = $_SESSION['user_id'];
    $image_name = "";

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
    }

    $raw_questions = isset($_POST['questions']) ? $_POST['questions'] : [];
    $questions = [];
    foreach($raw_questions as $q){
        $q = trim($q);
        if($q !== '') $questions[] = $q;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO items
    (item_name, category, description, location_found, date_found, image, posted_by, approval_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");

    mysqli_stmt_bind_param($stmt, "sssssss",
        $item_name, $category, $description,
        $location_found, $date_found, $image_name,
        $posted_by
    );
    mysqli_stmt_execute($stmt);

    $item_id = mysqli_insert_id($conn);

    foreach($questions as $q){
        $stmt_q = mysqli_prepare($conn, "
            INSERT INTO verification_questions (item_id, question)
            VALUES (?, ?)
        ");
        mysqli_stmt_bind_param($stmt_q, "is", $item_id, $q);
        mysqli_stmt_execute($stmt_q);
    }

    echo "<script>
    alert('Your item was submitted and is waiting for admin approval.');
    window.location='browse.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>List Item</title>
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/list.css">
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

<h1>List</h1>

<div class="container">

<div class="section-title">Public Information</div>

<form method="POST" enctype="multipart/form-data">

    <div class="field-group">
        <label for="item_name">Item Name</label>
        <input type="text" id="item_name" name="item_name" placeholder="e.g. Black umbrella" required>
    </div>

    <div class="field-group">
        <label for="category">Category</label>
        <select name="category" id="category" onchange="toggleOtherCategory()" required>
            <option value="">Select Category</option>
            <option value="Bottle">Bottle</option>
            <option value="Umbrella">Umbrella</option>
            <option value="Bag">Bag</option>
            <option value="ID">ID</option>
            <option value="Wallet">Wallet</option>
            <option value="Fan">Fan</option>
            <option value="Others">Others</option>
        </select>
    </div>

    <div id="otherCategoryBox" class="field-group" style="display:none;">
        <label for="other_category">Specify Category</label>
        <input type="text" id="other_category" name="other_category" placeholder="e.g. Accessories">
    </div>

    <div class="field-group">
        <label for="location_found">Location Found</label>
        <input type="text" id="location_found" name="location_found" placeholder="e.g. Library 2nd floor" required>
    </div>

    <div class="field-group">
        <label for="date_found">Date Found</label>
        <input type="date" id="date_found" name="date_found" required>
    </div>

    <div class="field-group">
        <label for="description">Item Description</label>
        <textarea id="description" name="description" placeholder="Describe the item in detail..." required></textarea>
    </div>

    <div class="field-group">
        <span class="upload-label">Upload Image (Optional)</span>
        <div class="drag-area" id="dragArea">
            <input type="file" name="image" id="fileInput" hidden accept="image/*">
            <p><strong>Drag & drop images, or click to upload</strong></p>
            <p class="hint">Supported formats: JPG, PNG, GIF</p>
        </div>
    </div>

    <div class="section-title">Verification Questions</div>

    <div id="questionsContainer">
        <div class="question-row">
            <div class="question-header">
                <p>Question 1</p>
            </div>
            <input type="text" name="questions[]" placeholder="Enter verification question" required>
        </div>
    </div>

    <button type="button" class="add-question-btn" onclick="addQuestion()">+ Add Question</button>

    <button type="submit">Submit</button>

</form>
</div>

<script src="js/global.js"></script>
<script src="js/list.js"></script>

</body>
</html>