<?php
require_once '../db_connect.php';
$conn = OpenCon();

$order_id = $_POST['id'];
$status = $_POST['status'];

// Update order status for pending items, change to accepted when pressed
$update_sql = "UPDATE food_order SET Order_Status='$status' WHERE Order_ID='$order_id'";
if ($conn->query($update_sql) === TRUE) {
    echo "Order status updated successfully";
} else {
    echo "Error updating order status: " . $conn->error;
}

$conn->close();
?>
