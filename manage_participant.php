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

// Add Participant
if (isset($_POST['add_participant'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    // Handle empty event_id as NULL
    $event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : NULL;
    $assigned_role = $_POST['assigned_role'];
    $status = $_POST['status'];

    // Basic Validation
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = "Name and Email are required!";
        header("Location: admin_dashboard.php#participants");
        exit();
    }

    // Check if email already exists for this event
    if ($event_id) {
        $check = $conn->prepare("SELECT id FROM volunteers WHERE email = ? AND event_id = ?");
        $check->bind_param("si", $email, $event_id);
    } else {
        $check = $conn->prepare("SELECT id FROM volunteers WHERE email = ? AND event_id IS NULL");
        $check->bind_param("s", $email);
    }
    
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "This person is already registered for this event!";
        header("Location: admin_dashboard.php#participants");
        exit();
    }

    // Insert new participant
    // Use 's' type for event_id to safely handle potential nulls or strings, as MySQL will cast if needed,
    // but explicit NULL in bind_param requires care.
    // If event_id is NULL, we can't easily bind it as 'i' without warnings in some PHP versions.
    // Safe approach: Prepare statement based on nullability.
    
    if ($event_id) {
        $stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, event_id, assigned_role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $phone, $event_id, $assigned_role, $status);
    } else {
        $stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, event_id, assigned_role, status) VALUES (?, ?, ?, NULL, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $assigned_role, $status);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Participant added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add participant: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php#participants");
    exit();
}

// Update Participant
if (isset($_POST['update_participant'])) {
    $participant_id = intval($_POST['participant_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    // Handle empty event_id as NULL
    $event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : NULL;
    $assigned_role = $_POST['assigned_role'];
    $status = $_POST['status'];

    if (empty($name) || empty($email)) {
        $_SESSION['error'] = "Name and Email are required!";
        header("Location: admin_dashboard.php#participants");
        exit();
    }

    if ($event_id) {
        $stmt = $conn->prepare("UPDATE volunteers SET name=?, email=?, phone=?, event_id=?, assigned_role=?, status=? WHERE id=?");
        $stmt->bind_param("sssissi", $name, $email, $phone, $event_id, $assigned_role, $status, $participant_id);
    } else {
        $stmt = $conn->prepare("UPDATE volunteers SET name=?, email=?, phone=?, event_id=NULL, assigned_role=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $assigned_role, $status, $participant_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Participant updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update participant: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php#participants");
    exit();
}

// Delete Participant
if (isset($_GET['delete'])) {
    $participant_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM volunteers WHERE id = ?");
    $stmt->bind_param("i", $participant_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Participant deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete participant.";
    }
    
    header("Location: admin_dashboard.php#participants");
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>
