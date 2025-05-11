<?php
session_start();
if (!isset($_SESSION["mySession"])) {
    header("Location: ../login.php");
    exit();
}
require_once '../db_connect.php';
$conn = OpenCon();
// $status = $statusMsg = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // $status = "error";
    $sql;
    $adminId = $_POST['admin_id'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $fileName = basename($_FILES['image']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileType = strtolower($fileExt);
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

        if(in_array($fileType, $allowTypes)){
            $image = $_FILES['image']['tmp_name'];
            $imgContent = addslashes(file_get_contents($image));

            $getId = $conn->query("SELECT Announcement_ID FROM announcement ORDER BY Announcement_ID DESC LIMIT 1");
            //Determine the ID for the new announcement
            $latestId = 'ANC001'; // Default ID if no entries are found
            if ($getId->num_rows > 0) {
                $row = $getId->fetch_assoc();
                $latestId = $row['Announcement_ID'];
            }
            $numericPart = substr($latestId, 3); // Remove the  prefix
            $nextNumber = intval($numericPart) + 1; // Convert to integer and increment
            // Step 3: Format the new ID
            $newId = 'ANC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // Ensure the ID is in the correct format, e.g., A013

            $sql = "INSERT INTO announcement (Announcement_ID, Admin_ID, Announcement_Subject, Content, Announcement_Image, Date_Posted)
                    VALUES ('".$newId."','".$adminId."','".$subject."','".$content."','".$imgContent."', NOW())";

            $insert = $conn->query($sql);
            if($insert){
                $status = 'success';
                echo "<script>
                      alert('Successfully made announcement!');
                      window.location.href='announce_manage.php';
                      </script>";
            } else {
                echo "<script>
                      alert('Error inserting record: " . addslashes($conn->error) . "');
                      window.history.back();
                      </script>";
            }

        } else {
            echo "<script>
                  alert('ALERT! File must be jpg, jpeg, png, or gif.');
                  window.history.back();
                  </script>";
        }
    }

    
}

?>