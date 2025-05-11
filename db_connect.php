<?php
function OpenCon(){
    $dbhost = "localhost";
    $db = "fooddeliverysystem";
    $dbuser = "root";
    $dbpass = "";

    $conn = mysqli_connect($dbhost,$dbuser,$dbpass,$db);
    if(!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function CloseCon($conn){
    $conn -> close();
}
?>