<?php
session_start();

if (!isset($_SESSION['email'])) {
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

if (isset($_POST['book_event'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = intval($_POST['event_id']);
    $ground_id = intval($_POST['ground_id']);
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    
    // Check if user already booked for this event
    $check = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND event_id = ?");
    $check->bind_param("ii", $user_id, $event_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "You have already requested a booking for this event.";
        header("Location: dashboard.php");
        exit();
    }
    
    // Insert Booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, ground_id, booking_date, booking_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiiss", $user_id, $event_id, $ground_id, $booking_date, $booking_time);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Booking requested successfully! Waiting for admin approval.";
        
        // Also add to volunteers as 'pending' maybe? Or keep them separate?
        // Let's add to volunteers table too, as that's what shows up in "My Events" currently.
        // But the current system uses valid email.
        $user_email = $_SESSION['email'];
        $user_name = $_SESSION['name'];
        // We don't have phone in session, maybe query it? Or just leave blank.
        // Let's check if we can get phone.
        $u_stmt = $conn->prepare("SELECT phone FROM users WHERE id = ?");
        $u_stmt->bind_param("i", $user_id);
        $u_stmt->execute();
        $u_res = $u_stmt->get_result()->fetch_assoc();
        $phone = $u_res['phone'];
        
        // Insert into volunteers (auto-accepted or pending? Let's say pending until booking approved? Or just separate logic?)
        // The previous system didn't link volunteers to bookings.
        // For now, let's just insert into volunteers so it shows in "My Events".
        // BUT, ideally, we should only add to volunteers if specific logic.
        // Let's just create the Booking. The "My Events" section in `dashboard.php` reads from `volunteers`.
        // I should probably update "My Events" to read from `bookings` OR insert into `volunteers`.
        // Compatibility: Insert into volunteers.
        
        $role = "Participant";
        $status = "active"; // or pending?
        
        // Check duplicate in volunteers
        $v_check = $conn->prepare("SELECT id FROM volunteers WHERE email = ? AND event_id = ?");
        $v_check->bind_param("si", $user_email, $event_id);
        $v_check->execute();
        
        if ($v_check->get_result()->num_rows == 0) {
            $v_stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, event_id, assigned_role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $v_stmt->bind_param("sssis", $user_name, $user_email, $phone, $event_id, $role, $status);
            $v_stmt->execute();
        }
        
    } else {
        $_SESSION['error'] = "Failed to request booking: " . $conn->error;
    }
    
    header("Location: dashboard.php");
    exit();
}
?>
