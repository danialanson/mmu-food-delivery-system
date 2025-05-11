<?php
session_start();
if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}
// Include your database connection script
require_once '../db_connect.php';
$conn = OpenCon();

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['help_id']) && isset($_POST['reply'])) {
    $id = mysqli_real_escape_string($conn, $_POST['help_id']);
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    $adminId = $_SESSION['Admin_ID'];
    
    $sql = "UPDATE help SET Reply = '$reply', Admin_ID = '$adminId' WHERE Help_ID = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Reply submitted successfully!');
                window.location.href='help_manage.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Error updating record: " . addslashes($conn->error) . "');
              window.history.back();
              </script>";
    }
} else {
    echo "<script>alert('Invalid request.');</script>";
}

// Close connection
CloseCon($conn);
?>