<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare('SELECT id FROM users WHERE username=? OR email=?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = 'Username or email already exists.';
    } else {
        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NeonTask</title>
    <link rel="stylesheet" href="assets/css/neon.css">
</head>
<body>
    <div class="neon-title">Register NeonTask</div>
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Username</div>
            <input class="input-neon" type="text" name="username" required>
            <div class="neon-label">Email</div>
            <input class="input-neon" type="email" name="email" required>
            <div class="neon-label">Password</div>
            <input class="input-neon" type="password" name="password" required>
            <button class="neon-btn" type="submit">Register</button>
            <div style="margin-top:10px;">
                <a href="index.php" style="color:#00fff7;">Back to Login</a>
            </div>
            <?php if ($error): ?>
                <div style="color:#ff003c; margin-top:10px;"> <?= $error ?> </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html> 