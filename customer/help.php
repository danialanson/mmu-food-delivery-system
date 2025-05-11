<?php
session_start();

if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}

include '../db_connect.php';

$conn = OpenCon();

$username = $_SESSION['mySession'];
$cus_id = $_SESSION['Cus_ID']; // Get Cus_ID from session
$admin_id = 'A000';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $uploadError = '';
    $filePath = '';

    // Generate Help_ID for Help
    $sql = "SELECT MAX(Help_ID) AS max_id FROM Help";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];

    // Increment and format the new Help_ID
    if ($maxId) {
        $newId = substr($maxId, 1);
        $newId = (int)$newId + 1;  
        $newId = 'H' . sprintf('%04d', $newId);
    } else {
        // If no existing help, start from H0001
        $newId = 'H0001';
    }

    if (isset($_FILES['upload']) && $_FILES['upload']['error'] != UPLOAD_ERR_NO_FILE) {
        $fileName = $_FILES['upload']['name'];
        $fileTmpName = $_FILES['upload']['tmp_name'];
        $fileSize = $_FILES['upload']['size'];
        $fileError = $_FILES['upload']['error'];
        $fileType = $_FILES['upload']['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');

        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) { // Limit file size to 5MB
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = '../uploads/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                    $filePath = $fileNameNew; // Set file path for database
                } else {
                    $uploadError = "Your file is too large.";
                }
            } else {
                $uploadError = "There was an error uploading your file.";
            }
        } else {
            $uploadError = "You cannot upload files of this type.";
        }
    }

    if (empty($uploadError)) {
        $query = "INSERT INTO help (Help_ID, Cus_ID, Admin_ID, Subject, Description, FilePath, Date_Submitted) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $newId, $cus_id, $admin_id, $subject, $description, $filePath);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ".$_SERVER['PHP_SELF']."?success=1");
            exit();
        } else {
            $errorMessage = "Error submitting your request: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $errorMessage = $uploadError;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Help | MMU Food</title>
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
            <h1>Help</h1>
            <div class="vertical-line">
                <div class="profile-info">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="subject">Please type your question or concerns title here:</label>
                            <input type="text" id="subject" name="subject" value="<?php echo isset($_GET['success']) ? '' : htmlspecialchars($subject ?? ''); ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="description">Description:</label>
                            <textarea style="border: 1px solid black" id="description" name="description" rows="6" required><?php echo isset($_GET['success']) ? '' : htmlspecialchars($description ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <label for="upload">Upload Image or Document:</label>
                            <input type="file" id="upload" name="upload">
                        </div>
                        <div class="form-row">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='customerhome.php'">Cancel</button>
                        </div>
                        <?php if (isset($_GET['success'])) : ?>
                            <div class="success-message">Your help request has been submitted successfully.</div>
                        <?php elseif (isset($errorMessage)) : ?>
                            <div class="error-message"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>
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
