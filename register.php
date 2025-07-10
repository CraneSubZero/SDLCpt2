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
    if (!check_rate_limit($client_ip, 'register', 3, 600)) {
        $error = 'Too many registration attempts. Please try again later.';
    } else {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (!validate_username($username)) {
            $error = 'Username must be 3-20 characters long and contain only letters, numbers, and underscores.';
        } elseif (!validate_email($email)) {
            $error = 'Please enter a valid email address.';
        } elseif (!validate_password($password)) {
            $error = 'Password must be at least 8 characters long and contain uppercase, lowercase, and numeric characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare('SELECT id FROM users WHERE username=? OR email=?');
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Username or email already exists.';
            } else {
                // Create user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $username, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success = 'Registration successful! You can now login.';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
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
    <div class="neon-subtitle">Create Your Account</div>
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Username</div>
            <input class="input-neon" type="text" name="username" required 
                   pattern="[a-zA-Z0-9_]{3,20}" 
                   title="3-20 characters, letters, numbers, and underscores only">
            
            <div class="neon-label">Email</div>
            <input class="input-neon" type="email" name="email" required>
            
            <div class="neon-label">Password</div>
            <input class="input-neon" type="password" name="password" required 
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                   title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
            
            <div class="neon-label">Confirm Password</div>
            <input class="input-neon" type="password" name="confirm_password" required>
            
            <button class="neon-btn" type="submit">Register</button>
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