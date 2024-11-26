<?php
session_start();


include('db_connection.php');


$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Password did not match.";
        }
    }

    
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $email_err = "This email is already taken.";
        } else {
            
            $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $pdo->prepare($query);

            
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            
            $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

            if ($stmt->execute()) {
                
                header("location: login.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<?php include('bootstrap.php') ?>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px; border-radius: 15px;">
            <div class="card-body">
                <h2 class="text-center mb-4">Create Account</h2>
                <p class="text-center mb-4">Please fill in this form to create an account.</p>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    
                    <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                        <div class="text-danger"><?php echo $name_err; ?></div>
                    </div>

                    
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

                    
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                        <div class="text-danger"><?php echo $confirm_password_err; ?></div>
                    </div>

                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </div>

                    
                    <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
                </form>
            </div>
        </div>
    </div>

    <?php include('js.php') ?>
    <script>
document.querySelector("form").addEventListener("submit", function(event) {
    let password = document.querySelector("input[name='password']").value;
    let confirmPassword = document.querySelector("input[name='confirm_password']").value;
    
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        event.preventDefault();
    }
});
</script>

</body>
</html>
