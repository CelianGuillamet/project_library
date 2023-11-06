<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $titlePage = "Delete";

    $id_books = $_GET['id_books'];

    $deleteBookCategoriesQuery = "DELETE FROM bookcategories WHERE id_books = :id_books";
    $deleteBooksQuery = "DELETE FROM books WHERE id_books = :id_books";

    $pdo->beginTransaction();

    try {
        $stmt1 = $pdo->prepare($deleteBookCategoriesQuery);
        $stmt1->bindParam(':id_books', $id_books, PDO::PARAM_INT);
        $stmt1->execute();

        $stmt2 = $pdo->prepare($deleteBooksQuery);
        $stmt2->bindParam(':id_books', $id_books, PDO::PARAM_INT);
        $stmt2->execute();

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }

    header('Location: /index.php');
    exit();

    include "footer.php";
} else {
    header('Location: /login.php');
    exit();
}
?>
