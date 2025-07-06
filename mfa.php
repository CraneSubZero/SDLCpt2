<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Enter the 6-digit code sent to your email</div>
            <input class="input-neon" type="text" name="code" maxlength="6" required>
            <div style="margin:10px 0; color:#00fff7;">Time left: <span id="mfa-timer">02:00</span></div>
            <button class="neon-btn" type="submit">Verify</button>
            <?php if ($error): ?>
                <div style="color:#ff003c; margin-top:10px;"> <?= $error ?> </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html> 