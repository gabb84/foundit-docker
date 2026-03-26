<?php
session_start();
include("../includes/config.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../home.php");
    exit();
}

/* MARK AS READ */
if(isset($_GET['read'])){
    $id = intval($_GET['read']);
    mysqli_query($conn, "UPDATE contacts SET is_read=1 WHERE id='$id'");
    header("Location: manage_messages.php");
    exit();
}

/* DELETE MESSAGE */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM contacts WHERE id='$id'");
    header("Location: manage_messages.php");
    exit();
}

/* MARK ALL AS READ */
if(isset($_GET['readall'])){
    mysqli_query($conn, "UPDATE contacts SET is_read=1");
    header("Location: manage_messages.php");
    exit();
}

/* GET ALL MESSAGES */
$messages = mysqli_query($conn,
    "SELECT * FROM contacts ORDER BY created_at DESC"
);

$unread = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM contacts WHERE is_read=0")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — Admin</title>
    <style>
        :root{
            --navy:   #0b3d70;
            --navy2:  #164e8a;
            --bg:     #f4f6fa;
            --white:  #ffffff;
            --muted:  #718096;
            --text:   #2d3748;
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.10), 0 2px 4px -1px rgba(0,0,0,0.06);
            --radius: 12px;
        }

        *,*::before,*::after{ box-sizing:border-box; }

        body{
            margin:0; padding:0;
            font-family:'Segoe UI', Roboto, Arial, sans-serif;
            background:var(--bg);
            color:var(--text);
            line-height:1.5;
            min-height:100vh;
        }

        /* HEADER */
        .header{
            background:var(--navy);
            color:var(--white);
            padding:1rem 1.5rem;
            position:sticky;
            top:0;
            z-index:1000;
            box-shadow:0 2px 10px rgba(0,0,0,0.15);
            display:flex;
            align-items:center;
            justify-content:space-between;
        }

        .header h1{ margin:0; font-size:1.2rem; font-weight:600; }

        .unread-badge{
            background:#f59e0b;
            color:white;
            font-size:0.75rem;
            font-weight:800;
            padding:2px 9px;
            border-radius:20px;
            margin-left:10px;
        }

        /* CONTAINER */
        .container{
            padding:1.5rem;
            max-width:900px;
            margin:0 auto;
        }

        /* TOOLBAR */
        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:1.25rem;
            flex-wrap:wrap;
            gap:0.5rem;
        }

        .toolbar-title{
            font-size:0.85rem;
            color:var(--muted);
        }

        .mark-all-btn{
            padding:8px 16px;
            background:var(--navy);
            color:white;
            border:none;
            border-radius:8px;
            font-size:0.82rem;
            font-weight:600;
            cursor:pointer;
            text-decoration:none;
            transition:background 0.2s;
        }

        .mark-all-btn:hover{ background:var(--navy2); }

        /* MESSAGE CARDS */
        .messages-list{
            display:flex;
            flex-direction:column;
            gap:1rem;
        }

        .msg-card{
            background:var(--white);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            border-left:5px solid #cbd5e0;
            padding:1.25rem 1.5rem;
            position:relative;
            transition:box-shadow 0.2s;
        }

        .msg-card.unread{
            border-left-color:var(--navy);
            background:#f0f5ff;
        }

        .msg-card.unread::before{
            content:"NEW";
            position:absolute;
            top:14px;
            right:14px;
            background:var(--navy);
            color:white;
            font-size:0.65rem;
            font-weight:800;
            padding:2px 8px;
            border-radius:20px;
            letter-spacing:0.05em;
        }

        .msg-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:10px;
            flex-wrap:wrap;
            gap:6px;
        }

        .msg-name{
            font-weight:700;
            font-size:1rem;
            color:var(--navy);
        }

        .msg-date{
            font-size:0.78rem;
            color:var(--muted);
        }

        .msg-meta{
            display:flex;
            gap:1rem;
            flex-wrap:wrap;
            margin-bottom:10px;
        }

        .meta-item{
            font-size:0.82rem;
            color:var(--muted);
        }

        .meta-item strong{
            color:var(--text);
        }

        .subject-tag{
            display:inline-block;
            background:#e8f0fe;
            color:var(--navy);
            font-size:0.78rem;
            font-weight:700;
            padding:3px 10px;
            border-radius:20px;
            margin-bottom:10px;
        }

        .msg-body{
            font-size:0.9rem;
            color:var(--text);
            line-height:1.7;
            background:#f8fafc;
            border-radius:8px;
            padding:12px 14px;
            margin-bottom:14px;
            white-space:pre-wrap;
            word-break:break-word;
        }

        /* ACTIONS */
        .msg-actions{
            display:flex;
            gap:0.6rem;
            flex-wrap:wrap;
        }

        .btn{
            padding:8px 16px;
            border-radius:7px;
            font-size:0.82rem;
            font-weight:600;
            text-decoration:none;
            cursor:pointer;
            transition:opacity 0.2s;
            border:none;
        }

        .btn:hover{ opacity:0.85; }

        .btn-read{
            background:#2fbf71;
            color:white;
        }

        .btn-reply{
            background:var(--navy);
            color:white;
        }

        .btn-delete{
            background:#e53e3e;
            color:white;
        }

        /* EMPTY STATE */
        .empty{
            text-align:center;
            padding:60px 20px;
            color:var(--muted);
        }

        .empty-icon{
            font-size:3rem;
            display:block;
            margin-bottom:12px;
        }

        /* BACK NAV */
        .back-nav{ margin-top:2rem; }

        .back-btn{
            display:flex;
            align-items:center;
            justify-content:center;
            padding:1rem;
            background:var(--navy);
            color:white;
            text-decoration:none;
            border-radius:var(--radius);
            font-weight:600;
            box-shadow:var(--shadow);
        }

        @media(min-width:600px){
            .msg-card.unread::before{ top:18px; right:18px; }
        }
    </style>
