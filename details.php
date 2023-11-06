<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $titlePage = "details";

    $id_books = $_GET['id_books'];
  
    $query = "SELECT books.title, books.publicationYear, books.bookDescription, books.nbBooks, authors.authorName, GROUP_CONCAT(categories.categoryName SEPARATOR ', ') as categoryNames
              FROM books 
              JOIN authors ON books.id_author = authors.id_author 
              JOIN bookCategories ON books.id_books = bookCategories.id_books
              JOIN categories ON bookCategories.id_category = categories.id_category
              WHERE books.id_books = :id_books";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_books', $id_books, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo '<div class="container">';
    echo '<table>';
    echo '<tr><th>Titre</th><td>' . $result["title"] . '</td></tr>';
    echo '<tr><th>Date de publication</th><td>' . $result["publicationYear"] . '</td></tr>';
    echo '<tr><th>Prénom de l\'auteur</th><td>' . $result["authorName"] . '</td></tr>';
    echo '<tr><th>Catégories</th><td>' . $result["categoryNames"] . '</td></tr>';
    echo '<tr><th>Description</th><td>' . $result["bookDescription"] . '</td></tr>';
    echo '<tr><th>Quantité disponible</th><td>' . $result["nbBooks"] . '</td></tr>';
    echo '</table>';
    echo '</div';

    include "footer.php";
} else {
    header('Location: /login.php');
    exit();
}
?>
