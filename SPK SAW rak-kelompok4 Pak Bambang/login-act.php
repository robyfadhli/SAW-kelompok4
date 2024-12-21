<?php

include 'include/conn.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$login = $db->query("select * from saw_users where username='$username' and password='$password'");
$cek = mysqli_num_rows($login);

// set session


if ($cek > 0) {
    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['status'] = "login";
    echo '<script>alert("Login Sukses! Selamat datang.");window.location="index.php"</script>';
   
} else {
    echo '<script>alert("Maaf! username atau password tidak valid.");history.go(-1);window.location="login.php"</script>';

}
