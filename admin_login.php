<?php
session_start();
include('db_connection.php');


$email_err = $password_err = "";
$email = $password = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST['email']);
    }

    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST['password']);
    }

    
    if (empty($email_err) && empty($password_err)) {
        $query = "SELECT id, email, password, role FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['id'];
            $role = $row['role']; 

            if (password_verify($password, $row['password'])) {
                if ($role == 'admin') {
                    
                    $_SESSION['admin_id'] = $user_id;
                    $_SESSION['email'] = $email;

                    
                    header("location: admin_dashboard.php");
                    exit();
                } else {
                    $password_err = "You do not have admin access.";
                }
            } else {
                $password_err = "Invalid password.";
            }
        } else {
            $email_err = "No account found with that email.";
        }
    }
}
?>

<?php include('bootstrap.php') ?>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <?php include('js.php') ?>
</body>
</html>
