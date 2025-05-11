<?php
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>

<header class="header">
    <section class="flex">
        <a href="index.php" class="logo">MMU Food</a>

        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="aboutus.php">About Us</a>
        </nav>
     
        <div class="icons">
            <div id="user-btn" class="fa fa-user"></div>
        </div>

        <div class="profile" id="profile-menu">
            <div class="flex">
                <a href="login.php">Login</a><br>
                <a href="register.php">Register</a>
            </div>
        </div>
    </section>
</header>
