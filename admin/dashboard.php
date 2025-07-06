<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();
require_admin();
// Fetch all users and their stats
$users = $conn->query('SELECT id, username, email FROM users WHERE role="user"');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NeonTask</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="../assets/js/clock.js"></script>
</head>
<body onload="startClock('admin-clock')">
    <div class="admin-title">Admin Dashboard</div>
    <div class="admin-clock" id="admin-clock"></div>
    <div class="admin-stats-box">
        <h2 style="color:#00ff7f;">User Task Monitoring</h2>
        <table class="admin-table">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Total Tasks</th>
                <th>Completed</th>
                <th>Pending</th>
                <th>Completion Rate</th>
            </tr>
            <?php while ($user = $users->fetch_assoc()):
                $stmt = $conn->prepare('SELECT status FROM tasks WHERE user_id=?');
                $stmt->bind_param('i', $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $total = $completed = $pending = 0;
                foreach ($result as $task) {
                    $total++;
                    if ($task['status'] === 'Completed') $completed++;
                    else $pending++;
                }
                $completion = $total ? round(($completed/$total)*100) : 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $total ?></td>
                <td><?= $completed ?></td>
                <td><?= $pending ?></td>
                <td><?= $completion ?>%</td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <div style="text-align:center; margin:30px;">
        <a href="../logout.php" class="neon-btn">Logout</a>
    </div>
</body>
</html> 