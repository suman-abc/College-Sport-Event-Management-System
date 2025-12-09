<?php
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$pass = "suman"; // your MySQL password
$db   = "sport_event";

// Connect to database
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hashed);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: index.html");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Try again.";
        header("Location: register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Arial; background:#e3f2fd; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .box { background:white; padding:30px; border-radius:10px; width:350px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
        .box h2 { text-align:center; color:#0d47a1; margin-bottom:20px; }
        .box input { width:100%; padding:12px; margin:10px 0; border:1px solid #90caf9; border-radius:5px; }
        .btn { width:100%; padding:12px; background:#0d47a1; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
        .btn:hover { background:#1565c0; }
        .text-center { text-align:center; margin-top:10px; }
        .text-center a { color:#0d47a1; text-decoration:none; }
        .error { color:red; text-align:center; margin-bottom:10px; }
        .success { color:green; text-align:center; margin-bottom:10px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Register</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<div class='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<div class='success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="btn">Register</button>
    </form>

    <div class="text-center">
        <p>Already have an account? <a href="index.html">Login</a></p>
    </div>
</div>
</body>
</html>
