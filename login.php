<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

// Connect to database
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            
            // Check user status
            if ($user['status'] == 'pending') {
                $error = "Your account is pending admin approval.";
            } elseif ($user['status'] == 'rejected') {
                $error = "Your account has been rejected.";
            } elseif ($user['status'] == 'accepted') {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $error = "Your account status is invalid.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Sport Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e3f2fd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            width: 350px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0d47a1;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #90caf9;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #0d47a1;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #1565c0;
        }
        .text-center {
            text-align: center;
            margin-top: 15px;
        }
        .text-center a {
            color: #0d47a1;
            text-decoration: none;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #f44336;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Sport Events Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="text-center">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>