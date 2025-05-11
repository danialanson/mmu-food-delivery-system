<?php
session_start();
include '../db_connect.php';

$conn = OpenCon();

// Check if action is set
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Handle update_quantity action
    if ($action === 'update_quantity') {
        if (isset($_POST['FoodItem_ID']) && isset($_POST['update_action'])) {
            $foodItemId = $_POST['FoodItem_ID'];
            $updateAction = $_POST['update_action'];

            // Fetch current quantity
            $sql_fetch_quantity = "SELECT CartItem_Qty FROM cart_item WHERE FoodItem_ID = '$foodItemId'";
            $result_fetch_quantity = mysqli_query($conn, $sql_fetch_quantity);

            if ($result_fetch_quantity && mysqli_num_rows($result_fetch_quantity) > 0) {
                $row = mysqli_fetch_assoc($result_fetch_quantity);
                $currentQuantity = $row['CartItem_Qty'];

                // Check update action (increase or decrease)
                if ($updateAction === 'increase') {
                    if ($currentQuantity < 50) { 
                        $sql = "UPDATE cart_item SET CartItem_Qty = CartItem_Qty + 1 WHERE FoodItem_ID = '$foodItemId'";
                    } else {
                        $response = array('error' => 'Maximum quantity reached.');
                        echo json_encode($response);
                        exit;
                    }
                } elseif ($updateAction === 'decrease') {
                    if ($currentQuantity > 1) { 
                        $sql = "UPDATE cart_item SET CartItem_Qty = CartItem_Qty - 1 WHERE FoodItem_ID = '$foodItemId'";
                    } else {
                        $response = array('error' => 'Minimum quantity reached.');
                        echo json_encode($response);
                        exit;
                    }
                }

                $result = mysqli_query($conn, $sql);

                if ($result) {
                    // After updating quantity, recalculate cart item price
                    $sql_update_price = "UPDATE cart_item ci
                                         JOIN food_item f ON ci.FoodItem_ID = f.FoodItem_ID
                                         SET ci.CartItem_Price = f.FoodItem_Price * ci.CartItem_Qty
                                         WHERE ci.FoodItem_ID = '$foodItemId'";
                    $result_update_price = mysqli_query($conn, $sql_update_price);

                    if ($result_update_price) {
                        // Fetch updated quantity and price
                        $sql_fetch_updated = "SELECT CartItem_Qty, CartItem_Price FROM cart_item WHERE FoodItem_ID = '$foodItemId'";
                        $result_fetch_updated = mysqli_query($conn, $sql_fetch_updated);

                        if ($result_fetch_updated && mysqli_num_rows($result_fetch_updated) > 0) {
                            $row = mysqli_fetch_assoc($result_fetch_updated);
                            $newQuantity = $row['CartItem_Qty'];
                            $newPrice = $row['CartItem_Price'];

                            $response = array('success' => true, 'new_quantity' => $newQuantity, 'new_price' => $newPrice);
                        } else {
                            $response = array('error' => 'Failed to fetch updated quantity and price');
                        }
                    } else {
                        $response = array('error' => mysqli_error($conn));
                    }
                } else {
                    $response = array('error' => mysqli_error($conn));
                }
            } else {
                $response = array('error' => 'Failed to fetch current quantity');
            }
        } else {
            $response = array('error' => 'Missing parameters for updating quantity');
        }
    }
    elseif ($action === 'remove') {
        if (isset($_POST['FoodItem_ID'])) {
            $foodItemId = $_POST['FoodItem_ID'];
            
            // Implement SQL to remove item from cart_item table
            $sql_remove = "DELETE FROM cart_item WHERE FoodItem_ID = '$foodItemId'";
            $result_remove = mysqli_query($conn, $sql_remove);
    
            if ($result_remove) {
                $response = array('success' => true);
            } else {
                $response = array('error' => 'Failed to remove item: ' . mysqli_error($conn));
            }
        } else {
            $response = array('error' => 'Missing FoodItem_ID parameter for remove action');
        }    } else {
        $response = array('error' => 'Unsupported action');
    }
} else {
    $response = array('error' => 'Action parameter not set');
}

mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>
