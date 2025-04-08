<?php
session_start();
require "config.php";

// Check if the user is logged in
if (!isset($_SESSION['utilisateur_name'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['utilisateur_id'];

// Fetch user data
$sql = "SELECT idUser, nomUser, prenomUser, mailUser, motPasse FROM utilisateur WHERE idUser = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute([':userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // If password is provided, hash it
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If no password is provided, keep the current password
        $hashedPassword = $user['motPasse'];
    }

    // Update user information in the database
    $updateSql = "UPDATE utilisateur 
                  SET nomUser = :firstName, prenomUser = :lastName, mailUser = :email, motPasse = :password 
                  WHERE idUser = :userId";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':userId' => $userId
    ]);

    // Update session data
    $_SESSION['utilisateur_name'] = $firstName;

    // Redirect to the same page to show updated details
    header('Location: profile.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="profile-container">
        <h1>User Profile</h1>

        <!-- Display User Info -->
        <div class="user-info">
            <h2>Personal Information</h2>
            <form method="POST" action="profile.php">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($user['nomUser']); ?>" required>

                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($user['prenomUser']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['mailUser']); ?>" required>

                <label for="password">Password (leave empty to keep the current password):</label>
                <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($hashedPassword); ?>">

                <button type="submit" name="update">Update Information</button>
            </form>
        </div>
    </div>
</body>

</html>
