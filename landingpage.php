<?php
session_start();
include("includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: home.php");
            exit();
        }
        else {
            echo "<script>alert('Invalid Email or Password');</script>";
        }

    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Foundit - Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/landingpage.css">
</head>
<body>

<div class="container">

    <img src="image/logo.png" alt="Foundit Logo" class="logo">

    <form method="POST" class="login-form">

        <input type="email" name="email" placeholder="HAU Email" required>

        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <i class="fas fa-eye-slash toggle-password"
            id="eyeIcon"
            onclick="togglePassword()"></i>
        </div>

        <a href="https://accounts.google.com/signin/v2/usernamerecovery?authuser=0&continue=http%3A%2F%2Fsupport.google.com%2Fmail%2F%3Fhl%3Den&dsh=S-1676193518%3A1773589913814876&ec=GAlAdQ&flowEntry=AddSession&flowName=GlifWebSignIn&hl=en" target="_blank" class="forgot">
            FORGOT PASSWORD?
        </a>

        <button type="submit" class="login-btn">LOG IN</button>

    </form>

    <div class="social">
        <a href="https://myaccount.google.com/" target="_blank" class="social-btn">Google</a>
        <a href="https://account.apple.com/" target="_blank" class="social-btn">Apple</a>
    </div>

    <div class="signup">
        <div class="line"></div>
        <p>DON'T HAVE AN ACCOUNT?</p>
        <div class="line"></div>
    </div>

    <p class="create">
        CREATE ONE <a href="signup.php">HERE!</a>
    </p>

</div>

<script src="js/index.js"></script>

</body>
</html>
