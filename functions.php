<?php

//function to connect to the database
function connectDB(){
    require_once 'connect.php';
    try{
        $pdo = new \PDO(DSN, USER, PASS);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET NAMES "utf8"');
        return $pdo;
    } catch (\PDOException $e) {
        echo 'Unable to connect to the database server: ' . $e->getMessage();
    }
}

//function to delete a book and its categories
function deleteBookAndCategories($pdo, $id_books) {
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
}