<?php
require '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $title = $data['title'];
    $description = $data['description'];
    $due_date = $data['due_date'];
    $status = $data['status'];

    // Fetch the latest task ID from the database
    try {
        $stmt = $pdo->query("SELECT MAX(task_id) AS max_task_id FROM tasks");
        $row = $stmt->fetch();
        $latestTaskId = $row['max_task_id'];

        // Extract the numeric part of the task ID and increment it
        if ($latestTaskId) {
            $numericPart = (int) substr($latestTaskId, 4); // Extract numeric part after "TASK"
            $nextNumericPart = $numericPart + 1;
        } else {
            $nextNumericPart = 1; // If no tasks exist, start with 1
        }

        // Format the new task ID (e.g., TASK001)
        $task_id = "TASK" . str_pad($nextNumericPart, 4, "0", STR_PAD_LEFT);

        // Insert task into the database
        $stmt = $pdo->prepare("INSERT INTO tasks (task_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$task_id, $title, $description, $due_date, $status]);

        // Return success response with the generated task ID
        echo json_encode(['success' => true, 'task_id' => $task_id]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>