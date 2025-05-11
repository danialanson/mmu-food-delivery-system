<?php
session_start();
require_once '../db_connect.php';
$conn = OpenCon();

if (isset($_POST['submit'])) {
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $vendorID = $_SESSION['Vendor_ID']; // Ensure that the session is started and Vendor_ID is set

    // Fetch Registration Number
    $regNoQuery = "SELECT Registration_No FROM restaurant WHERE Vendor_ID='$vendorID'";
    $regNoResult = $conn->query($regNoQuery);
    $registrationNo = '';

    if ($regNoResult->num_rows > 0) {
        $row = $regNoResult->fetch_assoc();
        $registrationNo = $row['Registration_No'];
    } else {
        echo "Error: Vendor not found.";
        exit();
    }

    // Handle image upload
    $uploadOk = 1;
    $imgContent = '';
    if(isset($_FILES["upload"]) && $_FILES["upload"]["error"] == 0){
        $fileName = basename($_FILES['upload']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileType = strtolower($fileExt);
        $allowTypes = array('jpg', 'jpeg', 'png');

        if(in_array($fileType, $allowTypes)){
            $image = $_FILES['upload']['tmp_name'];
            $imgContent = addslashes(file_get_contents($image));
        } else {
            echo "Only JPG, JPEG, PNG are allowed.";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 1) {
        // Insert data into the database
        // Generate ID for Fooditem number
        $sql = "SELECT MAX(FoodItem_ID) AS max_id FROM food_item";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $maxId = $row['max_id'];    

        // Increment and format the new vendor id
        if ($maxId) {
            $newId = substr($maxId, 3);
            $newId = (int)$newId + 1;  
            $newId = 'FI' . sprintf('%04d', $newId);
        } else {
            // If no existing Food ID, start from V0001
            $newId = 'FI0001';
        }
        
        $sql = "INSERT INTO food_item (FoodItem_ID, Registration_No, FoodItem_Name, FoodItem_Description, FoodItem_Price, FoodItem_Image, Is_Available) 
                VALUES ('$newId', '$registrationNo', '$itemName', '$description', '$price', '$imgContent', 'Y')";
        if ($conn->query($sql) === TRUE) {
            echo "The item has been added successfully.";
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
    <title>Add Item - MMU FOOD</title>
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

        button {
            width: 150px;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            align-self: center;
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
            <h2>Add Item</h2>
            <form action="vendor_itemadd.php" method="post" enctype="multipart/form-data">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <label for="price">Price</label>
                <input type="text" id="price" name="price" required>

                <label for="photo">Photo</label>
                <input type="file" id="upload" name="upload">

                <button type="submit" name="submit">Submit</button>
            </form>
        </main>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
