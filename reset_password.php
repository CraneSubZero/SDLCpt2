<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

$error = '';
$success = '';
$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';

// Validate token
if (!$token) {
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare('SELECT id, username FROM users WHERE reset_token = ? AND reset_expires > NOW()');
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $error = 'Invalid or expired reset token. Please request a new password reset.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!validate_password($password)) {
            $error = 'Password must be at least 8 characters long and contain uppercase, lowercase, and numeric characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Update password and clear reset token
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
            $stmt->bind_param('si', $hashed_password, $user['id']);
            $stmt->execute();
            
            $success = 'Password has been reset successfully. You can now login with your new password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - NeonTask</title>
    <link rel="stylesheet" href="assets/css/neon.css">
</head>
<body>
    <div class="neon-title">Reset Password</div>
    <div class="neon-subtitle">Set Your New Password</div>
    <div class="neon-box">
        <?php if ($error): ?>
            <div style="color:#ff003c; margin-bottom:15px;"> <?= $error ?> </div>
            <div style="margin-top:10px;">
                <a href="forgot_password.php" style="color:#00fff7;">Request New Reset Link</a>
            </div>
        <?php elseif ($success): ?>
            <div style="color:#00ff7f; margin-bottom:15px;"> <?= $success ?> </div>
            <div style="margin-top:10px;">
                <a href="index.php" class="neon-btn">Go to Login</a>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="neon-label">New Password</div>
                <input class="input-neon" type="password" name="password" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                       title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                <div class="neon-label">Confirm New Password</div>
                <input class="input-neon" type="password" name="confirm_password" required>
                <button class="neon-btn" type="submit">Reset Password</button>
                <div style="margin-top:10px;">
                    <a href="index.php" style="color:#00fff7;">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html> 