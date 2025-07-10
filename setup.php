<?php
require_once 'includes/db.php';

// Check if database is already set up
$tables_exist = $conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0;

if ($tables_exist) {
    echo "<div style='color:#ff003c; text-align:center; padding:20px;'>Database already set up. Please delete this file for security.</div>";
    exit();
}

// Create tables
$schema = file_get_contents('includes/schema.sql');
$statements = explode(';', $schema);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        $conn->query($statement);
    }
}

// Create admin user
$admin_username = 'admin';
$admin_email = 'admin@neontask.com';
$admin_password = password_hash('Admin123!', PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "admin")');
$stmt->bind_param('sss', $admin_username, $admin_email, $admin_password);
$stmt->execute();

echo "<div style='color:#00ff7f; text-align:center; padding:20px; background:#1a102a; border:2px solid #00ff7f; border-radius:8px; margin:20px;'>";
echo "<h2>NeonTask Setup Complete!</h2>";
echo "<p>Database tables have been created successfully.</p>";
echo "<p><strong>Admin Account Created:</strong></p>";
echo "<p>Username: <strong>admin</strong></p>";
echo "<p>Password: <strong>Admin123!</strong></p>";
echo "<p><strong>IMPORTANT:</strong> Please change the admin password after first login!</p>";
echo "<p><a href='index.php' style='color:#00fff7;'>Go to Login</a></p>";
echo "</div>";

echo "<div style='color:#ff9900; text-align:center; padding:20px; background:#1a102a; border:2px solid #ff9900; border-radius:8px; margin:20px;'>";
echo "<h3>Security Notice</h3>";
echo "<p>Please delete this setup.php file after successful installation for security reasons.</p>";
echo "</div>";
?> 