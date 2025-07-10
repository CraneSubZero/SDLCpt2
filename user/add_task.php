<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
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
        $stmt = $conn->prepare('INSERT INTO tasks (user_id, title, description, category, priority, due_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssss', $user_id, $title, $description, $category, $priority, $due_date);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Task added successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to add task. Please try again.';
        }
    } else {
        $_SESSION['error_message'] = implode(' ', $errors);
    }
}

header('Location: dashboard.php');
exit(); 