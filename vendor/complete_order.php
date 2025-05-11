<?php
require_once '../db_connect.php';
$conn = OpenCon();

$order_id = $_GET['id'];

// Update order_status to 'Completed'
$update_sql = "UPDATE food_order SET Order_Status='Completed' WHERE Order_ID='$order_id'";
if ($conn->query($update_sql) === TRUE) {
    echo "Order status updated to Completed";
} else {
    echo "Error updating order status: " . $conn->error;
}

// Close conn
$conn->close();

// Redirect back to Grand Order List 
header("Location: vendor_grandlist.php");
exit();
?>
