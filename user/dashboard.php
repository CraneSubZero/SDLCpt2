<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
// Fetch tasks
$stmt = $conn->prepare('SELECT * FROM tasks WHERE user_id=? ORDER BY due_date ASC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
// Stats
$total = $completed = $pending = 0;
foreach ($tasks as $task) {
    $total++;
    if ($task['status'] === 'Completed') $completed++;
    else $pending++;
}
$completion = $total ? round(($completed/$total)*100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NeonTask</title>
    <link rel="stylesheet" href="../assets/css/neon.css">
    <script src="../assets/js/clock.js"></script>
    <script src="../assets/js/main.js"></script>
</head>
<body onload="startClock('clock')">
    <div class="neon-title">NeonTask</div>
    <div class="neon-subtitle">Welcome, <?= htmlspecialchars($username) ?></div>
    <div class="neon-clock" id="clock"></div>
    <div class="neon-box">
        <h2 style="color:#00fff7;">Add New Task</h2>
        <form method="post" action="add_task.php">
            <div class="neon-label">Task Title</div>
            <input class="input-neon" type="text" name="title" required>
            <div class="neon-label">Description</div>
            <textarea class="input-neon" name="description"></textarea>
            <div class="neon-label">Category</div>
            <select class="input-neon" name="category">
                <option>Personal</option>
                <option>Work</option>
                <option>Education</option>
            </select>
            <div class="neon-label">Priority</div>
            <select class="input-neon" name="priority">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
            </select>
            <div class="neon-label">Due Date</div>
            <input class="input-neon" type="date" name="due_date">
            <button class="neon-btn" type="submit">Add Task</button>
        </form>
    </div>
    <div class="neon-stats">
        <div> <b><?= $total ?></b> Total Tasks | <b><?= $completed ?></b> Completed | <b><?= $pending ?></b> Pending | <b><?= $completion ?>%</b> Completion Rate </div>
    </div>
    <div class="neon-task-list">
        <div style="font-size:1.3rem; color:#00ff7f; font-weight:bold;">Task List</div>
        <div style="margin-bottom:10px;">
            <button class="neon-filter-btn active" data-filter="all">All Tasks</button>
            <button class="neon-filter-btn" data-filter="Pending">Pending</button>
            <button class="neon-filter-btn" data-filter="Completed">Completed</button>
            <button class="neon-filter-btn" data-filter="High">High Priority</button>
            <button class="neon-filter-btn" data-filter="Medium">Medium Priority</button>
            <button class="neon-filter-btn" data-filter="Low">Low Priority</button>
        </div>
        <?php foreach ($tasks as $task): ?>
            <div class="neon-task-item <?= $task['status'] ?> <?= $task['priority'] ?>">
                <div style="font-size:1.1rem; color:#00fff7; font-weight:bold;">
                    <?= htmlspecialchars($task['title']) ?>
                </div>
                <div><?= htmlspecialchars($task['description']) ?></div>
                <div style="margin:5px 0;">
                    <span class="neon-label" style="background:#a0f; color:#fff; border-radius:6px; padding:2px 8px; font-size:0.9em;"> <?= htmlspecialchars($task['category']) ?> </span>
                    <span class="neon-priority-<?= strtolower($task['priority']) ?>"> <?= strtoupper($task['priority']) ?> </span>
                </div>
                <div style="font-size:0.9em; color:#aaa;">Due: <?= htmlspecialchars($task['due_date']) ?></div>
                <form method="post" action="update_task.php" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <?php if ($task['status'] === 'Pending'): ?>
                        <button class="neon-btn" name="action" value="complete">Complete</button>
                    <?php else: ?>
                        <button class="neon-btn" name="action" value="undo">Undo</button>
                    <?php endif; ?>
                </form>
                <a href="edit_task.php?id=<?= $task['id'] ?>" class="neon-btn">Edit</a>
                <form method="post" action="delete_task.php" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <button class="neon-btn" style="background:#ff003c; color:#fff;" name="action" value="delete">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin:30px;">
        <a href="../logout.php" class="neon-btn">Logout</a>
    </div>
</body>
</html> 