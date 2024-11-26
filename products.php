<?php
session_start();
include('db_connection.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        
        $query = "SELECT * FROM products WHERE id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching product details: " . $e->getMessage();
        exit();
    }

    if (!$product) {
        echo "Product not found!";
        exit();
    }
} else {
    echo "Product ID is missing!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - E-Commerce</title>
    <link href="bootstrap-5.0.2-dist/css/bootstrap.css" rel="stylesheet">
    <style>
        .product-image {
            max-height: 400px;
            width: auto;
        }
        .product-description {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>


<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image img-fluid">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
            <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <button class="btn btn-primary add-to-cart-btn" data-id="<?php echo $product['id']; ?>">Add to Cart</button>
        </div>
    </div>
    <div class="mt-4">
        <a href="products.php" class="btn btn-secondary">Back to Products</a>
    </div>
</div>


<div class="cart-confirmation" id="cartConfirmation" style="display:none;">
    Item added to cart!
</div>



<script src="bootstrap-5.0.2-dist/js/jquery-3.7.1.min.js"></script>
<script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>

<script>

    document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        console.log('Added product with ID:', productId);


        const confirmation = document.getElementById('cartConfirmation');
        confirmation.style.display = 'block';
        setTimeout(function() {
            confirmation.style.display = 'none';
        }, 2000);


        $.ajax({
            url: 'cart.php', 
            method: 'GET',
            data: {
                action: 'add', 
                product_id: productId
            },
            success: function(response) {
                console.log('Product successfully added to cart!');
            },
            error: function() {
                alert('There was an error adding the product to the cart.');
            }
        });
    });
</script>

</body>
</html>
