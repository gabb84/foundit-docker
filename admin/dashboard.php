<?php
session_start();
include("../includes/config.php");

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../home.php");
    exit();
}

/* DASHBOARD STATS */
// total users
$userQuery = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
$totalUsers = mysqli_fetch_assoc($userQuery)['total'];

// total items
$itemQuery = mysqli_query($conn,"SELECT COUNT(*) AS total FROM items");
$totalItems = mysqli_fetch_assoc($itemQuery)['total'];

// available items
$availableQuery = mysqli_query($conn,"SELECT COUNT(*) AS total FROM items WHERE status='available'");
$availableItems = mysqli_fetch_assoc($availableQuery)['total'];

// unread messages
$unreadQuery = mysqli_query($conn,"SELECT COUNT(*) AS total FROM contacts WHERE is_read=0");
$unreadMessages = mysqli_fetch_assoc($unreadQuery)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-bg: #f4f6fa;
            --navy-dark: #0b3d70;
            --navy-light: #164e8a;
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

        /* MAIN CONTAINER */
        .dashboard {
            padding: 1.5rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* WELCOME SECTION */
        .welcome {
            margin-bottom: 2rem;
        }

        .welcome-hint {
            display: block;
            width: 30px;
            height: 4px;
            background: var(--navy-dark);
            margin-bottom: 8px;
            border-radius: 2px;
        }

        .welcome p {
            margin: 0;
            font-size: 1.1rem;
            color: var(--navy-dark);
            font-weight: 500;
        }

        /* STATS GRID */
        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-left: 5px solid var(--navy-dark);
        }

        /* Mobile specific grid logic: 3rd card fills row */
        .stat-card:last-child {
            grid-column: span 2;
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: var(--navy-dark);
        }

        .stat-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Contextual Border Colors */
        .blue { border-left-color: #1e6bd6; }
        .green { border-left-color: #2fbf71; }
        .orange { border-left-color: #f59e0b; }

        /* ACTION BUTTONS */
        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.1rem;
            background: var(--navy-dark);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 600;
            transition: transform 0.1s ease, background 0.2s ease;
            box-shadow: 0 4px 6px rgba(11, 61, 112, 0.15);
        }

        .action-btn:active {
            transform: scale(0.98);
        }

        .action-btn.secondary {
            background: #4a5568;
            margin-top: 0.5rem;
        }

        /* MESSAGES BADGE */
        .messages-btn {
            position: relative;
        }

        .msg-badge {
            background: #f59e0b;
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 20px;
            margin-left: 8px;
        }

        /* RESPONSIVE QUERIES */
        @media (min-width: 768px) {
            .stats {
                grid-template-columns: repeat(3, 1fr);
            }
            .stat-card:last-child {
                grid-column: span 1;
            }
            .actions {
                flex-direction: row;
                flex-wrap: wrap;
            }
            .action-btn {
                width: auto;
                min-width: 180px;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Admin Dashboard</h1>
    </header>

    <main class="dashboard">
        <section class="welcome">
            <span class="welcome-hint"></span>
            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong></p>
        </section>

        <section class="stats">
            <div class="stat-card blue">
                <span class="stat-number"><?php echo number_format($totalUsers); ?></span>
                <span class="stat-label">Total Users</span>
            </div>

            <div class="stat-card green">
                <span class="stat-number"><?php echo number_format($totalItems); ?></span>
                <span class="stat-label">Total Items</span>
            </div>

            <div class="stat-card orange">
                <span class="stat-number"><?php echo number_format($availableItems); ?></span>
                <span class="stat-label">Available</span>
            </div>
        </section>

        <nav class="actions">
            <a href="manage_items.php" class="action-btn">Manage Items</a>
            <a href="manage_claims.php" class="action-btn">Manage Claims</a>
            <a href="manage_messages.php" class="action-btn messages-btn">
                Messages
                <?php if($unreadMessages > 0): ?>
                    <span class="msg-badge"><?php echo $unreadMessages; ?></span>
                <?php endif; ?>
            </a>
            <a href="../home.php" class="action-btn secondary">Back to Site</a>
        </nav>
    </main>

</body>
</html>