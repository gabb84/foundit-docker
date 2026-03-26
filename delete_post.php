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

// Verify item belongs to this user
$stmt = mysqli_prepare($conn, "SELECT id, item_name FROM items WHERE id=? AND posted_by=?");
mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if(!$item){
    echo "<script>alert('Item not found or access denied.'); window.location='profile.php';</script>";
    exit();
}

// Check for active (pending or approved) claims
$claim_check = mysqli_prepare($conn,
    "SELECT COUNT(*) as total FROM claims
     WHERE item_id=? AND status IN ('pending','approved')"
);
mysqli_stmt_bind_param($claim_check, "i", $item_id);
mysqli_stmt_execute($claim_check);
$claim_result = mysqli_stmt_get_result($claim_check);
$claim_count  = mysqli_fetch_assoc($claim_result)['total'];

if($claim_count > 0){
    echo "<script>
        alert('This post cannot be deleted because it has " . (int)$claim_count . " active claim(s) (pending or approved). Please resolve all claims before deleting.');
        window.history.back();
    </script>";
    exit();
}

// Safe to delete
mysqli_query($conn, "DELETE FROM items WHERE id='$item_id' AND posted_by='$user_id'");

echo "<script>
    alert('Your post has been deleted successfully.');
    window.location='profile.php';
</script>";
exit();
?>
