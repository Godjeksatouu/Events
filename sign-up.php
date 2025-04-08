<?php 
session_start();
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE mailUser = ?");
    $stmt->execute([$email]);
    $existingClient = $stmt->fetch();

    if($existingClient) {
        $error = 'Email address already exists. PLease log in';
    } else {
        $stmt = $pdo->prepare("SELECT MAX(idUser) As last_id FROM utilisateur");
        $stmt->execute();
        $row = $stmt->fetch();
        $new_id = ($row['last_id'] ?? 0) + 1;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $pdo->prepare("INSERT INTO utilisateur (idUser, nomUser, prenomUser, mailUser, motPasse) VALUES (?,?,?,?,?)");
        if($stmt->execute([$new_id, $last_name, $first_name, $email, $hashedPassword])) {
            $_SESSION['utilisateur_id']= $new_id;
            $_SESSION['utilisateur_name'] = "$last_name $first_name";

            header('Location: index.php');
            exit();
        } else {
            $error = 'An error occurred while creating the account. Please try again later.';
    
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sign-up.css?v=<?php echo time(); ?>">
    <title>Sign Up</title>
</head>
<body>
    <div class="signup-container">
        <h2 class="form-heading">Create an Account</h2>
        <form action="" method="POST">
            <input type="text" name="last_name" placeholder="Last name" required>
            <input type="text" name="first_name" placeholder="First name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
