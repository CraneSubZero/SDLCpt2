<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = intval($_POST['task_id']);
    $action = $_POST['action'];
    if ($action === 'complete') {
        $stmt = $conn->prepare('UPDATE tasks SET status="Completed" WHERE id=? AND user_id=?');
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();
    } elseif ($action === 'undo') {
        $stmt = $conn->prepare('UPDATE tasks SET status="Pending" WHERE id=? AND user_id=?');
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();
    }
}
header('Location: dashboard.php');
exit(); 