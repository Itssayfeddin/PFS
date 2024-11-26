<?php
session_start();
include('db_connection.php');


if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id']; 

    
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1; 
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }

        header('Location: cart.php');
        exit();
    } else {
        echo "Product not found!";
    }
}
