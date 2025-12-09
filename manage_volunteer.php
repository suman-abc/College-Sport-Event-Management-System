<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Add Volunteer
if (isset($_POST['add_volunteer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $event_id = $_POST['event_id'];
    $assigned_role = $_POST['assigned_role'];

    $stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, event_id, assigned_role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $name, $email, $phone, $event_id, $assigned_role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Volunteer added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add volunteer.";
    }
    
    header("Location: admin_dashboard.php#volunteers");
    exit();
}

// Delete Volunteer
if (isset($_GET['delete'])) {
    $volunteer_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM volunteers WHERE id = ?");
    $stmt->bind_param("i", $volunteer_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Volunteer deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete volunteer.";
    }
    
    header("Location: admin_dashboard.php#volunteers");
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>