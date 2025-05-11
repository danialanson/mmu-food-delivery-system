<?php
include("db_connect.php");
$conn = OpenCon();

if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $vendorname = mysqli_real_escape_string($conn, $_POST['vendorname']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $shopname = mysqli_real_escape_string($conn, $_POST['shopname']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $phonenum = mysqli_real_escape_string($conn, $_POST['phonenum']);

    if ($password != $cpassword) {
        die("Passwords do not match.");
    }

    // Generate ID for Vendor
    $sql = "SELECT MAX(Vendor_ID) AS max_id FROM vendor";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];

    // Generate ID for Regis number
    $sql2 = "SELECT MAX(Registration_No) AS max_id2 FROM restaurant";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    $maxId2 = $row2['max_id2'];    

    // Increment and format the new vendor id
    if ($maxId) {
        $newId = substr($maxId, 3);
        $newId = (int)$newId + 1;  
        $newId = 'V' . sprintf('%04d', $newId);
    } else {
        // If no existing Vendors, start from V0001
        $newId = 'V0001';
    }

    // Increment and format the new registation id
    if ($maxId2) {
        $newRId = substr($maxId2, 3);
        $newRId = (int)$newRId + 1;  
        $newRId = 'A1-' . sprintf('%04d', $newRId);
    } else {
        // If no existing Registration Number, start from A1-0001
        $newRId = 'A1-0001';
    }

    // Insert into User table with default User_Type as 'Vendor'
    $userSql = "INSERT INTO User (Username, Password, User_Type) 
                VALUES ('$username', '$password', 'Vendor')";
    
    if(mysqli_query($conn, $userSql)) {
        $userId = mysqli_insert_id($conn);

        $vendorSql = "INSERT INTO Vendor (Vendor_ID, Username, Vendor_Name, Registration_Status) 
                        VALUES ('$newId', '$username','$vendorname', 'Pending')";

        // restaurant table insert
        if(mysqli_query($conn, $vendorSql)) {

            $regisSql = "INSERT INTO restaurant (Registration_No, Vendor_ID, Restaurant_Name,Restaurant_Category,Phone_No)   
                    VALUES ('$newRId', '$newId', '$shopname', '$category','$phonenum')";

            if(mysqli_query($conn, $regisSql)){
            // Redirect to login page after successful registration
                header('Location: login.php');
                exit(); 
            } else{
                die("Error inserting into Restaurant Table : " . mysqli_error($conn));
            }
        } else {
            die("Error inserting into Vendor table  : " . mysqli_error($conn));
        }
    } else {
        die("Error inserting into User table: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | MMU Food</title>
    <link rel="stylesheet" href="css/styledesign.css">
    <link rel="stylesheet" href="css/validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    
    <?php include 'header.php'; ?>

    <!-- main content -->
    <div id="registration-form">
        <form name="registrationForm" action="" method="POST" onsubmit="return validateForm()">
            <div class="form-row">
                <label for="username">Username:</label>
                <input type="text" name="username" placeholder="Enter your username" required>
                <div class="error-message" id="usernameError"></div> 
            </div>

            <div class="form-row">
                <label for="vendorname">Vendor Name:</label>
                <input type="text" name="vendorname" placeholder="Enter your vendor name" required>
                <div class="error-message" id="vendornameError"></div> 
            </div>
            
            <div class="form-row">
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <div class="error-message" id="passwordError"></div> 
            </div>

            <div class="form-row">
                <input type="password" name="cpassword" placeholder="Confirm your password" required>
                <div class="error-message" id="cpasswordError"></div> 
            </div>

            <div class="form-row">
                <label for="shopname">Shop Name:</label>
                <input type="text" name="shopname" placeholder="Enter your restaurant name" required>
                <div class="error-message" id="shopnameError"></div> 
            </div>

            <div class="form-row">
                <label for="category">Category:</label>
                <input type="text" name="category" placeholder="Enter your shop category" required>
                <div class="error-message" id="categoryError"></div> 
            </div>

            <div class="form-row">
                <label for="phonenum">Phone Number:</label>
                <input type="number" name="phonenum" placeholder="Enter your phone number" required>
                <div class="error-message" id="numberError"></div> 
            </div>
            <div class="form-row">
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </div>
            <p>Have an account?<a href="login.php"> Login Now</a></p><br>
        </form>
    </div>

    
    <?php include 'footer.php'; ?>
    
    <script src="js/script.js"></script>
    <script>
        function validateForm() {
        var username = document.forms["registrationForm"]["username"].value.trim();
        var vendorname = document.forms["registrationForm"]["vendorname"].value.trim();
        var password = document.forms["registrationForm"]["password"].value.trim();
        var cpassword = document.forms["registrationForm"]["cpassword"].value.trim();
        var shopname = document.forms["registrationForm"]["shopname"].value.trim();
        var category = document.forms["registrationForm"]["category"].value.trim();
        var phonenum = document.forms["registrationForm"]["phonenum"].value.trim();

        var usernamePattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,16}$/; // Username pattern added
        var vendornamePattern = /^[a-zA-Z0-9 ]{1,50}$/; //vendorname pattern
        var passwordPattern = /^(?=.*\d)(?=.*[a-zA-Z]).{8,16}$/; // Updated password pattern
        var shopnamePattern = /^[a-zA-Z0-9 ]{1,40}$/; //shopname pattern
        var categoryPattern = /^[a-zA-Z0-9 ]{1,20}$/; //categorypattern
        var phonenumPattern = /^\d{8,11}$/; //phonenumber pattern
        
        var isValid = true;

        // Clear previous error messages
        document.getElementById("usernameError").innerText = "";
        document.getElementById("vendornameError").innerText = "";
        document.getElementById("passwordError").innerText = "";
        document.getElementById("cpasswordError").innerText = "";
        document.getElementById("shopnameError").innerText = "";
        document.getElementById("categoryError").innerText = "";
        document.getElementById("numberError").innerText = "";

        // Username validation
        if (username === "") {
            document.getElementById("usernameError").innerText = "Username must be filled out.";
            isValid = false;
        } else if (!usernamePattern.test(username)) {
            document.getElementById("usernameError").innerText = "Username must contain at least one letter and one digit, 8-16 characters.";
            isValid = false;
        }

        // Vendorname validation
        if (vendorname === "") {
            document.getElementById("vendornameError").innerText = "vendor name must be filled out.";
            isValid = false;
        }else if (!vendornamePattern.test(vendorname)) {
            document.getElementById("vendornameError").innerText = "Invalid vendor name.";
            isValid = false;
        }  
        
        // Password validation
        if (password === "") {
            document.getElementById("passwordError").innerText = "Password must be filled out.";
            isValid = false;
        } else if (!passwordPattern.test(password)) {
            document.getElementById("passwordError").innerText = "Password must contain at least one letter, one digit, and be 8-16 characters long.";
            isValid = false;
        }

        // Confirm Password validation
        if (cpassword === "") {
            document.getElementById("cpasswordError").innerText = "Please confirm your password.";
            isValid = false;
        } else if (password !== cpassword) {
            document.getElementById("cpasswordError").innerText = "Passwords do not match.";
            isValid = false;
        }

        //Shopname validation
        if (shopname === "") {
            document.getElementById("shopnameError").innerText = "Shop name must be filled out.";
            isValid = false;
        }else if (!shopnamePattern.test(shopname)) {
            document.getElementById("shopnameError").innerText = "Invalid shop name.";
            isValid = false;
        }

        //Category validation
        if (category === "") {
            document.getElementById("categoryError").innerText = "The category must be filled out.";
            isValid = false;
        } else if (!categoryPattern.test(category)) {
            document.getElementById("categoryError").innerText = "Category must not be numbers or symbols.";
            isValid = false;
        }
        
        // Phonenumber validation
        if (phonenum === "") {
            document.getElementById("numberError").innerText = "Phone number cannot be empty.";
            isValid = false;
        } else if (!phonenumPattern.test(password)) {
            document.getElementById("numberError").innerText = "Invalid Phonenumber!.";
            isValid = false;
        }
    }
        return isValid;
    
    </script>
</body>
</html>
