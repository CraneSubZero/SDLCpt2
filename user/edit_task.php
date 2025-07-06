<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();
$user_id = $_SESSION['user_id'];
$task_id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT * FROM tasks WHERE id=? AND user_id=?');
$stmt->bind_param('ii', $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
if (!$task) {
    header('Location: dashboard.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $category = htmlspecialchars(trim($_POST['category']));
    $priority = htmlspecialchars(trim($_POST['priority']));
    $due_date = $_POST['due_date'];
    $stmt = $conn->prepare('UPDATE tasks SET title=?, description=?, category=?, priority=?, due_date=? WHERE id=? AND user_id=?');
    $stmt->bind_param('ssssssi', $title, $description, $category, $priority, $due_date, $task_id, $user_id);
    $stmt->execute();
    header('Location: dashboard.php');
    exit();
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
    <div class="neon-box">
        <form method="post">
            <div class="neon-label">Task Title</div>
            <input class="input-neon" type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            <div class="neon-label">Description</div>
            <textarea class="input-neon" name="description"><?= htmlspecialchars($task['description']) ?></textarea>
            <div class="neon-label">Category</div>
            <select class="input-neon" name="category">
                <option <?= $task['category']=='Personal'?'selected':'' ?>>Personal</option>
                <option <?= $task['category']=='Work'?'selected':'' ?>>Work</option>
                <option <?= $task['category']=='Education'?'selected':'' ?>>Education</option>
            </select>
            <div class="neon-label">Priority</div>
            <select class="input-neon" name="priority">
                <option <?= $task['priority']=='Low'?'selected':'' ?>>Low</option>
                <option <?= $task['priority']=='Medium'?'selected':'' ?>>Medium</option>
                <option <?= $task['priority']=='High'?'selected':'' ?>>High</option>
            </select>
            <div class="neon-label">Due Date</div>
            <input class="input-neon" type="date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>">
            <button class="neon-btn" type="submit">Update Task</button>
            <a href="dashboard.php" class="neon-btn">Cancel</a>
        </form>
    </div>
</body>
</html> 