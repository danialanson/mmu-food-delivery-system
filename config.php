<?php 
    $conn = new mysqli("localhost","root","","fooddeliverysystem");
    if ($conn->connect_error){
        die("Connection Failed.".$conn->connect_error);
    }
?>