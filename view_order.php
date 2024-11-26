<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit();
}

include('db_connection.php');

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    
    $query = "SELECT * FROM orders WHERE id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if (!$order) {
        echo "Order not found!";
        exit();
    }

    
    $query = "SELECT * FROM order_items WHERE order_id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    
    echo "Invalid order ID!";
    exit();
}

?>

<?php include('bootstrap.php') ?>

<body>
    <div class="container mt-4">
        <h2>Order Details - Order #<?php echo htmlspecialchars($order['id']); ?></h2>

        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order Information</h5>
            </div>
            <div class="card-body">
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            </div>
        </div>


        <div class="card mb-4">
            <div class="card-header">
                <h5>Shipping Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($order['shipping_city']); ?></p>
                <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($order['shipping_zip']); ?></p>
            </div>
        </div>

        
        <h4>Order Items</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="manage_orders.php" class="btn btn-primary mt-3">Back to Orders</a>
    </div>

    <?php include('js.php') ?>
</body>
</html>
