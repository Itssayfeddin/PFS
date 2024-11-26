<?php

session_start();
include('db_connection.php');
$email = $password = "";
$email_err = $password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $query = "SELECT id, name, email, password FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();


        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                session_regenerate_id(true);

                header("location: index.php");
                exit();
            } else {
                $password_err = "The password you entered is incorrect.";
            }
        } else {
            $email_err = "No account found with that email.";
        }
    }
}
?>

<?php include('bootstrap.php'); ?>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px; border-radius: 15px;">
            <div class="card-body">
                <h2 class="text-center mb-4">Login</h2>
                <p class="text-center mb-4">Please enter your credentials to log in</p>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                        <div class="text-danger"><?php echo $email_err; ?></div>
                    </div>
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <div class="text-danger"><?php echo $password_err; ?></div>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </div>

                    <p class="text-center mt-3">Don't have an account? <a href="register.php">Sign up here</a></p>
                </form>
            </div>
        </div>
    </div>

    <?php include('js.php'); ?>
    
</body>
</html>
