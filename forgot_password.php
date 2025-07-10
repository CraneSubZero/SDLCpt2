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
    if (!check_rate_limit($client_ip, 'forgot_password', 3, 600)) {
        $error = 'Too many password reset attempts. Please try again later.';
    } else {
        $email = sanitize($_POST['email']);
        
        if (!validate_email($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            $stmt = $conn->prepare('SELECT id, username FROM users WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Generate reset token
                $token = generate_reset_token();
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $stmt = $conn->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
                $stmt->bind_param('ssi', $token, $expires, $user['id']);
                $stmt->execute();
                
                // Send reset email (simulated)
                send_reset_email($email, $token);
                
                $success = 'Password reset instructions have been sent to your email address.';
            } else {
                // Don't reveal if email exists or not for security
                $success = 'If the email address exists in our system, password reset instructions have been sent.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - NeonTask</title>
    <link rel="stylesheet" href="assets/css/neon.css">
</head>
<body>
    <div class="neon-title">Forgot Password</div>
    <div class="neon-subtitle">Reset Your Password</div>
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Email Address</div>
            <input class="input-neon" type="email" name="email" required>
            <button class="neon-btn" type="submit">Send Reset Link</button>
            <div style="margin-top:10px;">
                <a href="index.php" style="color:#00fff7;">Back to Login</a>
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