<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../loginn.php");
    exit();
}
include '../db.php';

// Insert sample announcements (this should be done only once, consider using a separate script for initial data population)
$insertStmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
$insertStmt->bind_param("ss", $title, $content);

// Sample data
$announcements = [
    ['Welcome Back!', 'We are excited to welcome all students to the new semester.'],
    ['Exam Schedule', 'The exam schedule has been released. Please check your email for details.'],
    ['Holiday Notice', 'The school will be closed on September 15th for a public holiday.']
];

// Insert each announcement
foreach ($announcements as $announcement) {
    list($title, $content) = $announcement;
    $insertStmt->execute();
}

$insertStmt->close();

// Fetch announcements
$stmt = $conn->prepare("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
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
        .announcement {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .announcement h3 {
            margin: 0;
            font-size: 18px;
            color: #007bff;
        }
        .announcement p {
            margin: 10px 0;
            font-size: 14px;
        }
        .announcement .date {
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Announcements</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="announcement">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['content']); ?></p>
                    <p class="date">Posted on: <?php echo htmlspecialchars($row['created_at']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>
    </div>
</body>
</html>