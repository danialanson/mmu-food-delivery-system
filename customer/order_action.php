<?php
session_start();
include '../db_connect.php';

$conn = OpenCon();

$cus_id = $_SESSION['Cus_ID'] ?? '';
if (!$cus_id) {
    die("Customer ID is not set in the session.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $card_name = mysqli_real_escape_string($conn, $_POST['card_name']);
    $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
    $total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);

    // Generate Payment_ID
    $sql = "SELECT MAX(Payment_ID) AS max_id FROM payment";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];

    if ($maxId) {
        $newId = substr($maxId, 2); 
        $newId = (int)$newId + 1;  
        $newId = 'PM' . sprintf('%03d', $newId);
    } else {
        $newId = 'PM001';
    }

    $payment_id = $newId;

    // Insert payment details
    $sql = "INSERT INTO payment (Payment_ID, Cus_ID, Payment_Amount, Card_Name, Card_Number, CVV) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsss", $payment_id, $cus_id, $total_amount, $card_name, $card_number, $cvv);

    if (mysqli_stmt_execute($stmt)) {
        // Generate Order_ID
        $sql = "SELECT MAX(Order_ID) AS max_id FROM food_order";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $maxId = $row['max_id'];

        if ($maxId) {
            $newId = substr($maxId, 2); 
            $newId = (int)$newId + 1;  
            $newId = 'OR' . sprintf('%03d', $newId);
        } else {
            $newId = 'OR001';
        }

        $order_id = $newId;

        // Insert order details
        $sql = "INSERT INTO food_order (Order_ID, Cus_ID, Payment_ID, Order_Date, Order_Address, Total_Amount, Order_Status) VALUES (?, ?, ?, NOW(), ?, ?, 'Pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssd", $order_id, $cus_id, $payment_id, $address, $total_amount);

        if (mysqli_stmt_execute($stmt)) {
            // Insert order items
            $sql = "INSERT INTO order_item (Order_ID, FoodItem_ID, OrderItem_Qty, OrderItem_Price)
                    SELECT ?, FoodItem_ID, CartItem_Qty, CartItem_Price FROM cart_item WHERE Cart_ID IN (SELECT Cart_ID FROM cart WHERE Cus_ID = ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $order_id, $cus_id);

            if (mysqli_stmt_execute($stmt)) {
                // Clear the cart
                $sql = "DELETE FROM cart_item WHERE Cart_ID IN (SELECT Cart_ID FROM cart WHERE Cus_ID = ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $cus_id);
                mysqli_stmt_execute($stmt);

                header("Location: order_success.php?order_id=$order_id");
                exit();
            } else {
                $errorMessage = "Error submitting order items: " . mysqli_error($conn);
            }
        } else {
            $errorMessage = "Error submitting order: " . mysqli_error($conn);
        }
    } else {
        $errorMessage = "Error submitting payment: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
