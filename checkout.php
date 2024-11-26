<?php
session_start();
include('db_connection.php');


function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $product) {
        $total += $product['price'] * $product['quantity'];
    }
    return $total;
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $payment_method = $_POST['payment_method'];

    try {
        
        $pdo->beginTransaction();

        
        $totalAmount = calculateTotal($_SESSION['cart']); 

        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_name, shipping_address, shipping_city, shipping_zip, payment_method) 
            VALUES (:user_id, :total_amount, :shipping_name, :shipping_address, :shipping_city, :shipping_zip, :payment_method)");

        
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $totalAmount, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_city', $city, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_zip', $zip, PDO::PARAM_STR);
        $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);

        
        $stmt->execute();

        
        $order_id = $pdo->lastInsertId();

        
foreach ($_SESSION['cart'] as $product_id => $product) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) 
        VALUES (:order_id, :product_id, :product_name, :quantity, :price, :total)");

    
    $total = $product['price'] * $product['quantity']; 

    // Bind parameters
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_name', $product['name'], PDO::PARAM_STR);
    $stmt->bindParam(':quantity', $product['quantity'], PDO::PARAM_INT);
    $stmt->bindParam(':price', $product['price'], PDO::PARAM_STR);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR); 

    
    $stmt->execute();
}


        
        $pdo->commit();

        
        unset($_SESSION['cart']);

        
        header("Location: order_confirmation.php");
        exit;

    } catch (PDOException $e) {
        
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>


<?php include('bootstrap.php'); ?>
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

<div class="container mt-4">
    <h2>Checkout</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <h3>Your Cart</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Total: $<?php echo number_format(calculateTotal($_SESSION['cart']), 2); ?></h3>
        </div>
        <div class="col-md-4">
            <h3>Shipping Information</h3>
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="address">Shipping Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="zip">Zip Code</label>
                    <input type="text" class="form-control" id="zip" name="zip" required>
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Place Order</button>
            </form>
        </div>
    </div>
</div>

<?php include('js.php'); ?>
</body>
</html>
