<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    
    if (isset($_GET['id_books']) && is_numeric($_GET['id_books'])) {
        $idBooks = $_GET['id_books'];

        $productQuery = "SELECT books.id_books, books.title FROM books WHERE books.id_books = :idBooks";
        $stmt = $pdo->prepare($productQuery);
        $stmt->bindParam(':idBooks', $idBooks, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
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
} else {
    header('Location: /login.php');
    exit();
}
?>
