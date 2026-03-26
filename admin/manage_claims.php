<?php
session_start();
include("../includes/config.php");

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id'])){
    header("Location: ../home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT role FROM users WHERE ID = '$user_id'");
$user = mysqli_fetch_assoc($result);

if($user['role'] !== 'admin'){
    header("Location: ../home.php");
    exit();
}

/* APPROVE CLAIM */
if(isset($_GET['approve'])){
$id = $_GET['approve'];

/* approve claim */
mysqli_query($conn,"UPDATE claims SET status='approved' WHERE id='$id'");

/* get item id of the claim */
$claim = mysqli_query($conn,"SELECT item_id FROM claims WHERE id='$id'");
$row = mysqli_fetch_assoc($claim);

/* mark item as claimed */
mysqli_query($conn,"UPDATE items SET status='claimed' WHERE id='".$row['item_id']."'");

header("Location: manage_claims.php");
exit();
}

/* REJECT CLAIM */
if(isset($_GET['reject'])){
    $id = mysqli_real_escape_string($conn, $_GET['reject']);
    mysqli_query($conn,"UPDATE claims SET status='rejected' WHERE id='$id'");
    header("Location: manage_claims.php");
    exit();
}

/* GET ALL CLAIMS */
$query = mysqli_query($conn,"
    SELECT claims.*, users.fullname, items.item_name
    FROM claims
    LEFT JOIN users ON claims.user_ID = users.ID
    LEFT JOIN items ON claims.item_id = items.id
    ORDER BY claims.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Claims</title>
    <style>
        :root {
            --primary-bg: #f4f6fa;
            --navy-dark: #0b3d70;
            --navy-light: #164e8a;
            --success: #2fbf71;
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

        /* CLAIMS GRID */
        .claims-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .claim-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-left: 5px solid #cbd5e0; /* Default neutral border */
            display: flex;
            flex-direction: column;
        }

        /* Status-based Border Colors */
        .status-pending { border-left-color: #f59e0b; }
        .status-approved { border-left-color: var(--success); }
        .status-rejected { border-left-color: var(--danger); }

        .claim-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--navy-dark);
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .claim-info {
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .info-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            color: var(--text-main);
            word-break: break-word;
        }

        .status-text {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 4px;
            background: #edf2f7;
            margin-top: 0.5rem;
        }

        /* BUTTONS */
        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }

        .btn {
            padding: 0.85rem;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: transform 0.1s ease, opacity 0.2s;
        }

        .btn:active {
            transform: scale(0.96);
        }

        .btn-approve {
            background: var(--success);
            color: white;
        }

        .btn-reject {
            background: var(--danger);
            color: white;
        }

        /* BACK NAVIGATION */
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

        /* RESPONSIVE */
        @media (min-width: 768px) {
            .claims-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Manage Claims</h1>
    </header>

    <main class="container">
        <div class="claims-grid">
            <?php while($claim = mysqli_fetch_assoc($query)): ?>
                <div class="claim-card status-<?php echo $claim['status']; ?>">
                    <div class="claim-title">
                        <span>Item: <?php echo htmlspecialchars($claim['item_name']); ?></span>
                    </div>

                    <div class="info-label">Claimed By</div>
                    <div class="info-value" style="margin-bottom: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($claim['fullname']); ?>
                    </div>

                    <?php
                    $claim_answers = json_decode($claim['answers'] ?? '[]', true);
                    if(!empty($claim_answers)){
                        foreach($claim_answers as $ai => $ans){
                            echo '<div class="claim-info">';
                            echo '<span class="info-label">Answer ' . ($ai+1) . '</span>';
                            echo '<span class="info-value">' . htmlspecialchars($ans) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="claim-info"><span class="info-value" style="color:#999;">No answers recorded.</span></div>';
                    }
                    ?>

                    <div>
                        <span class="status-text">Status: <?php echo $claim['status']; ?></span>
                    </div>

                    <?php if($claim['status'] == 'pending'): ?>
                        <div class="btn-group">
                            <a href="?approve=<?php echo $claim['id']; ?>" class="btn btn-approve" onclick="return confirm('Approve this claim?');">
                                Approve
                            </a>
                            <a href="?reject=<?php echo $claim['id']; ?>" class="btn btn-reject" onclick="return confirm('Reject this claim?');">
                                Reject
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <nav class="back-nav">
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </nav>
    </main>

</body>
</html>