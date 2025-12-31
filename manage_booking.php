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

if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Get booking details for notification
    $stmt = $conn->prepare("SELECT b.*, g.name as ground_name, e.title as event_title 
                           FROM bookings b 
                           JOIN grounds g ON b.ground_id = g.id 
                           JOIN events e ON b.event_id = e.id 
                           WHERE b.id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if (!$booking) {
        $_SESSION['error'] = "Booking not found.";
        header("Location: admin_dashboard.php#bookings");
        exit();
    }

    $status = ($action == 'approve') ? 'approved' : 'rejected';
    
    // Update status
    $update = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $booking_id);
    
    if ($update->execute()) {
        $_SESSION['success'] = "Booking " . ucfirst($status) . " successfully!";
        
        // Create Notification
        $msg = "";
        if ($status == 'approved') {
            $msg = "Great news! Your booking for ground '{$booking['ground_name']}' for event '{$booking['event_title']}' on {$booking['booking_date']} has been APPROVED.";
        } else {
            $msg = "Sorry, your booking for ground '{$booking['ground_name']}' for event '{$booking['event_title']}' has been REJECTED.";
        }
        
        $notif = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif->bind_param("is", $booking['user_id'], $msg);
        $notif->execute();
        
    } else {
        $_SESSION['error'] = "Failed to update booking status.";
    }
}

header("Location: admin_dashboard.php#bookings");
exit();
?>
