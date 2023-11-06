<?php
session_start();
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
} else {
    header('Location: /login.php');
}
include "header.php";
if (isset($_GET['id_books']) && is_numeric($_GET['id_books'])) {
    $idBooks = $_GET['id_books'];

    if (isset($_SESSION['cart'])) {
        if (isset($_SESSION['cart'][$idBooks])) { 
            unset($_SESSION['cart'][$idBooks]);
        }
    }
}

header('Location: /cart.php');
?>
