<?php
session_start();
require_once '../db_connect.php';
$conn = OpenCon();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Grand Order List - MMU FOOD</title>
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

h2 {
    color: #333333;
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

.no-orders {
    text-align: center;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #dddddd;
    border-radius: 5px;
}

.no-orders p {
    margin: 0;
    color: #777777;
}

button {
    display: block;
    width: 200px;
    margin: 20px auto;
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

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

img {
    cursor: pointer;
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
            <h2>Grand Order List</h2>
            <?php
            // Query to get all orders
            //$sql = "SELECT Order_ID, Order_Status, Order_Date FROM food_order";

            $sql = "SELECT fo.Order_ID, fo.Order_Status, fo.Order_Date
                    FROM food_order AS fo
                    JOIN order_item AS oi ON fo.Order_ID = oi.Order_ID
                    JOIN food_item AS fi ON oi.FoodItem_ID = fi.FoodItem_ID
                    JOIN restaurant AS r ON fi.Registration_No = r.Registration_No
                    JOIN vendor AS v ON r.Vendor_ID = v.Vendor_ID
                    WHERE fo.Order_Status IN ('Accepted', 'Completed') 
                    AND v.Vendor_ID = '$vendor_id'"; //add seesion ID

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo '<tr>
                            <td><a href="vendor_orderdetails.php?id=' . $row["Order_ID"] . '">' . $row["Order_ID"] . '</a></td>
                            <td>' . $row["Order_Status"] . '</td>
                            <td>' . $row["Order_Date"] . '</td>
                            <td><button onclick="window.location.href=\'complete_order.php?id=' . $row["Order_ID"] . '\'">Completed</button></td>
                        </tr>';
                }
                echo '</tbody>
                    </table>';
            } else {
                echo '<div class="no-orders">
                        <p>No orders found</p>
                    </div>';
            }

            // Close the connection
            $conn->close();
            ?>
            <button onclick="window.location.href='vendor_home.php'">Pending Orders</button>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
