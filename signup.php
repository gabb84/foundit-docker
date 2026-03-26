<?php
session_start();
include("includes/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if(!preg_match("/@student\.hau\.edu\.ph$/", $email)){
        echo "<script>
        alert('Only Holy Angel University student emails are allowed.');
        window.location='signup.php';
        </script>";
        exit();
    }

    $password = $_POST['password'];
    $fullname = $fname . " " . $lname;

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $check = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Email already registered!');</script>";
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname,email,password) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $hashed_password);

        if(mysqli_stmt_execute($stmt)){

            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'user';

            header("Location: home.php");
            exit();

        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - Foundit</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/signup.css">
</head>
<body>

<div class="wrapper">

    <img src="image/logo.png" class="logo" alt="Foundit Logo">

    <div class="card">

        <h1>Create an Account</h1>
        <p>Already have an account? <a href="landingpage.php">Log in</a></p>

        <form method="POST">

            <div class="name-row">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
            </div>

            <input type="email" name="email" id="email"
            placeholder="HAU Student Email" required>

            <div class="password-wrapper">
                <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                <i class="fas fa-eye-slash toggle-password" id="loginEye" onclick="toggleLoginPassword()"></i>
            </div>

            <div class="terms">
                <input type="checkbox" name="terms" required>
                <label>I agree to the <a href="terms.php" target="_blank">Terms &amp; Conditions</a></label>
            </div>

            <button type="submit" class="signup-btn">SIGN UP</button>

        </form>

        <div class="divider">
            <span>or register with</span>
        </div>

        <div class="social">
            <a href="https://myaccount.google.com" target="_blank" class="social-btn">Google</a>
            <a href="https://account.apple.com/" target="_blank" class="social-btn">Apple</a>
        </div>

    </div>
</div>

<script src="js/signup.js"></script>

</body>
</html>