</head>
<body>

<header class="header">
    <h1>
        Messages
        <?php if($unread > 0): ?>
            <span class="unread-badge"><?php echo $unread; ?> new</span>
        <?php endif; ?>
    </h1>
</header>

<main class="container">

    <div class="toolbar">
        <span class="toolbar-title">
            <?php echo mysqli_num_rows($messages); ?> message(s) total
        </span>
        <?php if($unread > 0): ?>
            <a href="?readall=1" class="mark-all-btn">Mark all as read</a>
        <?php endif; ?>
    </div>

    <div class="messages-list">
    <?php if(mysqli_num_rows($messages) == 0): ?>
        <div class="empty">
            <span class="empty-icon">&#9993;</span>
            No messages yet.
        </div>
    <?php else: ?>
        <?php while($msg = mysqli_fetch_assoc($messages)): ?>
        <div class="msg-card <?php echo $msg['is_read'] ? '' : 'unread'; ?>">

            <div class="msg-header">
                <span class="msg-name"><?php echo htmlspecialchars($msg['name']); ?></span>
                <span class="msg-date"><?php echo date("M d, Y  g:i A", strtotime($msg['created_at'])); ?></span>
            </div>

            <div class="msg-meta">
                <span class="meta-item">&#9993; <strong><?php echo htmlspecialchars($msg['email']); ?></strong></span>
            </div>

            <span class="subject-tag"><?php echo htmlspecialchars($msg['subject']); ?></span>

            <div class="msg-body"><?php echo htmlspecialchars($msg['message']); ?></div>

            <div class="msg-actions">
                <?php if(!$msg['is_read']): ?>
                    <a href="?read=<?php echo $msg['id']; ?>" class="btn btn-read">Mark as Read</a>
                <?php endif; ?>
                <a href="https://outlook.live.com/mail/0/deeplink/compose?to=<?php echo urlencode($msg['email']); ?>&subject=<?php echo urlencode('Re: '.$msg['subject']); ?>"
                   class="btn btn-reply" target="_blank">Reply</a>
                <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-delete"
                   onclick="return confirm('Delete this message?');">Delete</a>
            </div>

        </div>
        <?php endwhile; ?>
    <?php endif; ?>
    </div>

    <nav class="back-nav">
        <a href="dashboard.php" class="back-btn">&#8592; Back to Dashboard</a>
    </nav>

</main>

</body>
</html>