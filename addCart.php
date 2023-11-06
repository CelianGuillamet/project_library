<?php
session_start();
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
} else {
    header('Location: /login.php');
    die();
}
include "header.php";
if (isset($_GET['id_books']) && is_numeric($_GET['id_books'])) {
    $idBooks = $_GET['id_books'];

    $productQuery = "SELECT books.id_books, books.title FROM books WHERE books.id_books = $idBooks";

    $productResult = $link->query($productQuery);

    if ($productResult && $productResult->num_rows > 0) {
        $product = $productResult->fetch_assoc();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$idBooks])) {

            $_SESSION['cart'][$idBooks]['quantite']++;
        } else {

            $_SESSION['cart'][$idBooks] = [
                'title' => $product['title'],
                'quantite' => 1,
            ];
        }

        echo "Produit ajouté au cart !";
    } else {
        echo "Produit introuvable.";
    }
} else {
    echo "ID de produit invalide.";
}

header("Location: index.php");
?>
