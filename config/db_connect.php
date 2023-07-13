<?php 
session_start();

$conn = mysqli_connect('localhost','root','','socialmedia');

if(!$conn){
    echo 'Conexão falhada:'. mysqli_connect_error();
}
//error_reporting(0);
?>