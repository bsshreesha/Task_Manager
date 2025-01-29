<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch task details
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="Assets/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
    <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Edit Task</h3>
            </div>
            <div class="panel-body">
        <form id="editTaskForm">
            <input type="hidden" name="id" value="<?= $task['id'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required><br>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control"><?= htmlspecialchars($task['description']) ?></textarea><br>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date']) ?>" required><br>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select><br>
            </div>
            <button type="submit" class="btn btn-success">Update Task</button>
        </form>
        </div>
        </div>
    </div>

    

    <script>
        // Handle form submission using fetch API
        document.getElementById('editTaskForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = {
                id: document.querySelector('input[name="id"]').value,
                title: document.querySelector('input[name="title"]').value,
                description: document.querySelector('textarea[name="description"]').value,
                due_date: document.querySelector('input[name="due_date"]').value,
                status: document.querySelector('select[name="status"]').value
            };

            fetch('api/update_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Task updated successfully!');
                    window.location.href = 'index.php';
                } else {
                    alert('Error updating task: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>