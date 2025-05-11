<?php
session_start();

require_once '../db_connect.php';
$conn = OpenCon();

$sql = "SELECT * FROM announcement WHERE Admin_Id = '" . $_SESSION['Admin_ID'] . "'";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Announcement Management</title>
    <?php include 'navbar.php'; ?>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="table-container">
        <table class="listTable">
            <tr>
                <th colspan="3" class="newBtnHeader">
                    <button id="approveBtn" onclick="window.location.href='announce_new.php'">New Announcement</button>
                </th>
            </tr>
            <tr>
                <th>Title</th>
                <th>Date Added</th>
            </tr>
            <?php
                if($result = mysqli_query($conn, $sql)){
                    while($row = mysqli_fetch_assoc($result)){
                        $id=$row['Announcement_ID'];
                        $title=$row['Announcement_Subject'];
                        $date=date('d-m-Y', strtotime($row['Date_Posted']));
                        echo "<tr>
                                <td><a href='announce_info.php?id=$id'>$title</a></td>
                                <td>$date</td>
                            </tr>";
                    }
                    mysqli_free_result($result);
                    }
                    else{
                        echo "Error: " . mysqli_error($conn);
                    }
                    CloseCon($conn);
            ?>
        </table>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>