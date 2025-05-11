<?php
session_start();

$AdminId = $_SESSION['Admin_ID'];

// connect to database
require_once '../db_connect.php';
$conn = OpenCon();

$sql = "SELECT * FROM help
        JOIN customer ON help.Cus_ID = customer.Cus_ID
        WHERE Admin_Id IN ('A000','$AdminId')
        ORDER BY CASE WHEN Reply = '' OR Reply IS NULL THEN 1 ELSE 2 END, Reply ASC";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Help Management</title>
    <?php include 'navbar.php'; ?>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="table-container">   
        <table class="listTable">
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Date Added</th>
                <th>By User</th>
            </tr>
            <?php
                if($result = mysqli_query($conn, $sql)){
                    while($row = mysqli_fetch_assoc($result)){
                        $id=$row['Help_ID'];
                        $user=$row['Username'];
                        $title=$row['Subject'];
                        $reply=$row['Reply'];
                        $date = date('d-m-Y', strtotime($row['Date_Submitted']));
                        echo "<tr>
                                <td><a href='help_info.php?id=$id'>$title</a></td>
                                <td style='color: " . (empty($reply) ? 'green' : 'gray') . ";'>"
                                . (empty($reply) ? 'Open' : 'Replied') . "</td>
                                <td>$date</td>
                                <td>$user</td>
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