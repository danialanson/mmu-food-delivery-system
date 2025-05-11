<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Home Page</title>
    <?php include 'navbar.php'; ?>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
    h1{
        font-size: 3em;
        text-align: center;
        margin-top: 2em;
    }
    p {
        font-size: 2em;
        text-align: center;
        margin-top: 1em;
    }
    </style>
</head>
<body>
    <h1 id="adminHome">Welcome to the Admin Page</h1>
    <p>Use the navigation bar above to go to other pages.</p>

    <?php include '../footer.php'; ?>
</body>
</html>