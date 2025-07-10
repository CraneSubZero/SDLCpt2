<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = intval($_POST['task_id']);
    $action = sanitize($_POST['action']);
    
    // Validate task ownership
    $stmt = $conn->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        if ($action === 'complete') {
            $stmt = $conn->prepare('UPDATE tasks SET status = "Completed" WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $task_id, $user_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Task marked as completed!';
            } else {
                $_SESSION['error_message'] = 'Failed to update task.';
            }
        } elseif ($action === 'undo') {
            $stmt = $conn->prepare('UPDATE tasks SET status = "Pending" WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $task_id, $user_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Task marked as pending!';
            } else {
                $_SESSION['error_message'] = 'Failed to update task.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid action.';
        }
    } else {
        $_SESSION['error_message'] = 'Task not found or access denied.';
    }
}

header('Location: dashboard.php');
exit(); 