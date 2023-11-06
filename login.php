<?php

session_start();

if (isset($_POST['name'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['user_role'] = 'admin';
    header('Location: /index.php');
}

?>

<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <input type="text" name="name" placeholder="Enter your name">
    <input type="submit" value="Login">