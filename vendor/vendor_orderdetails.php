<?php
session_start();
require_once '../db_connect.php';
$conn = OpenCon();

$order_id = $_GET['id'];

// Query to get order details
$order_sql = "SELECT Order_ID, Order_Date, Order_Status FROM food_order WHERE Order_ID='$order_id'";
$order_result = $conn->query($order_sql);
$order = $order_result->fetch_assoc();

// Query to get order items
$items_sql = "SELECT fi.FoodItem_Name, oi.OrderItem_Qty, oi.OrderItem_Price 
              FROM order_item oi
              JOIN food_item fi ON oi.FoodItem_ID = fi.FoodItem_ID
              WHERE oi.Order_ID='$order_id'";
$items_result = $conn->query($items_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Details - MMU FOOD</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            color: #333333;
        }

        p {
            font-size: 16px;
            color: #555555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        button {
            display: inline-block;
            width: 100px;
            margin: 20px 10px;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        button:hover {
            background-color: #0056b3;
        }

        th, td {
            text-align: center;
        }
    </style>
</head>
<body>
<?php include '../vendor/v_navbar.php'; ?>
    <div class="container">
        <main>
            <h2>Order ID: <?php echo $order['Order_ID']; ?></h2>
            <p>Date Placed: <?php echo $order['Order_Date']; ?></p>
            <p>Status: <?php echo $order['Order_Status']; ?></p>

            <h3>Order Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Food Item</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_quantity = 0;
                    $total_price = 0;

                    while($item = $items_result->fetch_assoc()) {
                        $total_quantity += $item['OrderItem_Qty'];
                        $total_price += $item['OrderItem_Price'];
                        echo '<tr>
                                <td>' . $item['FoodItem_Name'] . '</td>
                                <td>' . $item['OrderItem_Qty'] . '</td>
                                <td>' . number_format($item['OrderItem_Price'], 2) . '</td>
                            </tr>';
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td><?php echo $total_quantity; ?></td>
                        <td><?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </tbody>
            </table>
            <button onclick="updateOrderStatus('Accepted')">Accept</button>
            <button onclick="updateOrderStatus('Rejected')">Reject</button>
        </main>
    </div>

    <?php include '../footer.php'; ?>
    
    <script>
        function updateOrderStatus(status) {  
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_order_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //URL encodes data sent to webserver
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) { //when request is complete and successful
                    alert("Order status updated to " + status);
                    window.location.href = "vendor_grandlist.php";
                }
            };
            xhr.send("id=<?php echo $order_id; ?>&status=" + status);
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
