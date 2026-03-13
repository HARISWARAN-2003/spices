<?php

$host="localhost";
$user="root";
$password="mynewpass123";
$db="spice_shop";

$conn = mysqli_connect($host,$user,$password,$db);

if(!$conn){
    echo "Connection Failed";
}

?>