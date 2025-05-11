<?php
session_start();

if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}

include '../db_connect.php';

$conn = OpenCon();

// Fetch announcements for the slider and table
$sql = "SELECT Announcement_Subject, Content, Date_Posted, Announcement_Image FROM announcement ORDER BY Date_Posted DESC";
$result = mysqli_query($conn, $sql);

$announcements = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $announcements[] = $row;
    }
} else {
    die("Query Failed: " . mysqli_error($conn));
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Announcements | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/slideshow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .announcement-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .announcement-table th, .announcement-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .announcement-table th {
            background-color: #f2f2f2;
            font-size: 1.4em;
        }
        .announcement-table td {
            vertical-align: top;
        }
        .announcement-table thead {
            background-color: #f8f8f8;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- image slider section -->
    <section class="hero">
        <div class="hero-slider slideshow-container">
            <?php foreach ($announcements as $index => $announcement): ?>
                <div class="slide mySlides fade">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($announcement['Announcement_Image']); ?>" style="width:100%">
                    <div class="text"><?php echo htmlspecialchars($announcement['Announcement_Subject']); ?></div>
                </div>
            <?php endforeach; ?>
            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>

        <br>
        <div style="text-align:center">
            <?php for ($i = 0; $i < count($announcements); $i++): ?>
                <span class="dot" onclick="currentSlide(<?php echo $i+1; ?>)"></span>
            <?php endfor; ?>
        </div>
    </section>

    <!-- announcements section -->
    <section class="announcements">
        <div class="container">
            <h1>Announcements</h1>
            <table class="announcement-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Content</th>
                        <th>Date Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($announcement['Announcement_Subject']); ?></td>
                            <td><?php echo htmlspecialchars($announcement['Content']); ?></td>
                            <td><?php echo date("d M Y", strtotime($announcement['Date_Posted'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- footer section -->
    <?php include '../footer.php'; ?>

    <script src="../js/script.js"></script>
    <script src="../js/slideshow.js"></script>
</body>
</html>