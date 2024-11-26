<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit();
}
include('db_connection.php');


$name = $description = $price = $stock = $image_url = "";
$name_err = $description_err = $price_err = $stock_err = $image_url_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty(trim($_POST['name']))) {
        $name_err = "Please enter the product name.";
    } else {
        $name = trim($_POST['name']);
    }

    
    if (empty(trim($_POST['description']))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST['description']);
    }

    
    if (empty(trim($_POST['price']))) {
        $price_err = "Please enter the price.";
    } elseif (!is_numeric($_POST['price'])) {
        $price_err = "Price must be a valid number.";
    } else {
        $price = trim($_POST['price']);
    }

    
    if (empty(trim($_POST['stock']))) {
        $stock_err = "Please enter the stock quantity.";
    } elseif (!is_numeric($_POST['stock'])) {
        $stock_err = "Stock must be a valid number.";
    } else {
        $stock = trim($_POST['stock']);
    }


    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_info = pathinfo($_FILES['image']['name']);
        $file_extension = strtolower($file_info['extension']);
        $target_dir = "uploads/"; 
        $target_file = $target_dir . basename($_FILES['image']['name']);
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $image_url_err = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($_FILES['image']['size'] > 5000000) { 
            $image_url_err = "File size must be less than 5MB.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = $target_file; 
            } else {
                $image_url_err = "Sorry, there was an error uploading your image.";
            }
        }
    } else {
        $image_url_err = "Please upload a product image.";
    }

    
    if (empty($name_err) && empty($description_err) && empty($price_err) && empty($stock_err) && empty($image_url_err)) {
        $query = "INSERT INTO products (name, description, price, stock, image_url) VALUES (:name, :description, :price, :stock, :image_url)";
        $stmt = $pdo->prepare($query);
        
        
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $image_url, PDO::PARAM_STR);

        if ($stmt->execute()) {

            header("location: manage_products.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
?>

<?php include('bootstrap.php'); ?>
<body>
    <div class="container mt-4">
        <h2>Add New Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label for="name">Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                <label for="description">Product Description</label>
                <textarea name="description" class="form-control" rows="4" required><?php echo $description; ?></textarea>
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                <label for="price">Price</label>
                <input type="text" name="price" class="form-control" value="<?php echo $price; ?>" required>
                <span class="help-block"><?php echo $price_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($stock_err)) ? 'has-error' : ''; ?>">
                <label for="stock">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" value="<?php echo $stock; ?>" required>
                <span class="help-block"><?php echo $stock_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($image_url_err)) ? 'has-error' : ''; ?>">
                <label for="image">Product Image</label>
                <input type="file" name="image" class="form-control" required>
                <span class="help-block"><?php echo $image_url_err; ?></span>
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php include('js.php') ?>
</body>
</html>
