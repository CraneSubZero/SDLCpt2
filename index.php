<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Clean up old rate limits
cleanup_rate_limits();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting
    $client_ip = $_SERVER['REMOTE_ADDR'];
    if (!check_rate_limit($client_ip, 'login', 5, 60)) {
        $error = 'Too many login attempts. Please try again later.';
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        
        if (!validate_username($username)) {
            $error = 'Invalid username format.';
        } else {
            $stmt = $conn->prepare('SELECT * FROM users WHERE username=?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Check if account is locked
                if (check_account_locked($user['id'])) {
                    $error = 'Account is temporarily locked due to too many failed attempts. Please try again later.';
                } else {
                    if (password_verify($password, $user['password'])) {
                        // Success: reset failed attempts and generate MFA
                        reset_failed_attempts($user['id']);
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
                    } else {
                        // Increment failed attempts
                        increment_failed_attempts($user['id']);
                        $error = 'Invalid username or password.';
                    }
                }
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
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
    <div class="neon-title">To Do Task</div>
    <div class="neon-subtitle">Task Management System</div>
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Username</div>
            <input class="input-neon" type="text" name="username" required>
            <div class="neon-label">Password</div>
            <input class="input-neon" type="password" name="password" required>
            <button class="neon-btn" type="submit">Login</button>
            <div style="margin-top:10px;">
                <a href="register.php" style="color:#00fff7;">Create an account</a> | 
                <a href="forgot_password.php" style="color:#00fff7;">Forgot Password?</a>
            </div>
            <?php if ($error): ?>
                <div style="color:#ff003c; margin-top:10px;"> <?= $error ?> </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color:#00ff7f; margin-top:10px;"> <?= $success ?> </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html> 