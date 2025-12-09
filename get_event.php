<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        echo json_encode(['success' => true, 'event' => $event]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}
?>
