<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit();
}
?>

<?php include('bootstrap.php') ?>
</head>
<body>
    <div class="container mt-4">
        <h2>Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="admin_dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="manage_products.php" class="list-group-item list-group-item-action">Manage Products</a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">Manage Users</a>
                    <a href="manage_orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
                    <a href="logout.php" class="list-group-item list-group-item-action">Logout</a>
                </div>
            </div>
            <div class="col-md-9">
                <h3>Welcome to the Admin Panel!</h3>
                <p>Use the navigation on the left to manage the site.</p>
            </div>
        </div>
    </div>
    <?php include('js.php') ?>
</body>
</html>
