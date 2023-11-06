<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $titlePage = "Delete";

    $id_books = $_GET['id_books'];

    // Create a PDO instance
    $pdo = new PDO('mysql:host=localhost;dbname=project_library', 'root', '');

    // Use prepared statements to delete records
    $deleteBookCategoriesQuery = "DELETE FROM bookcategories WHERE id_books = :id_books";
    $deleteBooksQuery = "DELETE FROM books WHERE id_books = :id_books";

    $pdo->beginTransaction(); // Start a transaction

    try {
        $stmt1 = $pdo->prepare($deleteBookCategoriesQuery);
        $stmt1->bindParam(':id_books', $id_books, PDO::PARAM_INT);
        $stmt1->execute();

        $stmt2 = $pdo->prepare($deleteBooksQuery);
        $stmt2->bindParam(':id_books', $id_books, PDO::PARAM_INT);
        $stmt2->execute();

        $pdo->commit(); // Commit the transaction
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
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
