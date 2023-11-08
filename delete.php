<?php
session_start();
$titlePage = "Delete";
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $id_books = $_GET['id_books'];
    
    deleteBookAndCategories($pdo, $id_books);

    header('Location: /index.php');
    exit();

    include "footer.php";
} else {
    header('Location: /login.php');
    exit();
}
?>
