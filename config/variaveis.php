<?php 
include('config/db_connect.php');
if(!empty($_SESSION["id"])){
    $id = $_SESSION["id"];
    $result = mysqli_query($conn,"SELECT * FROM users WHERE id= $id");
    $row = mysqli_fetch_assoc($result);
    $result2 = mysqli_query($conn,"SELECT * FROM users WHERE id= $id");
    $rowdb = mysqli_fetch_assoc($result2);
}else {
    header("Location: login.php");
}
$username=$row["username"];
?>