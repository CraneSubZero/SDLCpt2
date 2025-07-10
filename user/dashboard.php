<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch tasks with proper sanitization
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
    <div class="neon-title">To Do Task</div>
    <div class="neon-subtitle">Welcome, <?= htmlspecialchars($username) ?></div>
    <div class="neon-clock" id="clock"></div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="color:#00ff7f; text-align:center; margin:10px; padding:10px; background:#1a102a; border:2px solid #00ff7f; border-radius:8px;">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="color:#ff003c; text-align:center; margin:10px; padding:10px; background:#1a102a; border:2px solid #ff003c; border-radius:8px;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <div class="neon-box">
        <h2 style="color:#00fff7;">Add New Task</h2>
        <form method="post" action="add_task.php">
            <div class="neon-label">Task Title</div>
            <input class="input-neon" type="text" name="title" required maxlength="100">
            <div class="neon-label">Description</div>
            <textarea class="input-neon" name="description" maxlength="1000"></textarea>
            <div class="neon-label">Category</div>
            <select class="input-neon" name="category">
                <option value="Personal">Personal</option>
                <option value="Work">Work</option>
                <option value="Education">Education</option>
                <option value="Health">Health</option>
                <option value="Finance">Finance</option>
            </select>
            <div class="neon-label">Priority</div>
            <select class="input-neon" name="priority">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
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
        <?php if ($tasks->num_rows > 0): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="neon-task-item <?= htmlspecialchars($task['status']) ?> <?= htmlspecialchars($task['priority']) ?>">
                    <div style="font-size:1.1rem; color:#00fff7; font-weight:bold;">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>
                    <div><?= htmlspecialchars($task['description']) ?></div>
                    <div style="margin:5px 0;">
                        <span class="neon-label" style="background:#a0f; color:#fff; border-radius:6px; padding:2px 8px; font-size:0.9em;"> <?= htmlspecialchars($task['category']) ?> </span>
                        <span class="neon-priority-<?= strtolower(htmlspecialchars($task['priority'])) ?>"> <?= strtoupper(htmlspecialchars($task['priority'])) ?> </span>
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
                        <button class="neon-btn" style="background:#ff003c; color:#fff;" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; color:#aaa; padding:20px;">
                No tasks found. Create your first task above!
            </div>
        <?php endif; ?>
    </div>
    <div style="text-align:center; margin:30px;">
        <a href="../logout.php" class="neon-btn">Logout</a>
    </div>
</body>
</html> 