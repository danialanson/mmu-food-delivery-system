<?php
session_start();
if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}
// Assuming you have a database connection established in $conn
require_once '../db_connect.php';
$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['vendor_id']) && !empty($_POST['vendor_id']) && isset($_POST['action'])) {
        $vendorId = $_POST['vendor_id'];
        $action = $_POST['action'];

        // Determine the new status based on the action
        $newStatus = ($action == 'Approve') ? 'Approved' : 'Rejected';

        // Construct the SQL statement
        $sql = "UPDATE vendor SET Registration_Status = '$newStatus' WHERE Vendor_ID = '$vendorId'";
        
        $conn->query($sql);
    } else {
        echo "<script>alert('Error updating vendor: " . addslashes($conn->error) . "');</script>";
    }
    header("Location: vendor_manage.php");
    exit();
}
?>