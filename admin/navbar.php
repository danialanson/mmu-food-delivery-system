<?php
    $username = "";
    $admin_id = ""; 

    if (isset($_SESSION['mySession'])) {
        $username = $_SESSION['mySession']; 
        if (isset($_SESSION['Admin_ID'])) {
            $admin_id = $_SESSION['Admin_ID'];
        } else {
            die("Admin ID is not set in the session.");
        }
    } else {
        die("Session data (mySession) is not set.");
    }
?>


<header class="header">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <section class="flex">
        <a href="adminhome.php" class="logo">MMU Food</a>

        <nav class="navbar">
            <a href="adminhome.php">Home</a>
            <a href="vendor_manage.php">Manage Vendor</a>
            <a href="help_manage.php">Manage Help</a>
            <a href="announce_manage.php">Manage Announcements</a>
        </nav>

        <div class="icons">
            <?php if (!empty($username)) : ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <?php endif; ?>
            <div id="user-btn" class="fa fa-user"></div>
        </div>

        <div class="profile" id="profile-menu">
            <div class="flex">
                <a href="../logout.php" class="delete-btn">Logout</a>
            </div>
        </div>
    </section>
</header>
