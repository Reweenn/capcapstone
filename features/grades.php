<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../loginn.php");
    exit();
}
include '../db.php';

// Fetch grades for the logged-in student
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT subject, grade, created_at FROM grades WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Insert sample grades for demonstration (remove this block in production)
$insert_stmt = $conn->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
$sample_grades = [
    [1, 'Math', 'A'],
    [1, 'Science', 'B+'],
    [1, 'History', 'A-']
];
foreach ($sample_grades as $grade) {
    $insert_stmt->bind_param("iss", ...$grade);
    $insert_stmt->execute();
}
$insert_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container {
            width: 600px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Grades</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Date Recorded</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo htmlspecialchars($row['grade']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <a href="features/grades.php" style="text-decoration: none; background: #007bff; color: #fff; padding: 10px 20px; border-radius: 5px;">View Grades</a>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>