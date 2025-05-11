<?php
session_start();

if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}

include '../db_connect.php';

$conn = OpenCon();

$cus_id = $_SESSION['Cus_ID']; // Get Cus_ID from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $service_rating = isset($_POST['service_rating']) ? (int)$_POST['service_rating'] : 0;
    $recommendation_rating = isset($_POST['recommendation_rating']) ? (int)$_POST['recommendation_rating'] : 0;
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

    // Generate Feedback_ID for Feedback
    $sql = "SELECT MAX(Feedback_ID) AS max_id FROM feedback";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];

    // Increment and format the new Feedback_ID
    if ($maxId) {
        $newId = substr($maxId, 2);  // Extract the numerical part starting from the third character
        $newId = (int)$newId + 1;  
        $newId = 'FB' . sprintf('%04d', $newId);
    } else {
        // If no existing feedbacks, start from FB0001
        $newId = 'FB0001';
    }

    $query = "INSERT INTO feedback (Feedback_ID, Cus_ID, Service, Recommendation, Comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssiis", $newId, $cus_id, $service_rating, $recommendation_rating, $comments);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ".$_SERVER['PHP_SELF']."?success=1");
        exit();
    } else {
        $errorMessage = "Error submitting your feedback: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback | MMU Food</title>
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
            <h1>Feedback</h1>
            <div class="vertical-line">
                <div class="profile-info">
                    <form id="feedbackForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-row">
                            <label for="title">Please type your Feedback title here:</label>
                            <input type="text" id="title" name="title" value="<?php echo isset($_GET['success']) ? '' : htmlspecialchars($title ?? ''); ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="service_rating">Service Rating (1-10):</label>
                            <div style="display: flex" class="rating-options">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <label style="width: 50px">
                                        <input type="radio" name="service_rating" value="<?php echo $i; ?>" <?php echo (isset($_GET['success']) || ($service_rating ?? 5) == $i) ? '' : 'checked'; ?> required>
                                        <?php echo $i; ?>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="recommendation_rating">Recommendation Rating (1-10):</label>
                            <div style="display: flex" class="rating-options">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <label style="width: 50px">
                                        <input type="radio" name="recommendation_rating" value="<?php echo $i; ?>" <?php echo (isset($_GET['success']) || ($recommendation_rating ?? 5) == $i) ? '' : 'checked'; ?> required>
                                        <?php echo $i; ?>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="comments">Any Comments/Feedback:</label>
                            <textarea id="comments" name="comments" rows="6" required><?php echo isset($_GET['success']) ? '' : htmlspecialchars($comments ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='customerhome.php'">Cancel</button>
                        </div>
                        <?php if (isset($_GET['success'])) : ?>
                            <div class="success-message">Your feedback has been submitted successfully.</div>
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
</body>
</html>
