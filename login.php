<?php
    session_start();

    include("db_connect.php");
    $conn = OpenCon();

    if (isset($_POST['login'])) {
        // Validate and sanitize inputs
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);
        
        $validUserTypes = ['Customer', 'Vendor', 'Admin'];
        if (!in_array($usertype, $validUserTypes)) {
            $login_error = "Invalid user type selected.";
            $_SESSION['login_error'] = $login_error;
            header('location: login.php'); 
            exit();
        }

        $sql = "SELECT u.*, c.Cus_ID , a.Admin_ID, v.Vendor_ID
                FROM User u
                LEFT JOIN Customer c ON u.Username = c.Username
                LEFT JOIN Admin a ON u.Username = a.Username
                LEFT JOIN Vendor v ON u.Username = v.Username
                WHERE u.Username ='$username' 
                AND u.Password='$password' 
                AND u.User_Type='$usertype'";
        
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Query Failed: " . mysqli_error($conn));
        }

        $rowcount = mysqli_num_rows($result);

        if ($rowcount == 1) {
            $row = mysqli_fetch_array($result);

            // Set session variables
            $_SESSION["mySession"] = $row['Username'];
            $_SESSION["userRole"] = $row['User_Type'];
            $_SESSION["Cus_ID"] = $row['Cus_ID'];
            $_SESSION["Admin_ID"] = $row['Admin_ID'];
            $_SESSION["Vendor_ID"] = $row['Vendor_ID'];

            // Redirect based on user role
            if ($_SESSION["userRole"] === 'Customer') {
                header('location: customer/customerhome.php');
                exit();
            } elseif ($_SESSION["userRole"] === 'Vendor') {
                header('location: vendor/vendor_home.php');
                exit();
            } elseif ($_SESSION["userRole"] === 'Admin') {
                header('location: admin/adminhome.php');
                exit();
            }
        } else {
            $login_error = "Invalid user type, username or password";
        }

        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | MMU Food</title>
    <link rel="stylesheet" href="css/styledesign.css">
    <link rel="stylesheet" href="css/validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- header section -->
    <?php include 'header.php'; ?>

    <!-- main content -->
    <div id="login-form">
        <form method="post" action="">
            <div class="form-row">
                <label for="usertype">User Type:</label>
                <select name="usertype" id="usertype" required>
                    <option value="Customer">Customer</option>
                    <option value="Vendor">Vendor</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div class="form-row">
                <label for="username">Username:</label>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-row">
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <?php
                if (isset($login_error)) {
                    echo '<div class="error-message">' . $login_error . '</div>';
                }
            ?>
            <div class="form-row">
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </div>
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
        </form>
    </div>
    
    <!-- footer section -->
    <?php include 'footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>
