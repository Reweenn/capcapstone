<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../loginn.php");
    exit();
}
include '../db.php';

// Fetch student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT name, email, grade, created_at FROM students WHERE id = ?");
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
    <title>Profile</title>
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
        .info { margin-top: 20px; }
        .info label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .info input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <div class="info">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" readonly>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" readonly>

            <label for="grade">Grade:</label>
            <input type="text" id="grade" name="grade" value="<?php echo htmlspecialchars($student['grade']); ?>" readonly>

            <label for="created_at">Member Since:</label>
            <input type="text" id="created_at" name="created_at" value="<?php echo htmlspecialchars($student['created_at']); ?>" readonly>
        </div>
    </div>
</body>
</html>