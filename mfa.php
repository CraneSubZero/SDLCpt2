<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Clean up old rate limits
cleanup_rate_limits();

$error = '';
if (!isset($_SESSION['pending_user'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['pending_user'];
$stmt = $conn->prepare('SELECT * FROM users WHERE id=?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: index.php');
    exit();
}

$mfa_demo_code = $user['mfa_code']; // For demo display

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting for MFA attempts
    $client_ip = $_SERVER['REMOTE_ADDR'];
    if (!check_rate_limit($client_ip, 'mfa_verify', 5, 300)) {
        $error = 'Too many MFA attempts. Please try again later.';
    } else {
        $code = sanitize($_POST['code']);
        
        if ($user['mfa_code'] === $code && strtotime($user['mfa_expires']) > time()) {
            // Success: log in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            unset($_SESSION['pending_user']);
            
            // Clear MFA code
            $stmt2 = $conn->prepare('UPDATE users SET mfa_code=NULL, mfa_expires=NULL WHERE id=?');
            $stmt2->bind_param('i', $user['id']);
            $stmt2->execute();
            
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = 'Invalid or expired code.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MFA Verification - NeonTask</title>
    <link rel="stylesheet" href="assets/css/neon.css">
    <script src="assets/js/mfa.js"></script>
</head>
<body onload="startMFATimer(120, 'mfa-timer')">
    <div class="neon-title">Multi-Factor Authentication</div>
    <div class="neon-subtitle">Secure Your Account</div>
    <div class="neon-box">
        <!-- Demo MFA code display -->
        <div style="background:#10101a; color:#00fff7; border:2px dashed #00fff7; border-radius:8px; padding:10px; margin-bottom:15px; text-align:center; font-size:1.2em;">
            <b>Demo MFA Code:</b> <?= htmlspecialchars($mfa_demo_code) ?>
        </div>
        <form method="post">
            <div class="neon-label">Enter the 6-digit code sent to your email</div>
            <input class="input-neon" type="text" name="code" maxlength="6" required 
                   pattern="[0-9]{6}" title="Please enter a 6-digit code">
            <div style="margin:10px 0; color:#00fff7;">Time left: <span id="mfa-timer">02:00</span></div>
            <button class="neon-btn" type="submit">Verify</button>
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