<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE username=?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Generate MFA code
            $code = generate_mfa_code();
            $expires = date('Y-m-d H:i:s', strtotime('+2 minutes'));
            $stmt2 = $conn->prepare('UPDATE users SET mfa_code=?, mfa_expires=? WHERE id=?');
            $stmt2->bind_param('ssi', $code, $expires, $user['id']);
            $stmt2->execute();
            // Simulate sending code
            send_mfa_code($user['email'], $code);
            $_SESSION['pending_user'] = $user['id'];
            header('Location: mfa.php');
            exit();
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NeonTask Login</title>
    <link rel="stylesheet" href="assets/css/neon.css">
</head>
<body>
    <div class="neon-title">NeonTask</div>
    <div class="neon-subtitle">Futuristic Task Management System</div>
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Username</div>
            <input class="input-neon" type="text" name="username" required>
            <div class="neon-label">Password</div>
            <input class="input-neon" type="password" name="password" required>
            <button class="neon-btn" type="submit">Login</button>
            <div style="margin-top:10px;">
                <a href="register.php" style="color:#00fff7;">Create an account</a>
            </div>
            <?php if ($error): ?>
                <div style="color:#ff003c; margin-top:10px;"> <?= $error ?> </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html> 