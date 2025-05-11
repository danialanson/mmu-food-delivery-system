<?php
session_start();

include("db_connect.php");
$conn = OpenCon();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);

    $sql = "SELECT * FROM User WHERE Username ='$username' AND Password='$password' AND User_Type='$usertype'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    $rowcount = mysqli_num_rows($result);

    if ($rowcount == 1) {
        $row = mysqli_fetch_array($result);

        $_SESSION["mySession"] = $row['Username'];
        $_SESSION["userRole"] = $row['User_Type'];

        // Redirect based on user role
        if ($_SESSION["userRole"] === 'Customer') {
            header('location: customerhome.php');
            exit();
        } elseif ($_SESSION["userRole"] === 'Vendor') {
            header('location: vendor/vendor_home.php');
            exit();
        } elseif ($_SESSION["userRole"] === 'Admin') {
        header('location: adminhome.php');
        exit();
        }
    } else {
        echo "<script>alert('Incorrect username / password');
              window.location.href='login.php'</script>";
    }
}

mysqli_close($conn);
?>
