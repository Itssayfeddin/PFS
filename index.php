<?php
session_start();
include('db_connection.php')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My E-commerce Site</title>
    <link href="bootstrap-5.0.2-dist/css/bootstrap.css" rel="stylesheet">
    <style>
        
        .card:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }

        
        .cart-confirmation {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
            z-index: 9999;
        }

    
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">E-Commerce</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products.php">Products</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">Cart</a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php else: ?>
                
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<?php
include('db_connection.php');

try {
    $query = "SELECT * FROM products";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
}
?>

<div class="container">
    <div class="row">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="card" data-id="<?php echo $product['id']; ?>">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" height="300px" width="100px">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                            <button class="btn btn-primary add-to-cart-btn" data-id="<?php echo $product['id']; ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found!</p>
        <?php endif; ?>
    </div>
</div>

<div class="cart-confirmation" id="cartConfirmation">
    Item added to cart!
</div>


<footer class="bg-light py-4">
    <div class="container text-center">
        <p>&copy; 2024 My E-Commerce Site</p>
    </div>
</footer>

<script src="bootstrap-5.0.2-dist/js/jquery-3.7.1.min.js"></script>
<script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            console.log('Added product with ID:', productId);
            const confirmation = document.getElementById('cartConfirmation');
            confirmation.style.display = 'block';
            setTimeout(function() {
                confirmation.style.display = 'none';
            }, 2000);
        });
    });
    const productCards = document.querySelectorAll('.card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg');
        });
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg');
        });
    });
</script>
<script>
    $(document).ready(function() {
    $('.add-to-cart-btn').on('click', function() {
        var productId = $(this).data('id');
        $.ajax({
            url: 'cart.php', 
            method: 'GET',
            data: {
                action: 'add', 
                product_id: productId 
            },
            success: function(response) {
                alert('Product added to cart!');
            },
            error: function() {
                alert('There was an error adding the product to the cart.');
            }
        });
    });
});
</script>
</body>
</html>