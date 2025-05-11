<?php
session_start();
include '../db_connect.php';

$conn = OpenCon();

$order_id = $_GET['order_id'] ?? '';

if (!$order_id) {
    die("Order ID is not set.");
}

// Fetch order details
$sql = "SELECT f.Order_ID, f.Order_Address, f.Total_Amount, p.Card_Number 
        FROM food_order f 
        JOIN payment p ON f.Payment_ID = p.Payment_ID 
        WHERE f.Order_ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

// Fetch order items
$sql = "SELECT oi.FoodItem_ID, oi.OrderItem_Qty, oi.OrderItem_Price, f.FoodItem_Name 
        FROM order_item oi 
        JOIN food_item f ON oi.FoodItem_ID = f.FoodItem_ID 
        WHERE oi.Order_ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

$masked_card_number = 'XXXX-XXXX-XXXX-' . substr($order['Card_Number'], -4);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | MMU Food</title>
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
        <h2>Order Success</h2>
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
                        $totalItems = 0;
                        foreach ($order_items as $item) {
                            echo "<tr>";
                            echo "<td class='text-center'>" . ++$totalItems . "</td>";
                            echo "<td class='food-name'>" . htmlspecialchars($item['FoodItem_Name']) . "</td>";
                            echo "<td class='food-price'>RM " . number_format($item['OrderItem_Price'], 2) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($item['OrderItem_Qty']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="order-summary">
                <h3>Order Summary</h3>
                <p>Order ID: <?php echo htmlspecialchars($order['Order_ID']); ?></p>
                <p>Delivery Address: <?php echo htmlspecialchars($order['Order_Address']); ?></p>
                <p>Total Amount: RM <?php echo number_format($order['Total_Amount'], 2); ?></p>
                <p>Paid using: <?php echo $masked_card_number; ?></p>
            </div>
        </div>
    </div>

    <!-- footer section -->
    <?php include '../footer.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
