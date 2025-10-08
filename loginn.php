<?php
include 'db.php';
session_start();

$message = "";
$show_signup = false;

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            header("Location: home.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Email not found!";
    }
    $stmt->close();
}

// Handle Signup (same logic as register.php, no username)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] === 'signup') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $grade = trim($_POST['grade']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $show_signup = true;
    } else {
        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already registered!";
            $show_signup = true;
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO students (name, email, password, grade) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $grade);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                $show_signup = false;
            } else {
                $message = "Error: " . $stmt->error;
                $show_signup = true;
            }
            $stmt->close();
        }
        $checkEmail->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login / Sign Up</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #2B303A;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      overflow: hidden;
    }
    .container {
      width: 900px;
      height: 500px;
      background:#2B303A;
      border-radius: 12px;
      display: flex;
      position: relative;
      overflow: hidden;
      transition: transform 0.6s ease-in-out;
    }
    .panel {
      width: 50%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      transition: transform 0.6s ease-in-out;
    }
    .left-panel {
      background: #1A212C;
      color: #fff;
    }
    .hexagon {
      width: 287.87px;
      height: 331.90px;
      border: 1.69px solid #ccc;
      background-color: #ECECEC;
      clip-path: polygon(
        50% 0%, 
        100% 25%, 
        100% 75%, 
        50% 100%, 
        0% 75%, 
        0% 25%
      );
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .hexagon img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .right-panel {
      background: #ECECEC;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .toggle-switch {
      position: absolute;
      top: 20px;
      left: 20px;
      width: 70px;
      height: 35px;
      background: #1A212C;
      border-radius: 50px;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: background 0.3s;
      z-index: 10;
    }
    .toggle-switch .circle {
      width: 30px;
      height: 30px;
      background: #ECECEC;
      border-radius: 50%;
      margin: 2px;
      transition: transform 0.3s;
    }
    .toggle-switch.active {
     background: #ECECEC;
     border: 2px solid #000;
    }
    .toggle-switch.active .circle {
      background: #1A212C;
      transform: translateX(35px);
    }
    .form {
      width: 100%;
      max-width: 280px;
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
      font-size: 28px;
      font-weight: bold;
      color: #000;
    }
    label {
      display: block;
      text-align: left;
      margin-bottom: 5px;
      font-size: 12px;
      font-weight: bold;
      letter-spacing: 1px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      background: #8BC5B6;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }
    .message {
      margin-bottom: 10px;
      font-weight: bold;
      color: #d8000c;
      background: #fff3f3;
      border-radius: 6px;
      padding: 8px;
      display: <?php echo ($message !== "") ? "block" : "none"; ?>;
    }
    .container.toggle .left-panel {
      transform: translateX(100%);
    }
    .container.toggle .right-panel {
      transform: translateX(-100%);
    }
    @media (max-width:820px){
      .container{flex-direction:column;width:98vw;height:auto;}
      .panel{width:100%;height:auto;}
      .hexagon{width:180px;height:200px;}
    }
  </style>
</head>
<body>
  <div class="container<?php echo $show_signup ? ' toggle' : ''; ?>" id="container">
    <!-- Left side (Hexagon) -->
    <div class="panel left-panel">
      <div class="hexagon">
         <img src="g2flogo.jpg" alt="">
      </div>
    </div>
    <!-- Right side (Forms) -->
    <div class="panel right-panel">
      <!-- Toggle -->
      <div class="toggle-switch<?php echo $show_signup ? ' active' : ''; ?>" id="toggleBtn">
        <div class="circle"></div>
      </div>
      <!-- Forms -->
      <form class="form" id="formContent" method="POST" action="loginn.php" autocomplete="off">
        <?php if (!$show_signup): ?>
          <h2>Login</h2>
          <div class="message"><?php echo htmlspecialchars($message); ?></div>
          <input type="hidden" name="form_type" value="login">
          <label for="email">EMAIL</label>
          <input type="text" name="email" id="email" placeholder="Email" required>
          <label for="password">PASSWORD</label>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <button type="submit">LOG IN</button>
        <?php else: ?>
          <h2>Sign up</h2>
          <div class="message"><?php echo htmlspecialchars($message); ?></div>
          <input type="hidden" name="form_type" value="signup">
          <label for="name">FULL NAME</label>
          <input type="text" name="name" id="name" placeholder="Full Name" required>
          <label for="email">EMAIL</label>
          <input type="email" name="email" id="email" placeholder="Email" required>
          <label for="grade">GRADE</label>
          <input type="text" name="grade" id="grade" placeholder="Grade" required>
          <label for="password">PASSWORD</label>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <label for="confirm_password">CONFIRM PASSWORD</label>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
          <button type="submit">SIGN UP</button>
        <?php endif; ?>
      </form>
    </div>
  </div>
  <script>
    const toggleBtn = document.getElementById("toggleBtn");
    const container = document.getElementById("container");
    const formContent = document.getElementById("formContent");

    let isLogin = <?php echo $show_signup ? 'false' : 'true'; ?>;

    toggleBtn.addEventListener("click", () => {
      toggleBtn.classList.toggle("active");
      container.classList.toggle("toggle");
      isLogin = !isLogin;
      // Swap form content via JS (no PHP, so fields won't persist after submit)
      if (isLogin) {
        formContent.innerHTML = `
          <h2>Login</h2>
          <div class="message" style="display:none;"></div>
          <input type="hidden" name="form_type" value="login">
          <label for="email">EMAIL</label>
          <input type="text" name="email" id="email" placeholder="Email" required>
          <label for="password">PASSWORD</label>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <button type="submit">LOG IN</button>
        `;
      } else {
        formContent.innerHTML = `
          <h2>Sign up</h2>
          <div class="message" style="display:none;"></div>
          <input type="hidden" name="form_type" value="signup">
          <label for="name">FULL NAME</label>
          <input type="text" name="name" id="name" placeholder="Full Name" required>
          <label for="email">EMAIL</label>
          <input type="email" name="email" id="email" placeholder="Email" required>
          <label for="grade">GRADE</label>
          <input type="text" name="grade" id="grade" placeholder="Grade" required>
          <label for="password">PASSWORD</label>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <label for="confirm_password">CONFIRM PASSWORD</label>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
          <button type="submit">SIGN UP</button>
        `;
      }
    });
  </script>
</body>
</html>
