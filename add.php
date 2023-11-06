<?php
session_start();

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    include "header.php";
    $titlePage = "Add";

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = $_POST["title"];
        $authorName = $_POST["authorName"];
        $categories = isset($_POST["categories"]) ? $_POST["categories"] : [];
        $publicationYear = $_POST["publicationYear"];
        $description = $_POST["description"];
        $nbBooks = $_POST["nbBooks"];

        if (empty($title)) {
            $errors["title"] = "Le titre est manquant";
        }

        if (empty($authorName)) {
            $errors["authorName"] = "Le nom de l'auteur est manquant";
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
               
                $stmt = $pdo->prepare("INSERT INTO authors (authorName) VALUES (:authorName)");
                $stmt->bindParam(':authorName', $authorName);
                $stmt->execute();
                $id_author = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO books (title, publicationYear, id_author, bookDescription, nbBooks) VALUES (:title, :publicationYear, :id_author, :description, :nbBooks)");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':publicationYear', $publicationYear);
                $stmt->bindParam(':id_author', $id_author);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':nbBooks', $nbBooks);
                $stmt->execute();
                $id_books = $pdo->lastInsertId();

                
                foreach ($categories as $category) {
                    $stmt = $pdo->prepare("INSERT INTO bookCategories (id_books, id_category) VALUES (:id_books, (SELECT id_category FROM categories WHERE categoryName = :category))");
                    $stmt->bindParam(':id_books', $id_books, PDO::PARAM_INT);
                    $stmt->bindParam(':category', $category);
                    $stmt->execute();
                }

                $pdo->commit();

                header('Location: /index.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack(); 
                echo "Error: " . $e->getMessage();
            }
        } else {
            foreach ($errors as $key => $value) {
                echo $value . "<br/>";
            }
        }
    }

    $categoryQuery = "SELECT categoryName FROM categories";
    $stmt = $pdo->query($categoryQuery);
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<form method="post" action="add.php">
    <label for="title">Titre</label>
    <input type="text" name="title" id="title">
    <label for="authorName">Nom de l'auteur</label>
    <input type="text" name="authorName" id="authorName">
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
