<?php
require 'config.php';

// Fetch all tasks
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY due_date ASC");
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="Assets/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

    <style>
        body { background-color:rgb(255, 255, 255); }
        .container { margin-top: 50px; }
        .panel { margin-bottom: 20px; }
        .panel-heading { padding: 10px 15px; background-color: #f8f9fa; border-bottom: 1px solid #ddd; }
        .panel-title { margin: 0; }
        .panel-body { padding: 15px; }
        .status-pending { color: red; font-weight: bold; }
        .status-in-progress { color: orange; font-weight: bold; }
        .status-completed { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Task Management System</h2>

        <!-- Add Task Panel -->
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Add Task</h3>
            </div>
            <div class="panel-body">
                <form id="addTaskForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required><br>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control"></textarea><br>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" required><br>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select><br>
                    </div>
                    <button type="submit" class="btn btn-success">Add Task</button>
                </form>
            </div>
        </div>

        <!-- Task List Panel -->
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Task List</h3>
            </div>
            <div class="panel-body">
                <table id="taskTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Task ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['task_id']) ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td><?= htmlspecialchars($task['due_date']) ?></td>
                                <td class="status-<?= strtolower(str_replace(' ', '-', $task['status'])) ?>">
                                    <?= htmlspecialchars($task['status']) ?>
                                </td>
                                <td>
                                    <a href="update_task.php?id=<?= $task['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#taskTable').DataTable({
                "scrollX": true,
                "autoWidth": false // This helps prevent auto width calculations that may interfere with scrolling
            });

            // Add Task Form Submission
            $('#addTaskForm').on('submit', function(event) {
                event.preventDefault();

                const formData = {
                    title: $('#title').val(),
                    description: $('#description').val(),
                    due_date: $('#due_date').val(),
                    status: $('#status').val()
                };

                fetch('api/create_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Task added successfully! Task ID:' ${data.task_id}'`);
                        window.location.reload();
                    } else {
                        alert('Error adding task: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>