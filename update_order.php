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

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_status = $_POST['status'];

    
        $updateQuery = "UPDATE orders SET status = :status WHERE id = :order_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $updateStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            
            header("Location: manage_orders.php");
            exit();
        } else {
            $error = "Error updating the order status.";
        }
    }
} else {
    
    echo "Invalid order ID!";
    exit();
}
?>

<?php include('bootstrap.php') ?>

<body>
    <div class="container mt-4">
        <h2>Update Order - Order #<?php echo htmlspecialchars($order['id']); ?></h2>

        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Order Information</h5>
            </div>
            <div class="card-body">
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Current Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            </div>
        </div>

        
        <form method="POST">
            <div class="form-group">
                <label for="status">Order Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Status</button>
        </form>

        
        <a href="manage_orders.php" class="btn btn-primary mt-3">Back to Orders</a>
    </div>

    <?php include('js.php') ?>
</body>
</html>
