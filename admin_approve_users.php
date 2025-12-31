<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);

// Handle approval actions
if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE users SET status = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $_SESSION['message'] = "User approved successfully!";
    header("Location: admin_approve_users.php");
    exit();
}

if (isset($_GET['reject'])) {
    $user_id = $_GET['reject'];
    $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $_SESSION['message'] = "User rejected successfully!";
    header("Location: admin_approve_users.php");
    exit();
}

// Get pending users
$pending_users = mysqli_query($conn, "SELECT * FROM users WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Users - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #0d47a1;
        }
        
        .back-btn {
            background: #0d47a1;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        
        .users-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .users-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            color: #0d47a1;
            font-weight: 600;
        }
        
        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-approve {
            background: #4caf50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-reject {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .no-users {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-users i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-check"></i> Pending User Approvals</h1>
            <a href="admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($pending_users) > 0): ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($pending_users)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo $user['name']; ?></strong></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['student_id'] ?: 'N/A'; ?></td>
                        <td><?php echo $user['phone'] ?: 'N/A'; ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['registration_date'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin_approve_users.php?approve=<?php echo $user['id']; ?>" class="btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                                <a href="admin_approve_users.php?reject=<?php echo $user['id']; ?>" class="btn-reject">
                                    <i class="fas fa-times"></i> Reject
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-users">
                <i class="fas fa-user-check"></i>
                <h2>No Pending Approvals</h2>
                <p>All users have been approved!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>