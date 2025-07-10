<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$task_id = intval($_GET['id']);

// Validate task ownership
$stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $priority = sanitize($_POST['priority']);
    $due_date = sanitize($_POST['due_date']);
    
    // Validate input
    $errors = [];
    
    if (empty($title) || strlen($title) > 100) {
        $errors[] = 'Title is required and must be less than 100 characters.';
    }
    
    if (strlen($description) > 1000) {
        $errors[] = 'Description must be less than 1000 characters.';
    }
    
    $valid_categories = ['Personal', 'Work', 'Education', 'Health', 'Finance'];
    if (!in_array($category, $valid_categories)) {
        $errors[] = 'Invalid category selected.';
    }
    
    $valid_priorities = ['Low', 'Medium', 'High'];
    if (!in_array($priority, $valid_priorities)) {
        $errors[] = 'Invalid priority selected.';
    }
    
    // Validate date if provided
    if (!empty($due_date)) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $due_date);
        if (!$date_obj || $date_obj->format('Y-m-d') !== $due_date) {
            $errors[] = 'Invalid date format.';
        }
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare('UPDATE tasks SET title = ?, description = ?, category = ?, priority = ?, due_date = ? WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ssssssi', $title, $description, $category, $priority, $due_date, $task_id, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Task updated successfully!';
            // Refresh task data
            $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $task_id, $user_id);
            $stmt->execute();
            $task = $stmt->get_result()->fetch_assoc();
        } else {
            $error = 'Failed to update task. Please try again.';
        }
    } else {
        $error = implode(' ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task - NeonTask</title>
    <link rel="stylesheet" href="../assets/css/neon.css">
</head>
<body>
    <div class="neon-title">Edit Task</div>
    <div class="neon-subtitle">Update Your Task</div>
    <div class="neon-box">
        <?php if ($error): ?>
            <div style="color:#ff003c; margin-bottom:15px;"> <?= $error ?> </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="color:#00ff7f; margin-bottom:15px;"> <?= $success ?> </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="neon-label">Task Title</div>
            <input class="input-neon" type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required maxlength="100">
            
            <div class="neon-label">Description</div>
            <textarea class="input-neon" name="description" maxlength="1000"><?= htmlspecialchars($task['description']) ?></textarea>
            
            <div class="neon-label">Category</div>
            <select class="input-neon" name="category">
                <option value="Personal" <?= $task['category']=='Personal'?'selected':'' ?>>Personal</option>
                <option value="Work" <?= $task['category']=='Work'?'selected':'' ?>>Work</option>
                <option value="Education" <?= $task['category']=='Education'?'selected':'' ?>>Education</option>
                <option value="Health" <?= $task['category']=='Health'?'selected':'' ?>>Health</option>
                <option value="Finance" <?= $task['category']=='Finance'?'selected':'' ?>>Finance</option>
            </select>
            
            <div class="neon-label">Priority</div>
            <select class="input-neon" name="priority">
                <option value="Low" <?= $task['priority']=='Low'?'selected':'' ?>>Low</option>
                <option value="Medium" <?= $task['priority']=='Medium'?'selected':'' ?>>Medium</option>
                <option value="High" <?= $task['priority']=='High'?'selected':'' ?>>High</option>
            </select>
            
            <div class="neon-label">Due Date</div>
            <input class="input-neon" type="date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>">
            
            <button class="neon-btn" type="submit">Update Task</button>
            <a href="dashboard.php" class="neon-btn" style="margin-left:10px;">Cancel</a>
        </form>
    </div>
</body>
</html> 