<?php
// Enhanced sanitization functions
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function sanitize_sql($data) {
    global $conn;
    if (is_array($data)) {
        return array_map('sanitize_sql', $data);
    }
    return $conn->real_escape_string(trim($data));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_username($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function validate_password($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// MFA functions
function generate_mfa_code() {
    return rand(100000, 999999);
}

function send_mfa_code($email, $code) {
    // Simulate sending email (for demo, just return true)
    // In production, use PHPMailer or similar
    return true;
}

// Lockout management
function check_account_locked($user_id) {
    global $conn;
    $stmt = $conn->prepare('SELECT locked_until FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['locked_until']) {
        if (strtotime($user['locked_until']) > time()) {
            return true; // Account is locked
        } else {
            // Lock has expired, reset it
            reset_failed_attempts($user_id);
        }
    }
    return false;
}

function increment_failed_attempts($user_id) {
    global $conn;
    $stmt = $conn->prepare('UPDATE users SET failed_attempts = failed_attempts + 1 WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    
    // Check if we should lock the account
    $stmt = $conn->prepare('SELECT failed_attempts FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user['failed_attempts'] >= 5) {
        // Lock account for 15 minutes
        $lock_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $stmt = $conn->prepare('UPDATE users SET locked_until = ? WHERE id = ?');
        $stmt->bind_param('si', $lock_until, $user_id);
        $stmt->execute();
    }
}

function reset_failed_attempts($user_id) {
    global $conn;
    $stmt = $conn->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
}

// Password reset functions
function generate_reset_token() {
    return bin2hex(random_bytes(32));
}

function send_reset_email($email, $token) {
    // Simulate sending email (for demo, just return true)
    // In production, use PHPMailer or similar
    return true;
}

// CSRF protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Rate limiting
function check_rate_limit($ip, $action, $max_attempts = 5, $time_window = 300) {
    global $conn;
    
    // Create rate_limits table if it doesn't exist
    $conn->query("CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        action VARCHAR(50) NOT NULL,
        attempts INT DEFAULT 1,
        first_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_action (ip_address, action)
    )");
    
    $stmt = $conn->prepare('SELECT * FROM rate_limits WHERE ip_address = ? AND action = ? AND first_attempt > DATE_SUB(NOW(), INTERVAL ? SECOND)');
    $stmt->bind_param('ssi', $ip, $action, $time_window);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $rate_limit = $result->fetch_assoc();
        if ($rate_limit['attempts'] >= $max_attempts) {
            return false; // Rate limit exceeded
        }
        
        // Increment attempts
        $stmt = $conn->prepare('UPDATE rate_limits SET attempts = attempts + 1 WHERE id = ?');
        $stmt->bind_param('i', $rate_limit['id']);
        $stmt->execute();
    } else {
        // First attempt
        $stmt = $conn->prepare('INSERT INTO rate_limits (ip_address, action) VALUES (?, ?)');
        $stmt->bind_param('ss', $ip, $action);
        $stmt->execute();
    }
    
    return true;
}

// Clean up old rate limit records
function cleanup_rate_limits() {
    global $conn;
    $conn->query('DELETE FROM rate_limits WHERE first_attempt < DATE_SUB(NOW(), INTERVAL 1 HOUR)');
}
?> 