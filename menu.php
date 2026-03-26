<?php if($_SESSION['role'] == 'admin'): ?>

<a href="admin/dashboard.php" class="menu-item">
<img src="image/admin.png"> Admin Panel
</a>

<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoundIt - Home</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:url("image/menubg.png") no-repeat center center;
    background-size:cover;
    height:100vh;
    overflow:hidden;
    position:relative;
    color:white;
}

.overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.4);
    z-index:1;
}

header{
    position:absolute;
    top:0;
    width:100%;
    padding:20px 30px 20px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    z-index:3;
}

.logo{
    width:190px;
}

.hamburger{
    width:28px;
    cursor:pointer;
    margin-right:10px;
    z-index:4;
}

.hamburger div{
    height:4px;
    background:white;
    margin:5px 0;
    border-radius:2px;
}


.sidebar{
    position:fixed;
    right:-100%;
    top:0;
    width:85%;
    height:100%;
    background:#f2f2f2;
    transition:0.3s ease;
    z-index:5;
    overflow-y:auto;
}

.sidebar.active{
    right:0;
}


.profile-header{
    background:url("image/menubg.png") no-repeat center center;
    background-size:cover;
    padding:25px 20px;
    color:white;
    position:relative;
}

.profile-pic{
    width:80px;
    height:80px;
    border-radius:50%;
    background:url("image/user.png") center center;
    background-size:cover;
    margin-bottom:10px;
}


.profile-header::after{
    content:"";
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.5);
}

.profile-content{
    position:relative;
    z-index:2;
}

.profile-content a{
    all: unset;
    display:block;
    cursor:pointer;
}

.profile-content{
    color: white;
}


.profile-pic{
    width:70px;
    height:70px;
    background:#ddd;
    border-radius:50%;
    margin-bottom:10px;
}

.profile-name{
    font-weight:bold;
    font-size:16px;
}

.profile-email{
    font-size:13px;
}

.bell{
    position:absolute;
    right:20px;
    top:20px;
    font-size:22px;
    z-index:2;
}


.menu-item{
    display:flex;
    align-items:center;
    padding:20px;
    font-size:18px;
    color:#555;
    text-decoration:none;
    border-bottom:1px solid #ddd;
}

.menu-item img{
    width:30px;
    margin-right:20px;
}

.menu-item:hover{
    background:#e6e6e6;
}


.content{
    position:relative;
    z-index:2;
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:20px;
}

.hero-image{
    width:100%;
    max-width:320px;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="overlay"></div>

<header>
    <img src="image/menulogo.png" class="logo">
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
</header>

<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">

    <div class="profile-header">
        <div class="bell">🔔</div>

        <div class="profile-content">
            <a href="menu.php">
                <img src="image/user.png" class="profile-pic" alt="Profile Picture">
                <div class="profile-name"><?php echo $_SESSION['fullname']; ?></div>
                <div class="profile-email"><?php echo $_SESSION['email']; ?></div>
            </a>
        </div>
    </div>

    <a href="home.php" class="menu-item">
        <img src="image/home.png"> Home
    </a>

    <a href="browse.php" class="menu-item">
        <img src="image/lost.png"> Browse
    </a>

    <a href="list.php" class="menu-item">
        <img src="image/list.png"> List
    </a>

    <a href="claim.php" class="menu-item">
        <img src="image/found.png"> Claim
    </a>

    <a href="profile.php" class="menu-item">
        <img src="image/profile.png"> Profile
    </a>

    <a href="contactus.php" class="menu-item">
        <img src="image/contact.png"> Contact Us
    </a>

        <a href="logout.php" class="menu-item">
        <img src="image/out.png"> Log Out
    </a>

</div> 

<div class="content">
    <div>
        <img src="image/menufoundit.png" class="hero-image" alt="Lost Something Banner">
        <p>Post lost items, browse found belongings, and get back what’s yours—fast and easy!</p>
    </div>
</div>

<script>
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("active");
}
</script>

</body>
</html>
