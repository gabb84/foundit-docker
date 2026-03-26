<?php
session_start();
include("../includes/config.php");

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../home.php");
    exit();
}

/* DELETE ITEM */
if(isset($_GET['delete'])){
    // Using mysqli_real_escape_string for a layer of security
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn,"DELETE FROM items WHERE id='$id'");
    header("Location: manage_items.php");
    exit();
}

/* APPROVE ITEM */
if(isset($_GET['approve'])){
    $id = mysqli_real_escape_string($conn, $_GET['approve']);
    mysqli_query($conn,"UPDATE items SET approval_status='approved' WHERE id='$id'");
    header("Location: manage_items.php");
    exit();
}

/* REJECT ITEM */
if(isset($_GET['reject'])){
    $id = mysqli_real_escape_string($conn, $_GET['reject']);
    mysqli_query($conn,"UPDATE items SET approval_status='rejected' WHERE id='$id'");
    header("Location: manage_items.php");
    exit();
}

/* GET ALL ITEMS */
$query = mysqli_query($conn,"
    SELECT items.*, users.fullname 
    FROM items
    LEFT JOIN users ON items.posted_by = users.ID
    ORDER BY items.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <style>
        :root {
            --primary-bg: #f4f6fa;
            --navy-dark: #0b3d70;
            --navy-light: #164e8a;
            --danger: #e53e3e;
            --text-main: #2d3748;
            --text-muted: #718096;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
            background: var(--navy-dark);
            color: var(--white);
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* CONTAINER */
        .container {
            padding: 1.5rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ITEM CARDS GRID */
        .items-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .item-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-top: 4px solid var(--navy-dark);
            display: flex;
            flex-direction: column;
        }

        .item-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--navy-dark);
            margin-bottom: 0.75rem;
        }

        .item-details {
            margin-bottom: 1rem;
        }

        .detail-row {
            font-size: 0.85rem;
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        .detail-value {
            color: var(--text-main);
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            background: #edf2f7;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--navy-dark);
            text-transform: capitalize;
        }

        /* DELETE BUTTON */
        .delete-btn {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: var(--danger);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .delete-btn:active {
            opacity: 0.8;
            transform: scale(0.98);
        }

        .actions{
            display:flex;
            gap:8px;
            margin-top:10px;
        }

        .approve-btn{
            flex:1;
            padding:0.6rem;
            background:#2fbf71;
            color:white;
            text-align:center;
            text-decoration:none;
            border-radius:8px;
            font-size:0.8rem;
            font-weight:600;
        }

        .reject-btn{
            flex:1;
            padding:0.6rem;
            background:#f59e0b;
            color:white;
            text-align:center;
            text-decoration:none;
            border-radius:8px;
            font-size:0.8rem;
            font-weight:600;
        }

        /* BACK BUTTON */
        .back-nav {
            margin-top: 2rem;
        }

        .back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.1rem;
            background: var(--navy-dark);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 600;
            box-shadow: var(--shadow);
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (min-width: 640px) {
            .items-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .items-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Manage Items</h1>
    </header>

    <main class="container">
        <div class="items-grid">
            <?php while($item = mysqli_fetch_assoc($query)): ?>
                <div class="item-card">
                    <div class="item-title">
                        <?php echo htmlspecialchars($item['item_name']); ?>
                    </div>

                    <div class="item-details">
                        <div class="detail-row">
                            <span class="detail-label">Category</span>
                            <span class="detail-value"><?php echo htmlspecialchars($item['category']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Posted By</span>
                            <span class="detail-value"><?php echo htmlspecialchars($item['fullname']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Location</span>
                            <span class="detail-value"><?php echo htmlspecialchars($item['location_found']); ?></span>
                        </div>
                        <div class="status-badge">
                            Approval: <?php echo htmlspecialchars($item['approval_status']); ?>
                        </div>
                    </div>

            <div class="actions">

            <?php if($item['approval_status'] == 'pending'): ?>

            <a href="?approve=<?php echo $item['id']; ?>" class="approve-btn">
                Approve
            </a>

            <a href="?reject=<?php echo $item['id']; ?>" class="reject-btn">
                Reject
            </a>

            <?php endif; ?>

            <a href="?delete=<?php echo $item['id']; ?>" 
                class="delete-btn"
                onclick="return confirm('Are you sure you want to delete this item?');">
                Delete
            </a>

            </div>
                </div>
            <?php endwhile; ?>
        </div>

        <nav class="back-nav">
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </nav>
    </main>

</body>
</html>