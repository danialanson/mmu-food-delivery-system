<?php
    session_start();
    require '../config.php';
    include '../db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Catalogue | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/slideshow.css">
    <link rel="stylesheet" href="../css/foodcatalogue.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- main content -->
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">
                    <h6 class="text-info">Select Restaurant Category</h6>
                    <ul class="list-group">
                        <?php
                            $sql = "SELECT DISTINCT restaurant_category FROM restaurant ORDER BY restaurant_category";
                            $result = $conn->query($sql);
                            while($row = $result->fetch_assoc()){
                        ?>
                        <li class="list-group-item">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input restaurant_check" 
                                    value="<?= htmlspecialchars($row['restaurant_category']); ?>" id="restaurant_category"><?= htmlspecialchars($row['restaurant_category']); ?>
                                </label>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>

                    <h6 class="text-info">Price</h6>
                    <input type="range" class="form-range" id="priceRange" min="0" max="100" value="0" step="1">
                    <br><br>
                    <span id="priceValue">RM0 - RM100</span>
                    <br>
                </div>
                <div class="col-lg-9">
                    <div class="heading-search-container">
                        <h5 class="text-center" id="textChange">All Food</h5>
                        <div class="search-bar-container">
                            <input type="text" id="search" placeholder="Search for food or restaurant...">
                            <button class="btn btn-dark" id="searchButton">Search</button>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="result">
                        <?php 
                            $sql = "SELECT f.*, r.Restaurant_Name FROM Food_Item f 
                                    JOIN Restaurant r ON f.Registration_No = r.Registration_No";
                            $result = $conn->query($sql);
                            
                            while ($row = $result->fetch_assoc()) {
                                // Check if item is available
                                $availabilityFood = ($row['Is_Available'] == 'N') ? 'out-of-stock' : '';
                                // Convert BLOB data to base64 format
                                $imageData = base64_encode($row['FoodItem_Image']);
                                $imageSrc = 'data:image/jpg;base64,' . $imageData;
                        ?>
                        <div class="col-md-3 mb-2">
                            <div class="card border-secondary food-item-card <?= $availabilityFood ?>">
                                <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= htmlspecialchars($row['FoodItem_Name']); ?>">                                
                                <div class="card-body">
                                    <h6 class="text-light bg-info"><?= htmlspecialchars($row['FoodItem_Name']); ?></h6>
                                    <p class="card-text">
                                        <br>Restaurant Name: <?= htmlspecialchars($row['Restaurant_Name']); ?><br>
                                        <br>Description: <?= htmlspecialchars($row['FoodItem_Description']); ?><br>
                                    </p>
                                    <h3 class="card-title text-danger"><br>Price: RM <?= htmlspecialchars($row['FoodItem_Price']); ?></h3><br>
                                    <?php if ($row['Is_Available'] == 'N') { ?>
                                        <span class="out-of-stock-label">Out of Stock</span>
                                        <button class="btn btn-success btn-block addToCart" data-food-id="<?= $row['FoodItem_ID']; ?>" disabled>Add to Cart</button>
                                    <?php } else { ?>
                                        <a href="#" class="btn btn-success btn-block addToCart" data-food-id="<?= $row['FoodItem_ID']; ?>">Add to Cart</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- footer section -->
    <?php include '../footer.php'; ?>
    <script src="../js/script.js"></script>


    <!-- script section -->
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function(){
        var priceFilterApplied = false;
        var currentRestaurantId = null; 

        // Function to fetch food items
        function fetchFoodItems() {
            var search = $("#search").val().trim();
            var categories = getSelectedRestaurantCategories();
            var minimum_price = 0; 
            var maximum_price = priceFilterApplied ? $("#priceRange").val() : 100;
            var action = 'fetch';

            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: { 
                    action: action, 
                    search: search, 
                    restaurant: categories, 
                    minimum_price: minimum_price, 
                    maximum_price: maximum_price 
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    updateFoodItems(data);
                    $("#textChange").text(search ? "Search Results" : "All Food");
                    console.log("Minimum Price:", minimum_price);
                    console.log("Maximum Price:", maximum_price);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        // Function to update food items on the page
        function updateFoodItems(data) {
            var resultContainer = $('#result');
            resultContainer.empty();

            if (data.length === 0) {
                resultContainer.append('<h2>No items found.</h2>');
                return;
            }

            data.forEach(function(item) {
                var availabilityFood = item.available ? '' : 'out-of-stock';
                var imageSrc = 'data:image/jpg;base64,' + item.image;

                var html = '<div class="col-md-3 mb-2">' +
                                '<div class="card border-secondary food-item-card ' + availabilityFood + '" data-restaurant-id="' + item.registration_no + '">' +
                                    '<img src="' + imageSrc + '" class="card-img-top" alt="' + item.name + '">' +
                                    '<div class="card-body">' +
                                        '<h6 class="text-light bg-info">' + item.name + '</h6>' +
                                        '<p class="card-text">' +
                                            '<br>Restaurant Name: ' + item.restaurant + '<br>' +
                                            '<br>Description: ' + item.description + '<br>' +
                                        '</p><br>' +
                                        '<h3 class="card-title text-danger">Price: RM ' + item.price + '</h3><br>';

                if (!item.available) {
                    html += '<span class="out-of-stock-label">Out of Stock</span>';
                }

                html += '<a href="#" class="btn btn-success btn-block addToCart" data-food-id="' + item.id + '">Add to Cart</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

                resultContainer.append(html);
            });
        }

        // Function to get selected restaurant categories
        function getSelectedRestaurantCategories() {
            var categories = [];
            $('.restaurant_check:checked').each(function() {
                categories.push($(this).val());
            });
            return categories;
        }

        // Event handlers
        $("#searchButton").click(fetchFoodItems);

        $("#search").keyup(function(event) {
            if (event.keyCode === 13) {
                fetchFoodItems();
            }
        });

        $(".restaurant_check").click(fetchFoodItems);

        $("#priceRange").on('input', function() {
            $("#priceValue").text("RM0 - RM" + $(this).val());
            priceFilterApplied = true;
            fetchFoodItems();
        });

        $("#priceRange").on('mousedown', function() {
            priceFilterApplied = false;
        });

        // Event handler for Add to Cart button
        $(document).on('click', '.addToCart', function(e) {
            e.preventDefault();

            var foodItemId = $(this).data('food-id');
            var quantity = 1; // Example quantity

            // Check if the button is disabled
            if ($(this).prop('disabled')) {
                console.log('This item is out of stock and cannot be added to cart.');
                return; 
            }

            $.ajax({
                    type: 'POST',
                    url: 'add_to_cart.php',
                    data: {
                        FoodItem_ID: foodItemId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.includes('success')) {
                            alert('Item has been added into cart successfully.');
                        } else {
                            alert('Item is not available.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
            
        fetchFoodItems(); // Initial fetch
    });
    </script>
</body>
</html>
