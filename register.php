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

    // Insert new user with pending approval
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'student', 'pending')");
    $stmt->bind_param("sss", $name, $email, $password_hashed);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration submitted successfully! Your account is pending admin approval. You will receive an email when approved.";
        header("Location: register.php");
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
    <title>Register - College Sport Events</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }
        
        .box { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            width: 100%;
            max-width: 450px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 5px solid #0d47a1;
        }
        
        .box h2 { 
            text-align: center; 
            color: #0d47a1; 
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        
        .box input, .box select { 
            width: 100%; 
            padding: 14px; 
            margin: 12px 0; 
            border: 1px solid #90caf9; 
            border-radius: 8px; 
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .box input:focus, .box select:focus {
            outline: none;
            border-color: #0d47a1;
            box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.1);
        }
        
        .btn { 
            width: 100%; 
            padding: 14px; 
            background: #0d47a1; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            margin-top: 10px;
            transition: background 0.3s, transform 0.2s;
        }
        
        .btn:hover { 
            background: #1565c0; 
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .text-center { 
            text-align: center; 
            margin-top: 20px; 
        }
        
        .text-center a { 
            color: #0d47a1; 
            text-decoration: none;
            font-weight: 500;
        }
        
        .text-center a:hover {
            text-decoration: underline;
        }
        
        .error { 
            color: #d32f2f; 
            text-align: center; 
            margin-bottom: 15px; 
            padding: 12px;
            background: #ffebee;
            border-radius: 8px;
            border-left: 4px solid #d32f2f;
        }
        
        .success { 
            color: #2e7d32; 
            text-align: center; 
            margin-bottom: 15px; 
            padding: 12px;
            background: #e8f5e9;
            border-radius: 8px;
            border-left: 4px solid #2e7d32;
        }
        
        .info-box {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #0d47a1;
        }
        
        .info-box h4 {
            color: #0d47a1;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-box p {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 5px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .required::after {
            content: " *";
            color: #d32f2f;
        }
        
        @media (max-width: 480px) {
            .box {
                padding: 25px;
            }
            
            .box h2 {
                font-size: 1.5rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="box">
    <h2>Register Account</h2>
    
    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> Important Note</h4>
        <p>Your account requires admin approval before you can login. You will receive an email notification once your account is approved.</p>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<div class='error'><i class='fas fa-exclamation-circle'></i> " . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<div class='success'><i class='fas fa-check-circle'></i> " . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="name" class="required">Full Name</label>
            <input type="text" name="name" placeholder="Enter your full name" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="required">Email Address</label>
            <input type="email" name="email" placeholder="Enter your college email" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="required">Password</label>
            <input type="password" name="password" placeholder="Create a password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirm_password" class="required">Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required minlength="6">
        </div>

        <button type="submit" class="btn">
            <i class="fas fa-user-plus"></i> Register Account
        </button>
    </form>

    <div class="text-center">
        <p>Already have an account? <a href="index.html">Login here</a></p>
        <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">
            <i class="fas fa-shield-alt"></i> Your information is secure
        </p>
    </div>
</div>
</body>
</html>