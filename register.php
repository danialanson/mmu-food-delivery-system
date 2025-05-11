<?php
include("db_connect.php");
$conn = OpenCon();

if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if ($password != $cpassword) {
        die("Passwords do not match.");
    }

    // Generate Cus_ID for Customer
    $sql = "SELECT MAX(Cus_ID) AS max_id FROM Customer";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];

    // Increment and format the new Cus_ID
    if ($maxId) {
        $newId = substr($maxId, 3);
        $newId = (int)$newId + 1;  
        $newId = 'MMU' . sprintf('%04d', $newId);
    } else {
        // If no existing customers, start from MMU0001
        $newId = 'MMU0001';
    }

    // Insert into User table with default User_Type as 'Customer'
    $userSql = "INSERT INTO User (Username, Password, User_Type) 
                VALUES ('$username', '$password', 'Customer')";
    
    if(mysqli_query($conn, $userSql)) {
        $userId = mysqli_insert_id($conn);

        $customerSql = "INSERT INTO Customer (Cus_ID, Username, EmailAddress) 
                        VALUES ('$newId', '$username', '$email')";
        
        if(mysqli_query($conn, $customerSql)) {
            // Redirect to login page after successful registration
            header('Location: login.php');
            exit();
        } else {
            die("Error inserting into Customer table: " . mysqli_error($conn));
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
    <!-- header section -->
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
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <div class="error-message" id="passwordError"></div> 
            </div>
            <div class="form-row">
                <input type="password" name="cpassword" placeholder="Confirm your password" required>
                <div class="error-message" id="cpasswordError"></div> 
            </div>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter your email address" required>
                <div class="error-message" id="emailError"></div> 
            </div>
            <div class="form-row">
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </div>
            <p>Have an account?<a href="login.php"> Login Now</a></p><br>
            <p>Register as a vendor?<a href="vendor_registration.php"> Register As A Vendor Now</a></p>
        </form>
    </div>

    <!-- footer section -->
    <?php include 'footer.php'; ?>
    
    <script src="js/script.js"></script>
    <script>
        function validateForm() {
        var username = document.forms["registrationForm"]["username"].value.trim();
        var email = document.forms["registrationForm"]["email"].value.trim();
        var password = document.forms["registrationForm"]["password"].value.trim();
        var cpassword = document.forms["registrationForm"]["cpassword"].value.trim();
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var passwordPattern = /^(?=.*\d)(?=.*[a-zA-Z]).{8,16}$/; // Updated password pattern
        var usernamePattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,16}$/; // Username pattern added
        var isValid = true;

        // Clear previous error messages
        document.getElementById("usernameError").innerText = "";
        document.getElementById("emailError").innerText = "";
        document.getElementById("passwordError").innerText = "";
        document.getElementById("cpasswordError").innerText = "";

        // Username validation
        if (username === "") {
            document.getElementById("usernameError").innerText = "Username must be filled out.";
            isValid = false;
        } else if (!usernamePattern.test(username)) {
            document.getElementById("usernameError").innerText = "Username must contain at least one letter and one digit, 8-16 characters.";
            isValid = false;
        }

        // Email validation
        if (!emailPattern.test(email)) {
            document.getElementById("emailError").innerText = "Invalid email address.";
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

        return isValid;
    }
    </script>
</body>
</html>
