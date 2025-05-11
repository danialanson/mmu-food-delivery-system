<?php
session_start();

if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}

include '../db_connect.php';

$conn = OpenCon();

$username = $_SESSION['mySession'];

$sql = "SELECT * FROM Customer WHERE Username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 1) {
    // Fetch customer details
    $row = mysqli_fetch_assoc($result);
    $first_name = $row['First_Name'];
    $last_name = $row['Last_Name'];
    $address = $row['Address'];
    $phone_number = $row['Phone_No'];
} else {
    echo "Customer details not found.";
    exit();
}

// Initialize field error messages
$fieldErrors = [
    'first_name' => '',
    'last_name' => '',
    'phone_number' => '',
];

// Validation patterns
$namePattern = "/^[a-zA-Z\s]+$/";
$phonePattern = "/^\d{3}-\d{7}$/";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';

    // Validation checks
    if (!preg_match($namePattern, $first_name)) {
        $fieldErrors['first_name'] = "First Name should only contain letters and spaces.";
    }

    if (!preg_match($namePattern, $last_name)) {
        $fieldErrors['last_name'] = "Last Name should only contain letters and spaces.";
    }

    if (!preg_match($phonePattern, $phone_number)) {
        $fieldErrors['phone_number'] = "Phone Number should be 11 to 13 numeric digits with optional hyphens.";
    }

    if (array_filter($fieldErrors)) {
        // Display errors below each input field
    } else {
        // Proceed with updating the database
        $query = "UPDATE Customer SET First_Name = ?, Last_Name = ?, Address = ?, Phone_No = ? WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $address, $phone_number, $username);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['update_success'] = true;
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: userprofile.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Details | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- main content -->
    <section class="customer-detail">
        <div class="customer">
            <h1>Account Details</h1>
            <div class="vertical-line">
                <div class="profile-info">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-row">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                            <span class="error"><?php echo $fieldErrors['first_name']; ?></span>
                            <br>
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                            <span class="error"><?php echo $fieldErrors['last_name']; ?></span>
                            <br>
                        </div>
                        <div class="form-row">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                        </div>
                        <br>
                        <div class="form-row">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                            <span class="error"><?php echo $fieldErrors['phone_number']; ?></span>
                        </div>
                        <br>
                        <div class="form-row">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <br>
                            <?php if (isset($_SESSION['update_success']) && $_SESSION['update_success']) : ?>
                                <div class="success-message">You have updated successfully.</div>
                                <?php unset($_SESSION['update_success']); ?> 
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- footer section -->
    <?php include '../footer.php'; ?>

    <script src="../js/script.js"></script>
    <script src="../js/slideshow.js"></script>
</body>
</html>

