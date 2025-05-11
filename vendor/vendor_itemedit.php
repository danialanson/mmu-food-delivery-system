<?php
session_start();
require_once '../db_connect.php';
$conn = OpenCon();

if (!isset($_GET['id'])) {
    echo "No item ID provided.";
    exit();
}

$itemID = $_GET['id'];
$vendorID = $_SESSION['Vendor_ID']; // Ensure that the session is started and Vendor_ID is set

// Fetch item details
$itemQuery = "SELECT * FROM food_item WHERE FoodItem_ID='$itemID' AND Registration_No IN (SELECT Registration_No FROM restaurant WHERE Vendor_ID='$vendorID')";
$itemResult = $conn->query($itemQuery);
$item = $itemResult->fetch_assoc();

if (!$item) {
    echo "Item not found or you don't have permission to edit this item.";
    exit();
}

if (isset($_POST['submit'])) {
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle image upload
    $uploadOk = 1;
    $imgContent = $item['FoodItem_Image']; // Default to current image
    if (isset($_FILES["upload"]) && $_FILES["upload"]["error"] == 0) {
        $fileName = basename($_FILES['upload']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileType = strtolower($fileExt);
        $allowTypes = array('jpg', 'jpeg', 'png');

        if (in_array($fileType, $allowTypes)) {
            $image = $_FILES['upload']['tmp_name'];
            $imgContent = addslashes(file_get_contents($image));
        } else {
            echo "Only JPG, JPEG, PNG are allowed.";
            $uploadOk = 0;
        }
    } else {
        // Retrieve the current image if a new one is not uploaded
        $imgContent = addslashes($imgContent);
    }

    if ($uploadOk == 1) {
        // Update the data in the database
        $sql = "UPDATE food_item 
                SET FoodItem_Name='$itemName', FoodItem_Description='$description', FoodItem_Price='$price', FoodItem_Image='$imgContent'
                WHERE FoodItem_ID='$itemID'";
        if ($conn->query($sql) === TRUE) {
            echo "The item has been updated successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Item - MMU FOOD</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            overflow-y: auto;
            max-height: 80vh; /* Ensure the container does not exceed 80% of the viewport height */
        }

        h2 {
            color: #333333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            color: #555555;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            padding: 10px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        img {
            max-width: 100%; 
            margin-top: 10px;
            max-height: 300px; 
            display: block;
            margin-bottom: 20px; 
        }

        button {
            width: 150px;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            align-self: center;
            margin-top: 20px; 
        }

        button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <?php include '../vendor/v_navbar.php'; ?>      
    <div class="container">
        <main>
            <h2>Edit Item</h2>
            <form action="vendor_itemedit.php?id=<?php echo $itemID; ?>" method="post" enctype="multipart/form-data">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" value="<?php echo htmlspecialchars($item['FoodItem_Name']); ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($item['FoodItem_Description']); ?></textarea>

                <label for="price">Price</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($item['FoodItem_Price']); ?>" required>

                <label for="photo">Photo</label>
                <input type="file" id="upload" name="upload">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['FoodItem_Image']); ?>" alt="Current Image">

                <button type="submit" name="submit">Submit</button>
            </form>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
