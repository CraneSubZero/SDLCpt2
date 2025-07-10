<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = intval($_POST['task_id']);
    $action = sanitize($_POST['action']);
    
    if ($action === 'delete') {
        // Validate task ownership before deletion
        $stmt = $conn->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $task_id, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Task deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete task.';
            }
        } else {
            $_SESSION['error_message'] = 'Task not found or access denied.';
        }
    } else {
        $_SESSION['error_message'] = 'Invalid action.';
    }
}

header('Location: dashboard.php');
exit(); 