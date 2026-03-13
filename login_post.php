<?php
include "db.php";

if(isset($_POST['username'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {
        // Successful login
        header("Location: dashboard.php");
        exit();
    } else {
        // Failed login - Send back to login with error
        echo "<script>alert('Invalid Username or Password'); window.location.href='login.php';</script>";
    }
}
?>