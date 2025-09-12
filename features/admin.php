<?php
// admin.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}
include '../db.php'; // Correct relative path to db.php

// Fetch statistics
$total_students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$total_staff = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(amount) AS total FROM tuitions WHERE status = 'Paid'")->fetch_assoc()['total'];
$recent_enrollees = $conn->query("SELECT name, grade, created_at FROM students ORDER BY created_at DESC LIMIT 5");

// Handle tuition data submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $amount = trim($_POST['amount']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("INSERT INTO tuitions (student_id, amount, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $amount, $status);

    if ($stmt->execute()) {
        $message = "Tuition data added successfully!";
    } else {
        $message = "Error adding tuition data: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch tuition data
$tuitions = $conn->query("SELECT tuitions.id, students.name, tuitions.amount, tuitions.status, tuitions.created_at 
                          FROM tuitions 
                          JOIN students ON tuitions.student_id = students.id 
                          ORDER BY tuitions.created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <!-- Include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        .table th {
            background-color: #007bff;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stats .card {
            flex: 1;
            text-align: center;
            padding: 20px;
        }
        .recent-enrollees {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-mortarboard-fill me-2"></i> Admin Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Payments</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2 class="text-center my-4">Admin Dashboard</h2>

        <!-- Statistics -->
        <div class="stats">
            <div class="card">
                <h4>Total Students</h4>
                <p class="fs-3"><?php echo $total_students; ?></p>
            </div>
            <div class="card">
                <h4>Total Staff</h4>
                <p class="fs-3"><?php echo $total_staff; ?></p>
            </div>
            <div class="card">
                <h4>Total School Income</h4>
                <p class="fs-3">P<?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>

        <!-- Recent Enrollees -->
        <div class="recent-enrollees">
            <h4>Recent Enrollees</h4>
            <ul class="list-group">
                <?php while ($row = $recent_enrollees->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong> - Grade: <?php echo htmlspecialchars($row['grade']); ?>
                        <span class="text-muted">Enrolled on <?php echo htmlspecialchars($row['created_at']); ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Tuition Form -->
        <div class="card my-4">
            <div class="card-header">Add Tuition</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID:</label>
                        <input type="number" id="student_id" name="student_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Tuition Amount:</label>
                        <input type="number" id="amount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Tuition</button>
                </form>
            </div>
        </div>

        <!-- Tuition Records -->
        <div class="card">
            <div class="card-header">Tuition Records</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $tuitions->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
