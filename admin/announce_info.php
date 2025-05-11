<?php
session_start();

require_once '../db_connect.php';
$conn = OpenCon();

$id = "";
$title = "";
$image = "";
$content = "";
$date = "";

// Check if 'id' is set in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $announcementId = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM announcement WHERE Announcement_ID = '$announcementId'";
    $result = mysqli_query($conn, $sql);
    
    if($result) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['Announcement_ID'];
        $title = $row['Announcement_Subject'];
        $image = $row['Announcement_Image'];
        $content = $row['Content'];
        $date = date('d-m-Y', strtotime($row['Date_Posted']));
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    CloseCon($conn);
} else {
    echo "No help entry selected.";
}
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
        <input type='hidden' name='help_id' value='<?php echo $id; ?>'>
        <table class="formTable">
            <tr>
                <th colspan="3" class="newBtnHeader">
                <button id="disapproveBtn" type="button" onclick="if(confirm('Are you sure you want to delete this announcement?')) window.location.href='announce_delete.php?id=<?php echo $announcementId; ?>'">Delete</button>
                </th>
            </tr>
            <tr>
                <th>Date</th>
                <td><?php echo $date; ?></td>
            </tr>
            <tr>
                <th>Title</th>
                <td><?php echo $title; ?></td>
            </tr>
            <tr>
                <th>Content</th>
                <td><?php echo $content; ?></td>
            </tr>
            <tr>
                <th>Image</th>
                <td>
                    <img src="data:image/jpg;charset-utf;base64, <?php echo base64_encode($image); ?>"
                    alt="Image" style="max-width: 100%; height: auto;">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="button" onclick="window.location.href='announce_manage.php'">Back</button>
                </td>
            </tr>
        </table>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>