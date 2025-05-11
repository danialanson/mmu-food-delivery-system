<?php
    $username = "";
    $vendor_id = ""; 

    if (isset($_SESSION['mySession'])) {
        $username = $_SESSION['mySession']; 
        if (isset($_SESSION['Vendor_ID'])) {
            $vendor_id = $_SESSION['Vendor_ID'];
        } else {
            die("Vendor ID is not set in the session.");
        }
    } else {
        die("Session data (mySession) is not set.");
    }
?>

<header class="header">
    <section class="flex">
        <a class="logo">MMU Food</a>
        <nav class="navbar">
            <a href="vendor_home.php">Home</a>
            <a href="vendor_grandlist.php">Order List</a>
            <a href="vendor_shopmanagement.php">Restaurant Management</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </section>
</header>