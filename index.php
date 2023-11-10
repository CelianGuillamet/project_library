<?php 
session_start();
 
$titlePage = "index";

include "header.php";

if (isset($_SESSION['name'])) {
    echo 'Bonjour ' . $_SESSION['name'] . ' !';
    echo '<a href="logout.php">Logout</a>';
} else {
    echo 'please login ';
    echo '<a href="login.php">Login</a>';
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%';
    $resultQuery = $pdo -> prepare("SELECT books.id_books, books.title, authors.authorName FROM books
                    LEFT JOIN authors ON books.id_author = authors.id_author
                    WHERE books.title LIKE :searchTerm OR authors.authorName LIKE :searchTerm OR books.publicationYear LIKE :searchTerm");
    $resultQuery->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $resultQuery->execute();
    $results = $resultQuery->fetchAll(PDO::FETCH_ASSOC);
} else {
    $resultQuery = $pdo->prepare("SELECT books.id_books, books.title, authors.authorName FROM books
                    INNER JOIN authors ON books.id_author = authors.id_author");
    $resultQuery->execute();
    $results = $resultQuery->fetchAll(PDO::FETCH_ASSOC);
}

echo '<form method="GET">';
echo 'Search: <input type="text" name="search" placeholder="Enter a search term" value="' . (isset($searchTerm) ? $searchTerm : '') . '">';
echo '<input type="submit" value="Search">';
echo '</form>';

echo '<div class="container">';
echo '<table>';
echo '<thead>';
echo '<tr><th>identifiant</th><th>Titre du livre </th><th>Nom de l\'auteur</th><th>Actions</th></tr>';
echo '</thead>';
echo '<tbody>';
foreach ($results as $row) {
    echo '<tr>';
    echo '<td>' . $row['id_books'] . '</td>';
    echo '<td>' . $row['title'] . '</td>';
    echo '<td>' . $row['authorName'] . '</td>';
    echo '<td>
    <a href="details.php?id_books=' . $row['id_books'] . '">Détails</a><br/>
    <a href="edit.php?id_books=' . $row['id_books'] . '">Modifier</a><br/>
    <a href="delete.php?id_books=' . $row['id_books'] . '">Supprimer</a><br/>
    <a href="addCart.php?id_books=' . $row['id_books'] . '">Ajouter au panier</a><br/>
    </td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
echo '<a href="add.php">Ajouter</a>';
echo '<br/>';
echo '<a href="cart.php">Voir le panier</a>'; 

include "footer.php";