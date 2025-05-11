<?php
    session_start();
    include '../db_connect.php'; 

    $conn = OpenCon(); 

    $totalItems = 0;
    $subtotal = 0.00;

    $username = $_SESSION['mySession']; 
    $cus_id = "";

    if (isset($_SESSION['Cus_ID'])) {
        $cus_id = $_SESSION['Cus_ID'];
    } else {
        die("Customer ID is not set in the session.");
    }

    $sql = "SELECT ci.FoodItem_ID, ci.CartItem_Price, ci.CartItem_Qty, f.FoodItem_Name 
            FROM cart_item ci 
            JOIN food_item f ON ci.FoodItem_ID = f.Fooditem_ID 
            JOIN cart c ON c.Cart_ID = ci.Cart_ID
            WHERE c.Cus_ID = '$cus_id'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/slideshow.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .quantity-btn {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            cursor: pointer;
            background-color: transparent;
            border: none;
            margin: 5px;
            font-size: 1em;
            font-weight: bold;
        }

        table{
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- main content -->
    <div class="container">
        <h2>My Order</h2>
        <br>
        <div class="cart-summary-container">
            <div class="cart-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Food Name</th>
                            <th>Food Price (RM)</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $foodItemID = $row['FoodItem_ID'];
                                $foodItemName = $row['FoodItem_Name'];
                                $cartItemPrice = $row['CartItem_Price'];
                                $cartItemQuantity = $row['CartItem_Qty'];

                                echo "<tr>";
                                echo "<td class='text-center'>" . ++$totalItems . "</td>";
                                echo "<td class='food-name'>" . htmlspecialchars($foodItemName) . "</td>";
                                echo "<td class='food-price'>RM " . number_format($cartItemPrice, 2) . "</td>";
                                echo "<td class='text-center'>";
                                echo "<button class='quantity-btn minus-btn' data-food-item-id='$foodItemID'>-</button>";
                                echo "<span class='quantity-value'>$cartItemQuantity</span>";
                                echo "<button class='quantity-btn plus-btn' data-food-item-id='$foodItemID'>+</button>";
                                echo "</td>";
                                echo "<td class='text-center'><button class='remove-btn' data-food-item-id='" . $foodItemID . "'>Remove</button></td>";
                                echo "</tr>";

                                $subtotal = $cartItemPrice + $subtotal;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No items in your cart.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="order-summary">
                <h3>Order Summary</h3>
                <p>Total Items: <?php echo $totalItems; ?></p>
                <p>Subtotal: <span class="subtotal-value">RM <?php echo number_format($subtotal, 2); ?></span></p>
                 <button class="placeorder-btn" onclick="window.location.href='order.php'">Place Order</button>
            </div>
        </div>
    </div>
    
    <!-- footer section -->
    <?php include '../footer.php'; ?>
    <script src="../js/script.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
        // Plus button click handler
        $('.plus-btn').click(function() {
            var foodItemId = $(this).data('food-item-id');
            var currentQuantity = parseInt($(this).siblings('.quantity-value').text().trim());

            if (currentQuantity < 50) {
                updateCartItem(foodItemId, 'update_quantity', 'increase');
            } else {
                alert('Maximum quantity is 50.');
            }     
        });

        // Minus button click handler
        $('.minus-btn').click(function() {
            var foodItemId = $(this).data('food-item-id');
            var currentQuantity = parseInt($(this).siblings('.quantity-value').text().trim());

            if (currentQuantity > 1) {
                updateCartItem(foodItemId, 'update_quantity', 'decrease');
            } else {
                alert('Minimum quantity is 1.');
            }        
        });

        // Remove button click handler
        $('.remove-btn').click(function() {
            var foodItemId = $(this).data('food-item-id');
            updateCartItem(foodItemId, 'remove');
        });

        function updateCartItem(foodItemId, action, updateAction = '') {
            $.ajax({
                url: 'cart_action.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    action: action,
                    FoodItem_ID: foodItemId,
                    update_action: updateAction
                },
                success: function(data) {
                    if (data.error) {
                        console.error('Server error:', data.error);
                    } else if (data.success) {
                        console.log('Success:', data);

                        if (action === 'update_quantity') {
                            // Update quantity UI
                            var quantityElement = $('[data-food-item-id="' + foodItemId + '"]').siblings('.quantity-value');
                            quantityElement.text(data.new_quantity); 

                            // Update price UI
                            var priceElement = $('[data-food-item-id="' + foodItemId + '"]').closest('tr').find('.food-price');
                            priceElement.text('RM ' + parseFloat(data.new_price).toFixed(2)); 

                            // Calculate and update subtotal
                            updateSubtotal();
                        } else if (action === 'remove') {
                            // Remove item from UI
                            $('[data-food-item-id="' + foodItemId + '"]').closest('tr').remove();

                            // Calculate and update subtotal
                            updateSubtotal();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }

        // Function to calculate and update subtotal
        function updateSubtotal() {
            var subtotal = 0;
            $('.food-price').each(function() {
                var priceText = $(this).text().trim().replace('RM ', '');
                var price = parseFloat(priceText);
                subtotal += price;
            });

            // Update subtotal UI
            $('.subtotal-value').text('RM ' + subtotal.toFixed(2));
        }
    });
    </script>
</body>
</html>

<?php
mysqli_close($conn); 
?>
