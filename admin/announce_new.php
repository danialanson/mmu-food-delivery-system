<?php
session_start();
$adminId = $_SESSION['Admin_ID'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Information</title>
    <?php include 'navbar.php'; ?>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="table-container">
        <form action="announce_insert.php" method="POST" enctype="multipart/form-data">
            <input type='hidden' name='admin_id' value='<?php echo $adminId; ?>'>
            <table class="formTable">
                <tr>
                    <th colspan="2">New Announcement</th>
                </tr>
                <tr>
                    <th>Subject</th>
                    <td><textarea id="subject" name="subject" placeholder="Announcement Subject" required></textarea></td>
                </tr>
                <tr>
                    <th>Content</th>
                    <td><textarea id="content" name="content" placeholder="Announcement Content" required></textarea></td>
                </tr>
                <tr>
                    <th>Image (Optional)</th>
                    <td>
                        <input type="file" name="image"  id="fileInput" required>
                        <button id="disapproveBtn" type="button" onclick="document.getElementById('fileInput').value = '';">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button id="approveBtn" type="submit">Submit Announcement</button>
                        <button type="button" onclick="window.location.href='announce_manage.php'">Back</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>