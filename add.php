<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $titlePage = "Add";

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = $_POST["title"];
        $authorID = $_POST["authorID"];
        $newAuthor = $_POST["newAuthor"];
        $categories = isset($_POST["categories"]) ? $_POST["categories"] : [];
        $publicationYear = $_POST["publicationYear"];
        $description = $_POST["description"];
        $nbBooks = $_POST["nbBooks"];

        if (empty($title)) {
            $errors["title"] = "Le titre est manquant";
        }

        if (empty($authorID) && empty($newAuthor)) {
            $errors["authorID"] = "L'auteur est manquant";
        }

        if (empty($categories)) {
            $errors["categories"] = "La catégorie est manquante";
        }

        if (empty($publicationYear)) {
            $errors["publicationYear"] = "L'année de publication est manquante";
        }

        if (empty($description)) {
            $errors["description"] = "La description est manquante";
        }

        if (empty($nbBooks)) {
            $errors["nbBooks"] = "La quantité est manquante";
        }

        if (count($errors) === 0) {
            $pdo->beginTransaction();

            try {
                if (empty($authorID) && !empty($newAuthor)) {
                    $checkAuthorQuery = "SELECT id_author FROM authors WHERE authorName = :newAuthor";
                    $stmt = $pdo->prepare($checkAuthorQuery);
                    $stmt->bindParam(':newAuthor', $newAuthor, PDO::PARAM_STR);
                    $stmt->execute();
                    $existingAuthor = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingAuthor) {
                        $newAuthorID = $existingAuthor['id_author'];
                    } else {
                        $insertAuthorQuery = "INSERT INTO authors (authorName) VALUES (:newAuthor)";
                        $stmt = $pdo->prepare($insertAuthorQuery);
                        $stmt->bindParam(':newAuthor', $newAuthor, PDO::PARAM_STR);
                        $stmt->execute();
                        $newAuthorID = $pdo->lastInsertId();
                    }
                } else {
                    $newAuthorID = $authorID;
                }

                $stmt = $pdo->prepare("INSERT INTO books (title, publicationYear, id_author, bookDescription, nbBooks) VALUES (:title, :publicationYear, :id_author, :description, :nbBooks)");
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':publicationYear', $publicationYear, PDO::PARAM_INT);
                $stmt->bindParam(':id_author', $newAuthorID, PDO::PARAM_INT);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':nbBooks', $nbBooks, PDO::PARAM_INT);
                $stmt->execute();
                $id_books = $pdo->lastInsertId();

                foreach ($categories as $category) {
                    $stmt = $pdo->prepare("INSERT INTO bookCategories (id_books, id_category) VALUES (:id_books, (SELECT id_category FROM categories WHERE categoryName = :category))");
                    $stmt->bindParam(':id_books', $id_books, PDO::PARAM_INT);
                    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                    $stmt->execute();
                }

                $pdo->commit();

                header('Location: /index.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    }

    $authorsQuery = "SELECT id_author, authorName FROM authors";
    $authorsResult = $pdo->query($authorsQuery);
    $authorsList = $authorsResult->fetchAll(PDO::FETCH_ASSOC);

    $categoryQuery = "SELECT categoryName FROM categories";
    $stmt = $pdo->query($categoryQuery);
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<form method="post" action="add.php">
    <label for="title">Titre</label>
    <input type="text" name="title" id="title">
    <tr><th>Auteur : </th><td>
    <select name="authorID">
        <option value="0">-- Nouvel auteur --</option>
        <?php 
            foreach ($authorsList as $author) {
            $selected = ($author['id_author'] == $authorID) ? 'selected' : '';
            echo '<option value="' . $author['id_author'] . '" ' . $selected . '>' . $author['authorName'] . '</option>';
            }
        ?>
    </select>
    <input type="text" name="newAuthor" placeholder="Nom du nouvel auteur">
    </td></tr>
    <label for="publicationYear">Année de publication</label>
    <input type="text" name="publicationYear" id="publicationYear">
    <label for="description">Description</label>
    <textarea name="description" id="description"></textarea>
    <label for="nbBooks">Quantité</label>
    <input type="text" name="nbBooks" id="nbBooks">
    <fieldset>
        <legend>Catégories</legend>
        <?php
        foreach ($categories as $category) {
            echo '<label>';
            echo '<input type="checkbox" name="categories[]" value="' . $category . '"> ' . $category;
            echo '</label><br>';
        }
        ?>
    </fieldset>
    <input type="submit" value="Ajouter">
</form>

<?php

include "footer.php";
} else {
    header('Location: /login.php');
    exit();
}
?>
