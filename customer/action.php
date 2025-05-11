<?php
require '../config.php';

$action = $_POST['action']; 
$search = $_POST['search']; 
$categories = isset($_POST['restaurant']) ? $_POST['restaurant'] : []; 
$minimum_price = isset($_POST['minimum_price']) ? $_POST['minimum_price'] : 0; 
$maximum_price = isset($_POST['maximum_price']) ? $_POST['maximum_price'] : 100; 

if ($action == 'fetch') {
    $sql = "SELECT f.*, r.Restaurant_Name 
            FROM Food_Item f 
            JOIN Restaurant r ON f.Registration_No = r.Registration_No
            WHERE 1=1";

    if (!empty($search)) {
        $sql .= " AND (f.FoodItem_Name LIKE '%$search%' 
                      OR r.Restaurant_Name LIKE '%$search%' 
                      OR r.Restaurant_Category LIKE '%$search%')";
    }

    // Add category filter condition if categories are selected
    if (!empty($categories)) {
        $categoryFilter = implode("','", $categories);
        $sql .= " AND r.Restaurant_Category IN ('$categoryFilter')";
    }

    // Add price range filter conditions only if minimum_price or maximum_price are set
    if ($minimum_price != 0 || $maximum_price != 100) {
        $sql .= " AND f.FoodItem_Price BETWEEN $minimum_price AND $maximum_price";
    }

    $result = $conn->query($sql);
    $foodItems = [];

    // Loop through the query results and populate $foodItems array
    while ($row = $result->fetch_assoc()) {
        $foodItems[] = [
            'id' => $row['FoodItem_ID'],
            'name' => $row['FoodItem_Name'],
            'description' => $row['FoodItem_Description'],
            'price' => $row['FoodItem_Price'],
            'available' => $row['Is_Available'] == 'Y',
            'restaurant' => $row['Restaurant_Name'],
            'image' => base64_encode($row['FoodItem_Image'])
        ];
    }

    echo json_encode($foodItems);
}

$conn->close(); 
?>
