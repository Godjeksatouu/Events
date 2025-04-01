<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE mailUser = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['motPasse'])) {
            $_SESSION['utilisateur_id'] = $user["idUser"];
            $_SESSION['utilisateur_name'] = $user['nomUser'] . " " . $user['prenomUser'];
    
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Email not found.";
    }
    
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
    <title>Login</title>
</head>
<body>
    <section class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <p class="signup-link">Don't have an account? <a href="sign-up.php">Sign Up</a></p>
        </div>
    </section>
</body>
</html>
