<?php
session_start();

// connect to database
require_once '../db_connect.php';
$conn = OpenCon();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Management</title>
    <?php include 'navbar.php'; ?>
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="table-container">
        <table class="listTable">
            <tr>
                <th>Vendor Name</th>
                <th>Status</th>
                <th>By User</th>
                <th>Action</th>
            </tr>
            <?php
                $sql = "SELECT Vendor_ID, Username, Vendor_Name, Registration_Status FROM vendor
                ORDER BY CASE WHEN Registration_Status = 'Pending' 
                THEN 1 WHEN Registration_Status = 'Rejected' THEN 2 WHEN Registration_Status = 'Approved' THEN 3 ELSE 4 END, Vendor_Name ASC";

                if($result = mysqli_query($conn, $sql)){
                    while($row = mysqli_fetch_assoc($result)){
                        $id=$row['Vendor_ID'];
                        $user=$row['Username'];
                        $vendor=$row['Vendor_Name'];
                        $status=$row['Registration_Status'];
                        echo "<tr>
                                <td>$vendor</td>
                                <td style='color: " . (($status == 'Approved') ? 'green' : (($status == 'Rejected') ? 'red' : 'default')) . ";'>$status</td>
                                <td>$user</td>
                                <td>
                                    <form action='vendor_update.php' method='post'>
                                        <input type='hidden' name='vendor_id' value='" . $id . "'>
                                        <input id='approveBtn' type='submit' name='action' value='Approve'>
                                        <input id='rejectBtn' type='submit' name='action' value='Reject'>
                                    </form>
                                </td>
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