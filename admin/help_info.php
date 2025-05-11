<?php
session_start();

require_once '../db_connect.php';
$conn = OpenCon();

// Check if 'id' is set in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $helpId = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM help JOIN customer ON help.Cus_ID = customer.Cus_ID WHERE Help_ID = '$helpId'";
    $result = mysqli_query($conn, $sql);
    
    if($result) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['Help_ID'];
        $title = $row['Subject'];
        $description = $row['Description'];
        $reply = $row['Reply'];
        $date = date('d-m-Y', strtotime($row['Date_Submitted']));
        $user = $row['Username'];
        
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
        <form action="help_reply.php" method="POST">
            <input type='hidden' name='help_id' value='<?php echo $id; ?>'>
            <table class="formTable">
                <tr>
                    <th colspan="4" class="newBtnHeader">
                    <button id="disapproveBtn" type="button" onclick="if(confirm('Are you sure you want to delete this help request?')) window.location.href='help_delete.php?id=<?php echo $helpId; ?>'">Delete</button>
                    </th>
                </tr>
                <tr>
                    <th>By</th>
                    <td><?php echo $user; ?></td>
                    <th>Date</th>
                    <td><?php echo $date; ?></td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td colspan="3"><?php echo $title; ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td colspan="3"><?php echo $description; ?></td>
                </tr>
                <tr>
                    <th>Reply</th>
                    <td colspan="3"><textarea id="reply" name="reply" placeholder="Reply Here" required><?php echo $reply; ?></textarea></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <button id="approveBtn" type="submit">Submit Reply</button>
                        <button type="button" onclick="window.location.href='help_manage.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>