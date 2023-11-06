<?php
session_start();
include "header.php";
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $titlePage = "Edit";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id_books = $_GET['id_books'];
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
        $publicationYear = htmlspecialchars($_POST['publicationYear'], ENT_QUOTES);
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
        $authorID = (int) $_POST['authorID']; 
        $newAuthor = htmlspecialchars($_POST['newAuthor'], ENT_QUOTES);

        $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

        $updateBookQuery = "UPDATE books SET title = ?, publicationYear = ?, bookDescription = ?, id_author = ? WHERE id_books = ?";
        $stmt = $pdo->prepare($updateBookQuery);
        $stmt->execute([$title, $publicationYear, $description, $authorID, $id_books]);

        if ($authorID == 0 && !empty($newAuthor)) {
            $insertAuthorQuery = "INSERT INTO authors (authorName) VALUES (?)";
            $stmt = $pdo->prepare($insertAuthorQuery);
            $stmt->execute([$newAuthor]);
            $newAuthorID = $pdo->lastInsertId();
        } else {
            $newAuthorID = $authorID;
        }

        $updateAuthorQuery = "UPDATE books SET id_author = ? WHERE id_books = ?";
        $stmt = $pdo->prepare($updateAuthorQuery);
        $stmt->execute([$newAuthorID, $id_books]);

        $deleteCategoriesQuery = "DELETE FROM bookCategories WHERE id_books = ?";
        $stmt = $pdo->prepare($deleteCategoriesQuery);
        $stmt->execute([$id_books]);

        $insertCategoryQuery = "INSERT INTO bookCategories (id_books, id_category) VALUES (?, (SELECT id_category FROM categories WHERE categoryName = ?))";
        $stmt = $pdo->prepare($insertCategoryQuery);

        foreach ($categories as $category) {
            $stmt->execute([$id_books, $category]);
        }

        header('Location: /index.php');
        exit();
    }

    $id_books = $_GET['id_books'];
    $query = "SELECT books.id_books, books.title, books.publicationYear, books.bookDescription, books.id_author, authors.authorName
              FROM books
              JOIN authors ON books.id_author = authors.id_author
              WHERE books.id_books = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_books]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    $authorsQuery = "SELECT id_author, authorName FROM authors";
    $authorsResult = $pdo->query($authorsQuery);
    $authorsList = [];

    while ($author = $authorsResult->fetch(PDO::FETCH_ASSOC)) {
        $authorsList[$author['id_author']] = $author['authorName'];
    }

    $categoryQuery = "SELECT categoryName FROM categories";
    $categoryResult = $pdo->query($categoryQuery);
    $categories = [];

    while ($row = $categoryResult->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row['categoryName'];
    }
    $selectedCategories = [];

    echo '<form action="edit.php?id_books=' . $id_books . '" method="POST">';
    echo '<table>';
    echo '<tr><th>Titre : </th><td><input type="text" name="title" value="' . $book["title"] . '"</td></tr>';
    echo '<tr><th>Date de publication : </th><td><input type="text" name="publicationYear" value="' . $book["publicationYear"] . '"</td></tr>';
    echo '<tr><th>Auteur : </th><td>';
    echo '<select name="authorID">';
    echo '<option value="0">-- Nouvel auteur --</option>';
    foreach ($authorsList as $authorID => $authorName) {
        $selected = ($authorID == $book['id_author']) ? 'selected' : '';
        echo '<option value="' . $authorID . '" ' . $selected . '>' . $authorName . '</option>';
    }
    echo '</select>';
    echo '<input type="text" name="newAuthor" placeholder="Nom du nouvel auteur">';
    echo '</td></tr>';
    echo '<tr><td>';
    echo '<fieldset>';
    echo '<legend>Catégories</legend>';
    foreach ($categories as $category) {
        $checked = in_array($category, $selectedCategories) ? 'checked' : '';
        echo '<label>';
        echo '<input type="checkbox" name="categories[]" value="' . $category . '" ' . $checked . '> ' . $category;
        echo '</label><br>';
    }
    echo '</fieldset>';
    echo '</td></tr>';
    echo '<tr><th>Description</th><td><textarea name="description">' . $book["bookDescription"] . '</textarea></td></tr>';
    echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
    echo '</table>';
    echo '</form>';

    include "footer.php";
} else {
    header('Location: /login.php');
    exit();
}
