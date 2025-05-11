<?php
session_start();
require_once '../db_connect.php';
$conn = OpenCon();

// Delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $food_item_id = $_POST['delete_id'];

    // Delete food item
    $delete_sql = "DELETE FROM food_item WHERE FoodItem_ID='$food_item_id'";
    if ($conn->query($delete_sql) === TRUE) {
        $delete_message = "Food item deleted successfully";
    } else {
        $delete_message = "Error deleting food item: " . $conn->error;
    }
}

// Get food items but wrong logic
$items_sql = "SELECT * FROM food_item";
$items_result = $conn->query($items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant Management - MMU FOOD</title>
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

        .item-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 10px;
        }

        .item-container img {
            width: 50px;
            height: 50px;
            margin-right: 20px;
        }

        .item-container h3 {
            margin: 0;
            color: #555555;
            flex: 1;
        }

        .item-container p {
            margin: 0;
            color: #999999;
        }

        .item-container select {
            margin-right: 20px;
        }

        .item-container button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-left: 10px;
        }

        .item-container button:hover {
            background-color: #0056b3;
        }

        .add-item-button {
            display: block;
            margin: 20px 0;
            padding: 10px;
            background-color: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .add-item-button:hover {
            background-color: #218838;
        }

        .delete-button {
            background-color: #FF0000;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .delete-form {
            display: inline;
        }
    </style>
</head>
<body>

<?php include '../vendor/v_navbar.php'; ?>
    <div class="container">
        <main>
            <h2>Restaurant Management</h2>
            <?php if (isset($delete_message)) { echo '<p>' . $delete_message . '</p>'; } ?>
            <button class="add-item-button" onclick="window.location.href='vendor_itemadd.php'">Add Food Item</button>
            <?php
            if ($items_result->num_rows > 0) {
                while($item = $items_result->fetch_assoc()) {
                    echo '<div class="item-container">
                            <form class="delete-form" method="POST" action="">
                                <input type="hidden" name="delete_id" value="' . $item["FoodItem_ID"] . '">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                            <img src="data:image/jpg;charset-utf;base64,' . base64_encode($item["FoodItem_Image"]) . '" alt="Food Image"> 
                            <div>
                                <h3>' . $item["FoodItem_Name"] . '</h3>
                                <p>RM' . number_format($item["FoodItem_Price"], 2) . '</p>
                            </div>
                            <select>
                                <option value="available"' . ($item["Is_Available"] == 1 ? ' selected' : '') . '>Available</option>
                                <option value="unavailable"' . ($item["Is_Available"] == 0 ? ' selected' : '') . '>Unavailable</option>
                            </select>
                            <button onclick="window.location.href=\'vendor_itemedit.php?id=' . $item["FoodItem_ID"] . '\'">Edit</button>
                        </div>';
                }
            } else {
                echo '<div class="no-items">
                        <p>No food items found</p>
                    </div>';
            }

            // Close the connection
            $conn->close();
            ?>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
