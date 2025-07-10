<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();
require_admin();

// Fetch all users and their stats with proper sanitization
$stmt = $conn->prepare('SELECT id, username, email, created_at, failed_attempts FROM users WHERE role = "user" ORDER BY created_at DESC');
$stmt->execute();
$users = $stmt->get_result();

// Get system statistics
$total_users = $conn->query('SELECT COUNT(*) as count FROM users WHERE role = "user"')->fetch_assoc()['count'];
$total_tasks = $conn->query('SELECT COUNT(*) as count FROM tasks')->fetch_assoc()['count'];
$completed_tasks = $conn->query('SELECT COUNT(*) as count FROM tasks WHERE status = "Completed"')->fetch_assoc()['count'];
$pending_tasks = $conn->query('SELECT COUNT(*) as count FROM tasks WHERE status = "Pending"')->fetch_assoc()['count'];
$overall_completion = $total_tasks ? round(($completed_tasks/$total_tasks)*100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NeonTask</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/neon.css">
    <script src="../assets/js/clock.js"></script>
</head>
<body onload="startClock('admin-clock')">
    <div class="neon-title">Admin Dashboard</div>
    <div class="neon-subtitle">System Overview</div>
    <div class="neon-clock" id="admin-clock"></div>
    
    <!-- System Statistics -->
    <div class="neon-stats">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
            <div style="text-align:center;">
                <div style="font-size:2rem; color:#00fff7;"><?= $total_users ?></div>
                <div>Total Users</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:2rem; color:#00ff7f;"><?= $total_tasks ?></div>
                <div>Total Tasks</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:2rem; color:#ff9900;"><?= $completed_tasks ?></div>
                <div>Completed Tasks</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:2rem; color:#ff003c;"><?= $pending_tasks ?></div>
                <div>Pending Tasks</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:2rem; color:#00fff7;"><?= $overall_completion ?>%</div>
                <div>Overall Completion</div>
            </div>
        </div>
    </div>
    
    <div class="neon-box">
        <h2 style="color:#00ff7f;">User Task Monitoring</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Total Tasks</th>
                    <th>Completed</th>
                    <th>Pending</th>
                    <th>Completion Rate</th>
                    <th>Failed Logins</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()):
                    $stmt = $conn->prepare('SELECT status FROM tasks WHERE user_id = ?');
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
                    <td style="color:#00ff7f;"><?= $completed ?></td>
                    <td style="color:#ff9900;"><?= $pending ?></td>
                    <td style="color:#00fff7;"><?= $completion ?>%</td>
                    <td style="color:<?= $user['failed_attempts'] > 0 ? '#ff003c' : '#00ff7f' ?>;"><?= $user['failed_attempts'] ?></td>
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div style="text-align:center; margin:30px;">
        <a href="../logout.php" class="neon-btn">Logout</a>
    </div>
</body>
</html> 