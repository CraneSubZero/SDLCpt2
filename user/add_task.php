<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $category = htmlspecialchars(trim($_POST['category']));
    $priority = htmlspecialchars(trim($_POST['priority']));
    $due_date = $_POST['due_date'];
    $stmt = $conn->prepare('INSERT INTO tasks (user_id, title, description, category, priority, due_date) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssss', $user_id, $title, $description, $category, $priority, $due_date);
    $stmt->execute();
}
header('Location: dashboard.php');
exit(); 