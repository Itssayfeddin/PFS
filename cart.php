<?php
session_start();
include('db_connection.php');


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $product_id => $product) {
        $total += $product['price'] * $product['quantity'];
    }
    return $total;
}

if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $quantity = 1;

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image_url' => $product['image_url']
            ];
        }
    }
}

if (isset($_POST['update_cart']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;

        echo count($_SESSION['cart']);
    } else {
        unset($_SESSION['cart'][$product_id]);
        echo count($_SESSION['cart']);
    }

    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    unset($_SESSION['cart'][$product_id]);
}
?>

<?php include('bootstrap.php') ?>
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
                <a class="nav-link" href="cart.php">Cart <span id="cart-count"><?php echo count($_SESSION['cart']); ?></span></a>
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

<div class="container mt-4">
    <h2>Your Shopping Cart</h2>
    
    <?php if (!empty($_SESSION['cart'])): ?>
    <form action="cart.php" method="POST" id="cart-form">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $product_id => $product): ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" width="50">
                            <?php else: ?>
                                <img src="default-image.jpg" alt="Default image" width="50">
                            <?php endif; ?>
                            <?php echo $product['name']; ?>
                        </td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $product['quantity']; ?>" min="1" class="form-control update-quantity" data-id="<?php echo $product_id; ?>" style="width: 80px;">
                        </td>
                        <td>$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
                        <td>
                            <a href="cart.php?action=remove&product_id=<?php echo $product_id; ?>" class="btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
    </form>

    <h3>Total: $<?php echo number_format(calculateTotal($_SESSION['cart']), 2); ?></h3>

    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    <?php else: ?>
        <p>Your cart is empty. <a href="products.php">Browse Products</a></p>
    <?php endif; ?>
</div>

<?php include('js.php') ?>

<script>
document.querySelectorAll('.update-quantity').forEach(input => {
    input.addEventListener('change', function() {
        const productId = this.getAttribute('data-id');
        const quantity = this.value;

        
        if (quantity <= 0) {
            alert('Quantity must be at least 1');
            return;
        }

        const formData = new FormData();
        formData.append('update_cart', true);  
        formData.append('product_id', productId);  
        formData.append('quantity', quantity);  

        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            
            document.querySelector('#cart-count').textContent = data;
            location.reload(); 
        })
        .catch(error => console.error('Error:', error));
    });
});

</script>

</body>
</html>
