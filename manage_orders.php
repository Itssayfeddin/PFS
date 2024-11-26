<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit();
}
include('db_connection.php');


$query = "SELECT * FROM orders";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('bootstrap.php') ?>
<body>
    <div class="container mt-4">
        <h2>Manage Orders</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Total Price</th> 
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td> 
                    <td><?php echo htmlspecialchars($order['status']); ?></td> 
                    <td>
                        <a href="view_order.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-info btn-sm">View</a>
                        <a href="update_order.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-warning btn-sm">Update</a>
                        <a href="delete_order.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include('js.php') ?>
</body>
</html>
