<?php
session_start();
include '../db_connect.php';

$conn = OpenCon();

$totalItems = 0;
$subtotal = 0.00;

$username = $_SESSION['mySession']; 
$cus_id = $_SESSION['Cus_ID'] ?? '';

if (!$cus_id) {
    die("Customer ID is not set in the session.");
}

$sql = "SELECT ci.FoodItem_ID, ci.CartItem_Price, ci.CartItem_Qty, f.FoodItem_Name 
        FROM cart_item ci 
        JOIN food_item f ON ci.FoodItem_ID = f.FoodItem_ID 
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
    <title>Order | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/slideshow.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        table {
            font-size: 1.5em;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-row label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-row input, .form-row textarea {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-row textarea {
            resize: vertical;
        }

        .order-summary {
            margin-top: 20px;
        }

        .placeorder-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
        }

        .placeorder-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- main content -->
    <div class="container">
        <h2>Order Review</h2>
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
                                echo "<td class='text-center'>" . htmlspecialchars($cartItemQuantity) . "</td>";
                                echo "</tr>";

                                $subtotal += $cartItemPrice;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No items in your cart.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="order-summary">
                <h3>Total Amount</h3>
                <p>Total Items: <?php echo $totalItems; ?></p>
                <p>Subtotal: <span class="subtotal-value">RM <?php echo number_format($subtotal, 2); ?></span></p>
            </div>
        </div>
        <form id="orderForm" action="order_action.php" method="post">
            <div class="form-row">
                <label for="address">Delivery Address:</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>
            <div class="form-row">
                <label for="card_name">Card Holder's Name:</label>
                <input type="text" id="card_name" name="card_name" required>
            </div>
            <div class="form-row">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" maxlength="16" required>
            </div>
            <div class="form-row">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" maxlength="3" required>
            </div>
            <div class="form-row">
                <input type="hidden" name="total_amount" value="<?php echo number_format($subtotal, 2); ?>">
                <button type="submit" class="placeorder-btn">Pay Amount</button>
            </div>
        </form>
    </div>

    <!-- footer section -->
    <?php include '../footer.php'; ?>
    <script src="../js/script.js"></script>
    <script>
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            var cardNumber = document.getElementById('card_number').value;
            var cvv = document.getElementById('cvv').value;

            if (!/^\d{16}$/.test(cardNumber)) {
                alert('Please enter a valid 16-digit card number.');
                e.preventDefault();
            } else if (!/^\d{3}$/.test(cvv)) {
                alert('Please enter a valid 3-digit CVV.');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
