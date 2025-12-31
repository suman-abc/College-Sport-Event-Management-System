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

// Add Ground
if (isset($_POST['add_ground'])) {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if (empty($name) || empty($location)) {
        $_SESSION['error'] = "Name and Location are required!";
        header("Location: admin_dashboard.php#grounds");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO grounds (name, location, description, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $description, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ground added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add ground: " . $conn->error;
    }
    header("Location: admin_dashboard.php#grounds");
    exit();
}

// Update Ground
if (isset($_POST['update_ground'])) {
    $id = intval($_POST['ground_id']);
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if (empty($name) || empty($location)) {
        $_SESSION['error'] = "Name and Location are required!";
        header("Location: admin_dashboard.php#grounds");
        exit();
    }

    $stmt = $conn->prepare("UPDATE grounds SET name=?, location=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $location, $description, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ground updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update ground: " . $conn->error;
    }
    header("Location: admin_dashboard.php#grounds");
    exit();
}

// Delete Ground
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Check for active bookings
    $check = $conn->prepare("SELECT id FROM bookings WHERE ground_id = ? AND status = 'pending'");
    $check->bind_param("i", $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Cannot delete ground with pending bookings!";
        header("Location: admin_dashboard.php#grounds");
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM grounds WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ground deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete ground.";
    }
    header("Location: admin_dashboard.php#grounds");
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>
