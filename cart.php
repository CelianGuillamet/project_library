<?php

session_start();
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
} else {
    header('Location: /login.php');
}
$titlePage = "Panier";

include "header.php";

echo '<h1>Votre Panier</h1>';

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo '<form method="POST" action="cart.php">';
    echo '<table>';
    echo '<tr><th>Titre du livre</th><th>Quantité</th><th>Actions</th></tr>';
    
    $totalPanier = 0;
    
    foreach ($_SESSION['cart'] as $idBooks => $product) {
        echo '<tr>';
        echo '<td>' . $product['title'] . '</td>';
        echo '<td><input type="number" name="quantite[' . $idBooks . ']" value="' . $product['quantite'] . '" min="1" max="10"></td>'; // Champ pour la quantité
        echo '<td><a href="deleteItemCart.php?id_books=' . $idBooks . '">Supprimer</a></td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '</form>';
} else {
    echo "Le panier est vide.";
    echo '<a href="index.php">Retour à la liste des livres</a>';
}

include "footer.php";
?>
