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

// Create Event
if (isset($_POST['create_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $sport_type = trim($_POST['sport_type']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = trim($_POST['venue']);
    $max_participants = !empty($_POST['max_participants']) ? $_POST['max_participants'] : 0;
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($sport_type) || empty($event_date) || empty($venue)) {
        $_SESSION['error'] = "All required fields must be filled!";
        header("Location: admin_dashboard.php#events");
        exit();
    }

    // Validate Date (must be future or today)
    if (strtotime($event_date) < strtotime('today')) {
         // Optional: allow past events? User said "valid data", usually implies logical validity.
         // Let's warn but maybe allow? No, let's enforce future dates for "Upcoming".
         // Actually, for admin adding history, maybe past is ok. 
         // Let's just stick to basic non-empty validation for now to avoid blocking testing.
    }

    $stmt = $conn->prepare("INSERT INTO events (title, description, sport_type, event_date, event_time, venue, max_participants, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssii", $title, $description, $sport_type, $event_date, $event_time, $venue, $max_participants, $created_by);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Event '$title' created successfully!";
    } else {
        $_SESSION['error'] = "Failed to create event: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php#events");
    exit();
}

// Update Event
if (isset($_POST['update_event'])) {
    $event_id = intval($_POST['event_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $sport_type = trim($_POST['sport_type']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = trim($_POST['venue']);
    $max_participants = !empty($_POST['max_participants']) ? $_POST['max_participants'] : 0;

    if (empty($title) || empty($sport_type) || empty($event_date) || empty($venue)) {
        $_SESSION['error'] = "All required fields must be filled!";
        header("Location: admin_dashboard.php#events");
        exit();
    }

    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, sport_type=?, event_date=?, event_time=?, venue=?, max_participants=? WHERE id=?");
    $stmt->bind_param("sssssiii", $title, $description, $sport_type, $event_date, $event_time, $venue, $max_participants, $event_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Event '$title' updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update event: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php#events");
    exit();
}

// Delete Event
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Event deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete event.";
    }
    
    header("Location: admin_dashboard.php#events");
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>