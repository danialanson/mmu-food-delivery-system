<?php
session_start();
if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}
require_once '../db_connect.php';
$conn = OpenCon();


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM announcement WHERE Announcement_ID = '$id'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Deleted successfully!');
                window.location.href='announce_manage.php';
              </script>";
    } else {
        echo "<script>alert('Error deleting record: " . addslashes($conn->error) . "');
              window.history.back();
              </script>";
    }
} else {
    echo "<script>alert('Invalid request.');
          window.history.back();
          </script>";
}

CloseCon($conn);
?>