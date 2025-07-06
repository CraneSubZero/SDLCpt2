<?php
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function generate_mfa_code() {
    return rand(100000, 999999);
}
function send_mfa_code($email, $code) {
    // Simulate sending email (for demo, just return true)
    return true;
}
?> 