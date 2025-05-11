<?php
session_start();
require '../config.php';
include '../db_connect.php'; 

// Function to generate unique cart ID
function generate_cart_id($conn) {
    $query = "SELECT MAX(SUBSTRING(Cart_ID, 3)) AS max_id FROM Cart";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];

        if ($max_id !== null) {
            $next_id = sprintf('%04d', intval($max_id) + 1);
        } else {
            // If no cart_id exists yet, start with CT0001
            $next_id = '0001';
        }
    } else {
        $next_id = '0001';
    }

    return 'CT' . $next_id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and validate FoodItem_ID
        $fooditem_id = isset($_POST['FoodItem_ID']) ? $_POST['FoodItem_ID'] : null;

        if ($fooditem_id === null || $fooditem_id === '') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid FoodItem_ID']);
            exit();
        }

        // Validate and retrieve quantity
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($quantity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than zero']);
            exit();
        }

        // Check if user is logged in and retrieve Cus_ID from session
        if (!isset($_SESSION['Cus_ID'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
            exit();
        }

        $cus_id = $_SESSION['Cus_ID'];

        // Check if food item exists in the database
        $query = "SELECT * FROM Food_Item WHERE FoodItem_ID = ?";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $fooditem_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // Food item exists, fetch its details including price and availability
            $food_item = mysqli_fetch_assoc($result);
            $fooditem_price = $food_item['FoodItem_Price'];
            $is_available = $food_item['Is_Available'];

            if ($food_item['Is_Available'] == 'N') {
                // Item is not available
                $response = [
                    'status' => 'error',
                    'message' => 'Item is not available for purchase.'
                ];
                echo json_encode($response);
                exit;
            }

            // Calculate total price for the cart item
            $cartitem_price = $quantity * $fooditem_price;

            // Format cart item price to two decimal places
            $formatted_cartitem_price = number_format($cartitem_price, 2);

            // Get or create cart for the current user using Cus_ID
            $query = "SELECT * FROM Cart WHERE Cus_ID = ?";
            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "s", $cus_id);
            mysqli_stmt_execute($stmt);
            $cart_result = mysqli_stmt_get_result($stmt);

            if ($cart_result && mysqli_num_rows($cart_result) > 0) {
                // User already has a cart, fetch cart details
                $cart = mysqli_fetch_assoc($cart_result);
                $cart_id = $cart['Cart_ID'];
            } else {
                // Create new cart if not exists
                $cart_id = generate_cart_id($conn);

                $query = "INSERT INTO Cart (Cart_ID, Cus_ID) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $query);

                if (!$stmt) {
                    throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt, "ss", $cart_id, $cus_id);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_errno($stmt) !== 0) {
                    throw new Exception('Failed to insert new cart: ' . mysqli_stmt_error($stmt));
                }
            }

            // Check if the cart item already exists
            $query = "SELECT CartItem_Qty, CartItem_Price FROM Cart_Item WHERE Cart_ID = ? AND FoodItem_ID = ?";
            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "ss", $cart_id, $fooditem_id);
            mysqli_stmt_execute($stmt);
            $cart_item_result = mysqli_stmt_get_result($stmt);

            if ($cart_item_result && mysqli_num_rows($cart_item_result) > 0) {
                // Cart item exists, fetch current quantity and price
                $cart_item = mysqli_fetch_assoc($cart_item_result);
                $current_quantity = $cart_item['CartItem_Qty'];
                $current_price = $cart_item['CartItem_Price'];

                // Calculate new quantity and total price
                $new_quantity = $current_quantity + $quantity;
                $new_cartitem_price = $current_price + $cartitem_price;

                $formatted_new_cartitem_price = number_format($new_cartitem_price, 2);

                // Prepare update statement
                $update_query = "UPDATE Cart_Item SET CartItem_Qty = ?, CartItem_Price = ? WHERE Cart_ID = ? AND FoodItem_ID = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);

                if (!$update_stmt) {
                    throw new Exception('Failed to prepare update statement: ' . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($update_stmt, "dsss", $new_quantity, $formatted_new_cartitem_price, $cart_id, $fooditem_id);
                mysqli_stmt_execute($update_stmt);

                // Check if the update was successful
                if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Quantity updated in cart item: Cart_ID=' . $cart_id . ', FoodItem_ID=' . $fooditem_id,
                        'food_item' => [
                            'FoodItem_ID' => $fooditem_id,
                            'CartItem_Price' => $formatted_new_cartitem_price,
                        ],
                        'cart_id' => $cart_id,
                    ]);
                    exit();
                } else {
                    throw new Exception('Failed to update Cart_Item');
                }
            } else {
                // Insert new cart item with quantity and total price
                $insert_query = "INSERT INTO Cart_Item (Cart_ID, FoodItem_ID, CartItem_Qty, CartItem_Price) VALUES (?, ?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_query);

                if (!$insert_stmt) {
                    throw new Exception('Failed to prepare insert statement: ' . mysqli_error($conn));
                }

                // Format cart item price to two decimal places for insertion
                $formatted_cartitem_price = number_format($cartitem_price, 2);

                mysqli_stmt_bind_param($insert_stmt, "ssds", $cart_id, $fooditem_id, $quantity, $formatted_cartitem_price);
                mysqli_stmt_execute($insert_stmt);

                // Check if the insertion was successful
                if (mysqli_stmt_affected_rows($insert_stmt) > 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'New item added to cart with FoodItem_ID: ' . $fooditem_id,
                        'food_item' => [
                            'FoodItem_ID' => $fooditem_id,
                            'FoodItem_Name' => $food_item['FoodItem_Name'], 
                            'CartItem_Price' => $formatted_cartitem_price,
                        ],
                        'cart_id' => $cart_id,
                    ]);
                    exit();
                } else {
                    throw new Exception('Failed to insert into Cart_Item');
                }
            }
        } else {
            // Food item not found in database
            echo json_encode(['status' => 'error', 'message' => 'Food item not found or invalid FoodItem_ID']);
            exit();
        }
    } catch (Exception $e) {
        // Handle exceptions and errors
        echo json_encode(['status' => 'error', 'message' => 'Error processing request: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

mysqli_close($conn);
?>