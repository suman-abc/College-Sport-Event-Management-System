<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.html");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sport Event Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #0d47a1 0%, #1976d2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .logo h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo i {
            color: #4fc3f7;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            list-style: none;
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid #4fc3f7;
        }

        .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #0d47a1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .logout-btn {
            background: #ff5252;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #ff1744;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 30px;
        }

        .page-title {
            margin-bottom: 30px;
            color: #0d47a1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.event { background: #e3f2fd; color: #0d47a1; }
        .stat-icon.participant { background: #f3e5f5; color: #7b1fa2; }
        .stat-icon.volunteer { background: #e8f5e8; color: #2e7d32; }
        .stat-icon.upcoming { background: #fff3e0; color: #ef6c00; }

        .stat-info h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-info .number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            color: #0d47a1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-btn {
            background: #4caf50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .add-btn:hover {
            background: #388e3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            background: #2196f3;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete {
            background: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            background: #9c27b0;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            overflow: hidden;
        }

        .modal-header {
            padding: 20px;
            background: #0d47a1;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .submit-btn {
            background: #0d47a1;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #1565c0;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active { background: #e8f5e8; color: #2e7d32; }
        .status-upcoming { background: #fff3e0; color: #ef6c00; }
        .status-completed { background: #f5f5f5; color: #757575; }

        /* Tabs */
        .tab-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .tab {
            padding: 15px 30px;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
        }

        .tab.active {
            color: #0d47a1;
            border-bottom: 3px solid #0d47a1;
            background: white;
        }

        .tab-content {
            padding: 20px;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .nav-link span {
                display: none;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2><i class="fas fa-running"></i> <span>Sport Events</span></h2>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#dashboard" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#events" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Manage Events</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_approve_users.php"><i class="fas fa-users"></i>
                    <span>Manage Participants</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#grounds" class="nav-link">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Manage Grounds</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#bookings" class="nav-link">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Booking Requests</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <h1 id="page-title">Admin Dashboard</h1>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 2)); ?>
                </div>
                <div>
                    <strong><?php echo $_SESSION['name']; ?></strong>
                    <div style="font-size: 0.9rem; color: #666;">Admin</div>
                </div>
                <button class="logout-btn" onclick="location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #e8f5e8; color: #2e7d32; padding: 15px 30px; margin: 20px 30px; border-radius: 8px; border-left: 4px solid #2e7d32; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <i class="fas fa-check-circle"></i>
                    <strong><?php echo $_SESSION['success']; ?></strong>
                </div>
                <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #2e7d32; cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #ffebee; color: #c62828; padding: 15px 30px; margin: 20px 30px; border-radius: 8px; border-left: 4px solid #c62828; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <i class="fas fa-exclamation-circle"></i>
                    <strong><?php echo $_SESSION['error']; ?></strong>
                </div>
                <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #c62828; cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content active">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <?php
                    // Get statistics
                    $total_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
                    $total_participants = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers")->fetch_assoc()['total'];
                    $upcoming_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['total'];
                    $active_volunteers = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers WHERE status = 'active'")->fetch_assoc()['total'];
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-icon event">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Events</h3>
                            <div class="number"><?php echo $total_events; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon participant">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Participants</h3>
                            <div class="number"><?php echo $total_participants; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon volunteer">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Active Volunteers</h3>
                            <div class="number"><?php echo $active_volunteers; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon upcoming">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Upcoming Events</h3>
                            <div class="number"><?php echo $upcoming_events; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events -->
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-calendar-check"></i> Recent Events</h3>
                        <button class="add-btn" onclick="showModal('event')">
                            <i class="fas fa-plus"></i> Add Event
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Sport Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC LIMIT 5");
                            while ($event = mysqli_fetch_assoc($recent_events)) {
                                $status = (strtotime($event['event_date']) > time()) ? 'Upcoming' : 'Completed';
                                $status_class = strtolower($status);
                                ?>
                                <tr>
                                    <td><strong><?php echo $event['title']; ?></strong></td>
                                    <td><?php echo $event['sport_type']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($event['event_time'])); ?></td>
                                    <td><?php echo $event['venue']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $status_class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" onclick="editEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-view" onclick="viewParticipants(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Participants -->
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-user-friends"></i> Recent Participants</h3>
                        <button class="add-btn" onclick="showModal('participant')">
                            <i class="fas fa-plus"></i> Add Participant
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Event</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_participants = mysqli_query($conn, 
                                "SELECT v.*, e.title as event_title 
                                 FROM volunteers v 
                                 LEFT JOIN events e ON v.event_id = e.id 
                                 ORDER BY v.created_at DESC LIMIT 5");
                            while ($participant = mysqli_fetch_assoc($recent_participants)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo $participant['name']; ?></strong></td>
                                    <td><?php echo $participant['email']; ?></td>
                                    <td><?php echo $participant['phone']; ?></td>
                                    <td><?php echo $participant['event_title'] ?: 'Not assigned'; ?></td>
                                    <td><?php echo $participant['assigned_role']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $participant['status']; ?>">
                                            <?php echo ucfirst($participant['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" onclick="editParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-delete" onclick="deleteParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Events Tab -->
            <div id="events-tab" class="tab-content">
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-calendar-alt"></i> All Events</h3>
                        <button class="add-btn" onclick="showModal('event')">
                            <i class="fas fa-plus"></i> Add New Event
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Sport Type</th>
                                <th>Date & Time</th>
                                <th>Venue</th>
                                <th>Max Participants</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
                            while ($event = mysqli_fetch_assoc($all_events)) {
                                ?>
                                <tr>
                                    <td>#<?php echo str_pad($event['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><strong><?php echo $event['title']; ?></strong></td>
                                    <td><?php echo $event['sport_type']; ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?><br>
                                        <small><?php echo date('h:i A', strtotime($event['event_time'])); ?></small>
                                    </td>
                                    <td><?php echo $event['venue']; ?></td>
                                    <td><?php echo $event['max_participants'] ?: 'Unlimited'; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" onclick="editEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-view" onclick="viewParticipants(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            <button class="btn-delete" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Participants Tab -->
            <div id="participants-tab" class="tab-content">
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-users"></i> All Participants</h3>
                        <button class="add-btn" onclick="showModal('participant')">
                            <i class="fas fa-plus"></i> Add New Participant
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact Info</th>
                                <th>Assigned Event</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_participants = mysqli_query($conn, 
                                "SELECT v.*, e.title as event_title 
                                 FROM volunteers v 
                                 LEFT JOIN events e ON v.event_id = e.id 
                                 ORDER BY v.created_at DESC");
                            while ($participant = mysqli_fetch_assoc($all_participants)) {
                                ?>
                                <tr>
                                    <td>#<?php echo str_pad($participant['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><strong><?php echo $participant['name']; ?></strong></td>
                                    <td>
                                        <div><?php echo $participant['email']; ?></div>
                                        <small><?php echo $participant['phone']; ?></small>
                                    </td>
                                    <td><?php echo $participant['event_title'] ?: 'Not assigned'; ?></td>
                                    <td><?php echo $participant['assigned_role']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $participant['status']; ?>">
                                            <?php echo ucfirst($participant['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" onclick="editParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-delete" onclick="deleteParticipant(<?php echo $participant['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Volunteers Tab -->
            <div id="volunteers-tab" class="tab-content">
                <div class="tab-container">
                    <div class="tabs">
                        <div class="tab active" onclick="showVolunteerTab('all')">All Volunteers</div>
                        <div class="tab" onclick="showVolunteerTab('active')">Active</div>
                        <div class="tab" onclick="showVolunteerTab('inactive')">Inactive</div>
                    </div>
                    <div id="all-volunteers" class="tab-content active">
                        <div style="padding: 20px;">
                            <p>Volunteer management content goes here...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Tab -->
            <div id="reports-tab" class="tab-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon event">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Event Analytics</h3>
                            <div class="number">Coming Soon</div>
                        </div>

            <!-- Grounds Tab -->
            <div id="grounds-tab" class="tab-content">
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-map-marker-alt"></i> Manage Grounds</h3>
                        <button class="add-btn" onclick="showModal('ground')">
                            <i class="fas fa-plus"></i> Add New Ground
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_grounds = mysqli_query($conn, "SELECT * FROM grounds ORDER BY id DESC");
                            while ($ground = mysqli_fetch_assoc($all_grounds)) {
                                ?>
                                <tr>
                                    <td>#<?php echo $ground['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($ground['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($ground['location']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $ground['status'] == 'available' ? 'status-active' : 'status-completed'; ?>">
                                            <?php echo ucfirst($ground['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" onclick="editGround(<?php echo $ground['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-delete" onclick="deleteGround(<?php echo $ground['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bookings Tab -->
            <div id="bookings-tab" class="tab-content">
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-clipboard-check"></i> Booking Requests</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Ground</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_bookings = mysqli_query($conn, 
                                "SELECT b.*, u.name as user_name, e.title as event_title, g.name as ground_name 
                                 FROM bookings b 
                                 JOIN users u ON b.user_id = u.id 
                                 JOIN events e ON b.event_id = e.id 
                                 JOIN grounds g ON b.ground_id = g.id 
                                 ORDER BY b.created_at DESC");
                            while ($booking = mysqli_fetch_assoc($all_bookings)) {
                                ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['event_title']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['ground_name']); ?></td>
                                    <td>
                                        <?php echo $booking['booking_date'] . ' ' . $booking['booking_time']; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status'] == 'approved' ? 'active' : ($booking['status'] == 'pending' ? 'upcoming' : 'completed'); ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($booking['status'] == 'pending'): ?>
                                        <div class="action-buttons">
                                            <button class="btn-edit" style="background:#4caf50" onclick="updateBooking(<?php echo $booking['id']; ?>, 'approve')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn-delete" onclick="updateBooking(<?php echo $booking['id']; ?>, 'reject')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <?php else: ?>
                                            <small><?php echo date('M d', strtotime($booking['created_at'])); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon participant">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Participation Stats</h3>
                            <div class="number">Coming Soon</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-plus"></i> Add New Event</h3>
                <button class="close-modal" onclick="closeModal('event')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="event-form" action="manage_event.php" method="POST">
                    <div class="form-group">
                        <label for="event-title">Event Title *</label>
                        <input type="text" id="event-title" name="title" required placeholder="Enter event title">
                    </div>
                    
                    <div class="form-group">
                        <label for="event-description">Description</label>
                        <textarea id="event-description" name="description" placeholder="Enter event description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="sport-type">Sport Type *</label>
                        <input type="text" id="sport-type" name="sport_type" required placeholder="e.g., Football, Basketball, Cricket">
                    </div>
                    
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="event-date">Event Date *</label>
                            <input type="date" id="event-date" name="event_date" required>
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="event-time">Event Time *</label>
                            <input type="time" id="event-time" name="event_time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="venue">Venue *</label>
                        <input type="text" id="venue" name="venue" required placeholder="Enter venue address">
                    </div>
                    
                    <div class="form-group">
                        <label for="max-participants">Maximum Participants</label>
                        <input type="number" id="max-participants" name="max_participants" placeholder="Leave empty for unlimited">
                    </div>
                    
                    <button type="submit" class="submit-btn" name="create_event">
                        <i class="fas fa-save"></i> Create Event
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div id="edit-event-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Event</h3>
                <button class="close-modal" onclick="closeModal('edit-event')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-event-form" action="manage_event.php" method="POST">
                    <input type="hidden" id="edit-event-id" name="event_id">
                    
                    <div class="form-group">
                        <label for="edit-event-title">Event Title *</label>
                        <input type="text" id="edit-event-title" name="title" required placeholder="Enter event title">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-event-description">Description</label>
                        <textarea id="edit-event-description" name="description" placeholder="Enter event description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-sport-type">Sport Type *</label>
                        <input type="text" id="edit-sport-type" name="sport_type" required placeholder="e.g., Football, Basketball, Cricket">
                    </div>
                    
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="edit-event-date">Event Date *</label>
                            <input type="date" id="edit-event-date" name="event_date" required>
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="edit-event-time">Event Time *</label>
                            <input type="time" id="edit-event-time" name="event_time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-venue">Venue *</label>
                        <input type="text" id="edit-venue" name="venue" required placeholder="Enter venue address">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-max-participants">Maximum Participants</label>
                        <input type="number" id="edit-max-participants" name="max_participants" placeholder="Leave empty for unlimited">
                    </div>
                    
                    <button type="submit" class="submit-btn" name="update_event">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Participant Modal -->
    <div id="participant-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Add New Participant</h3>
                <button class="close-modal" onclick="closeModal('participant')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="participant-form" action="manage_participant.php" method="POST">
                    <div class="form-group">
                        <label for="participant-name">Full Name *</label>
                        <input type="text" id="participant-name" name="name" required placeholder="Enter full name">
                    </div>
                    
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="participant-email">Email Address *</label>
                            <input type="email" id="participant-email" name="email" required placeholder="Enter email">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="participant-phone">Phone Number</label>
                            <input type="tel" id="participant-phone" name="phone" placeholder="Enter phone number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="event-assign">Assign to Event</label>
                        <select id="event-assign" name="event_id">
                            <option value="">Select an event (optional)</option>
                            <?php
                            $events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date");
                            while ($event = mysqli_fetch_assoc($events)) {
                                echo "<option value='{$event['id']}'>{$event['title']} - {$event['event_date']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="participant-role">Role/Position</label>
                        <select id="participant-role" name="assigned_role">
                            <option value="">Select role</option>
                            <option value="Player">Player</option>
                            <option value="Coach">Coach</option>
                            <option value="Referee">Referee</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="participant-status">Status</label>
                        <select id="participant-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn" name="add_participant">
                        <i class="fas fa-user-plus"></i> Add Participant
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Participant Modal -->
    <div id="edit-participant-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Edit Participant</h3>
                <button class="close-modal" onclick="closeModal('edit-participant')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-participant-form" action="manage_participant.php" method="POST">
                    <input type="hidden" id="edit-participant-id" name="participant_id">
                    
                    <div class="form-group">
                        <label for="edit-participant-name">Full Name *</label>
                        <input type="text" id="edit-participant-name" name="name" required placeholder="Enter full name">
                    </div>
                    
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="edit-participant-email">Email Address *</label>
                            <input type="email" id="edit-participant-email" name="email" required placeholder="Enter email">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="edit-participant-phone">Phone Number</label>
                            <input type="tel" id="edit-participant-phone" name="phone" placeholder="Enter phone number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-event-assign">Assign to Event</label>
                        <select id="edit-event-assign" name="event_id">
                            <option value="">Select an event (optional)</option>
                            <?php
                            $events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date");
                            while ($event = mysqli_fetch_assoc($events)) {
                                echo "<option value='{$event['id']}'>{$event['title']} - {$event['event_date']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-participant-role">Role/Position</label>
                        <select id="edit-participant-role" name="assigned_role">
                            <option value="">Select role</option>
                            <option value="Player">Player</option>
                            <option value="Coach">Coach</option>
                            <option value="Referee">Referee</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-participant-status">Status</label>
                        <select id="edit-participant-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn" name="update_participant">
                        <i class="fas fa-save"></i> Update Participant
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Ground Modal -->
    <div id="ground-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-map-marker-alt"></i> Add New Ground</h3>
                <button class="close-modal" onclick="closeModal('ground')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="ground-form" action="manage_ground.php" method="POST">
                    <div class="form-group">
                        <label>Ground Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Main Stadium">
                    </div>
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" required placeholder="e.g. North Campus">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" placeholder="Description..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn" name="add_ground">Add Ground</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Ground Modal -->
    <div id="edit-ground-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Ground</h3>
                <button class="close-modal" onclick="closeModal('edit-ground')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-ground-form" action="manage_ground.php" method="POST">
                    <input type="hidden" id="edit-ground-id" name="ground_id">
                    <div class="form-group">
                        <label>Ground Name *</label>
                        <input type="text" id="edit-ground-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" id="edit-ground-location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="edit-ground-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit-ground-status" name="status">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn" name="update_ground">Update Ground</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Ground Management Functions
        function editGround(id) {
            fetch('get_ground.php?id=' + id)
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('edit-ground-id').value = data.ground.id;
                        document.getElementById('edit-ground-name').value = data.ground.name;
                        document.getElementById('edit-ground-location').value = data.ground.location;
                        document.getElementById('edit-ground-description').value = data.ground.description;
                        document.getElementById('edit-ground-status').value = data.ground.status;
                        document.getElementById('edit-ground-modal').style.display = 'flex';
                    } else {
                        alert('Error fetching ground data');
                    }
                });
        }
        
        function deleteGround(id) {
            if(confirm('Delete this ground?')) location.href='manage_ground.php?delete='+id;
        }

        function updateBooking(id, action) {
            if(confirm(action.toUpperCase() + ' this booking?')) location.href='manage_booking.php?action='+action+'&id='+id;
        }

        // Updated Modal Functions to include Ground modals
        const originalShowModal = window.showModal;
        window.showModal = function(type) {
            if (type === 'ground') {
                document.getElementById('ground-modal').style.display = 'flex';
            } else {
                originalShowModal(type); // Call original if exists, or basic fallback
                if(type === 'event') document.getElementById('event-modal').style.display = 'flex';
                if(type === 'participant') document.getElementById('participant-modal').style.display = 'flex';
            }
        }

        const originalCloseModal = window.closeModal;
        window.closeModal = function(type) {
            if (type === 'ground') {
                document.getElementById('ground-modal').style.display = 'none';
            } else if (type === 'edit-ground') {
                document.getElementById('edit-ground-modal').style.display = 'none';
            } else {
                // Call original logic manually to avoid recursion or undefined if not assigned
                if (type === 'event') document.getElementById('event-modal').style.display = 'none';
                if (type === 'participant') document.getElementById('participant-modal').style.display = 'none';
                if (type === 'edit-event') document.getElementById('edit-event-modal').style.display = 'none';
                if (type === 'edit-participant') document.getElementById('edit-participant-modal').style.display = 'none';
            }
        }

        // Navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
                
                // Update page title
                const pageTitle = this.querySelector('span').textContent;
                document.getElementById('page-title').textContent = pageTitle;
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                
                // Show selected tab
                const tabId = this.getAttribute('href').substring(1) + '-tab';
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Handle hash modification on load
        window.addEventListener('load', function() {
            if(window.location.hash) {
                const targetTab = window.location.hash.substring(1); // e.g. "participants"
                const link = document.querySelector(`.nav-link[href="#${targetTab}"]`);
                if(link) {
                    link.click();
                }
            }
        });

        // Modal Functions
        function showModal(type) {
            if (type === 'event') {
                document.getElementById('event-modal').style.display = 'flex';
            } else if (type === 'participant') {
                document.getElementById('participant-modal').style.display = 'flex';
            }
        }

        function closeModal(type) {
            if (type === 'event') {
                document.getElementById('event-modal').style.display = 'none';
            } else if (type === 'participant') {
                document.getElementById('participant-modal').style.display = 'none';
            } else if (type === 'edit-event') {
                document.getElementById('edit-event-modal').style.display = 'none';
            } else if (type === 'edit-participant') {
                document.getElementById('edit-participant-modal').style.display = 'none';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const eventModal = document.getElementById('event-modal');
            const participantModal = document.getElementById('participant-modal');
            const editEventModal = document.getElementById('edit-event-modal');
            const editParticipantModal = document.getElementById('edit-participant-modal');
            
            if (event.target === eventModal) {
                eventModal.style.display = 'none';
            }
            if (event.target === participantModal) {
                participantModal.style.display = 'none';
            }
            if (event.target === editEventModal) {
                editEventModal.style.display = 'none';
            }
            if (event.target === editParticipantModal) {
                editParticipantModal.style.display = 'none';
            }
        }

        // Delete Event
        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                window.location.href = 'manage_event.php?delete=' + eventId;
            }
        }

        // Delete Participant
        function deleteParticipant(participantId) {
            if (confirm('Are you sure you want to delete this participant?')) {
                window.location.href = 'manage_participant.php?delete=' + participantId;
            }
        }

        // View Participants for Event
        function viewParticipants(eventId) {
            // Navigate to participants tab and filter by event
            window.location.href = 'admin_dashboard.php#participants';
            // In a full implementation, you'd add filtering logic here
            alert('Viewing participants for event #' + eventId + '\nNote: Full filtering can be added in future enhancement.');
        }

        // Edit Event - Fetch data and populate modal
        function editEvent(eventId) {
            // Fetch event data using AJAX
            fetch('get_event.php?id=' + eventId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit form
                        document.getElementById('edit-event-id').value = data.event.id;
                        document.getElementById('edit-event-title').value = data.event.title;
                        document.getElementById('edit-event-description').value = data.event.description || '';
                        document.getElementById('edit-sport-type').value = data.event.sport_type;
                        document.getElementById('edit-event-date').value = data.event.event_date;
                        document.getElementById('edit-event-time').value = data.event.event_time;
                        document.getElementById('edit-venue').value = data.event.venue;
                        document.getElementById('edit-max-participants').value = data.event.max_participants || '';
                        
                        // Show the modal
                        document.getElementById('edit-event-modal').style.display = 'flex';
                    } else {
                        alert('Error loading event data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading event data');
                });
        }

        // Edit Participant - Fetch data and populate modal
        function editParticipant(participantId) {
            // Fetch participant data using AJAX
            fetch('get_participant.php?id=' + participantId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit form
                        document.getElementById('edit-participant-id').value = data.participant.id;
                        document.getElementById('edit-participant-name').value = data.participant.name;
                        document.getElementById('edit-participant-email').value = data.participant.email;
                        document.getElementById('edit-participant-phone').value = data.participant.phone || '';
                        document.getElementById('edit-event-assign').value = data.participant.event_id || '';
                        document.getElementById('edit-participant-role').value = data.participant.assigned_role || '';
                        document.getElementById('edit-participant-status').value = data.participant.status;
                        
                        // Show the modal
                        document.getElementById('edit-participant-modal').style.display = 'flex';
                    } else {
                        alert('Error loading participant data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading participant data');
                });
        }

        // Volunteer tab switching
        function showVolunteerTab(tab) {
            // Simple placeholder for volunteer tab switching
            console.log('Switching to volunteer tab:', tab);
        }
    </script>
</body>
</html>