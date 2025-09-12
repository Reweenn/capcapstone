<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: loginn.php");
    exit();
}
include 'db.php';

// Fetch student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT name, email, grade, status, created_at FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container {
            width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        .info { margin: 20px 0; }
        .info label { font-weight: bold; }
        .features { margin-top: 30px; }
        .features a {
            display: block;
            margin: 10px 0;
            color: #007bff;
            text-decoration: none;
        }
        .features a:hover { text-decoration: underline; }
        .logout-btn {
            display: block;
            width: 100%;
            margin-top: 30px;
            padding: 10px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }
        .logout-btn:hover { background: #b52a37; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h2>
        <div class="info">
            <p><label>Email:</label> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><label>Grade:</label> <?php echo htmlspecialchars($student['grade']); ?></p>
            <p><label>Status:</label> <?php echo htmlspecialchars($student['status']); ?></p>
            <p><label>Member Since:</label> <?php echo htmlspecialchars($student['created_at']); ?></p>
        </div>
        <div class="features">
            <h3>Student Features</h3>
            <a href="features/grades.php">View Grades</a>
            <a href="#">Class Schedule</a>
            <a href="features/profile.php">Profile</a>
            <a href="#">Account Balance</a>
            <a href="features/announcement.php">Announcement</a>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>