<?php
    $username = "";
    $cus_id = ""; 

    if (isset($_SESSION['mySession'])) {
        $username = $_SESSION['mySession']; 
        if (isset($_SESSION['Cus_ID'])) {
            $cus_id = $_SESSION['Cus_ID'];
        } else {
            die("Customer ID is not set in the session.");
        }
    } else {
        die("Session data (mySession) is not set.");
    }
?>
<header class="header">
    <section class="flex">
        <a href="customerhome.php" class="logo">MMU Food</a>
        <nav class="navbar">
            <a href="customerhome.php">Home</a>
            <a href="announcement.php">Announcement</a>
            <a href="feedback.php">Feedback</a>
            <a href="foodcatalogue.php">Food Catalogue</a>
            <a href="help.php">Help</a>
            <a href="cart.php">Cart</a>
        </nav>

        <div class="icons">
            <?php if (!empty($username)) : ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <?php endif; ?>
            <div id="user-btn" class="fa fa-user"></div>
        </div>

        <div class="profile" id="profile-menu">
            <div class="flex">
                <a href="userprofile.php" class="btn">Profile</a>
                <a href="../logout.php" class="delete-btn">Logout</a>
            </div>
        </div>
    </section>
</header>
