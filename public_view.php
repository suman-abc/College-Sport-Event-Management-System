<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public View - Events & Participants</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        /* Navigation */
        .navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo h1 {
            color: #0d47a1;
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: #555;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .nav-link.active {
            background: #0d47a1;
            color: white;
        }

        .back-btn {
            background: #0d47a1;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            color: #0d47a1;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Tabs */
        .tabs {
            display: flex;
            background: white;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 0;
        }

        .tab {
            padding: 20px 40px;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tab:hover {
            background: #f8f9fa;
            color: #0d47a1;
        }

        .tab.active {
            background: white;
            color: #0d47a1;
            border-bottom: 3px solid #0d47a1;
        }

        /* Tab Content */
        .tab-content {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .tab-content.active {
            display: block;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 30px;
            position: relative;
        }

        .search-box {
            width: 100%;
            padding: 15px 20px;
            padding-left: 50px;
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-box:focus {
            outline: none;
            border-color: #0d47a1;
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #0d47a1;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid #0d47a1;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .event-title {
            color: #0d47a1;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .event-sport {
            color: #666;
            font-size: 0.9rem;
            background: #e3f2fd;
            padding: 3px 10px;
            border-radius: 15px;
            display: inline-block;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-upcoming { background: #e3f2fd; color: #0d47a1; }
        .status-completed { background: #e8f5e8; color: #2e7d32; }

        .event-details {
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #555;
        }

        .detail-item i {
            color: #0d47a1;
            width: 20px;
        }

        .event-description {
            color: #666;
            line-height: 1.6;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        /* Participants List */
        .participants-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .participants-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            color: #0d47a1;
            font-weight: 600;
            border-bottom: 2px solid #e3f2fd;
        }

        .participants-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .participants-table tr:hover {
            background: #f9f9f9;
        }

        .participant-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .participant-avatar {
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

        .role-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .role-player { background: #e3f2fd; color: #0d47a1; }
        .role-volunteer { background: #e8f5e8; color: #2e7d32; }
        .role-coach { background: #fff3e0; color: #ef6c00; }
        .role-referee { background: #f3e5f5; color: #7b1fa2; }

        /* Volunteers Grid */
        .volunteers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .volunteer-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #2e7d32;
        }

        .volunteer-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .volunteer-avatar {
            width: 50px;
            height: 50px;
            background: #2e7d32;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .volunteer-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .volunteer-role {
            color: #2e7d32;
            font-size: 0.9rem;
        }

        .volunteer-details {
            color: #666;
            font-size: 0.9rem;
        }

        .volunteer-details p {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Upcoming Events */
        .upcoming-list {
            display: grid;
            gap: 15px;
        }

        .upcoming-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #4caf50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .upcoming-info h4 {
            color: #333;
            margin-bottom: 8px;
        }

        .upcoming-date {
            color: #0d47a1;
            font-weight: 600;
        }

        .days-left {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: 600;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-data i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }

        /* Statistics */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
        }

        .events-stat { background: #e3f2fd; color: #0d47a1; }
        .participants-stat { background: #f3e5f5; color: #7b1fa2; }
        .volunteers-stat { background: #e8f5e8; color: #2e7d32; }
        .upcoming-stat { background: #fff3e0; color: #ef6c00; }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.95rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            color: #666;
            border-top: 1px solid #eee;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                justify-content: center;
                padding: 15px;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .participants-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-running" style="color: #0d47a1; font-size: 1.5rem;"></i>
            <h1>College Sport Events</h1>
        </div>
        
        <div class="nav-links">
            <a href="#events" class="nav-link active" onclick="showTab('events')">Events</a>
            <a href="#participants" class="nav-link" onclick="showTab('participants')">Participants</a>
            <a href="#volunteers" class="nav-link" onclick="showTab('volunteers')">Volunteers</a>
            <a href="#upcoming" class="nav-link" onclick="showTab('upcoming')">Upcoming</a>
        </div>
        
        <a href="landing.php" class="back-btn">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>Public Event & Participant Viewer</h2>
            <p>View all sports events, participants, and volunteers. No login required!</p>
        </div>

        <!-- Statistics -->
        <div class="stats-bar">
            <?php
            // Get statistics
            $total_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
            $total_participants = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers")->fetch_assoc()['total'];
            $upcoming_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['total'];
            $active_volunteers = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers WHERE status = 'active'")->fetch_assoc()['total'];
            ?>
            
            <div class="stat-card">
                <div class="stat-icon events-stat">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-number"><?php echo $total_events; ?></div>
                <div class="stat-label">Total Events</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon participants-stat">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_participants; ?></div>
                <div class="stat-label">Participants</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon volunteers-stat">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="stat-number"><?php echo $active_volunteers; ?></div>
                <div class="stat-label">Volunteers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon upcoming-stat">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $upcoming_events; ?></div>
                <div class="stat-label">Upcoming Events</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('events')">
                <i class="fas fa-calendar-alt"></i> All Events
            </div>
            <div class="tab" onclick="showTab('participants')">
                <i class="fas fa-users"></i> Participants List
            </div>
            <div class="tab" onclick="showTab('volunteers')">
                <i class="fas fa-hands-helping"></i> Volunteers
            </div>
            <div class="tab" onclick="showTab('upcoming')">
                <i class="fas fa-clock"></i> Upcoming Events
            </div>
        </div>

        <!-- Events Tab -->
        <div id="events-tab" class="tab-content active">
            <h3 style="margin-bottom: 20px; color: #0d47a1;">
                <i class="fas fa-calendar-check"></i> All Sports Events
            </h3>
            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="eventSearch" class="search-box" placeholder="Search events by name, sport, or venue..." onkeyup="searchEvents()">
            </div>
            
            <div class="events-grid" id="eventsList">
                <?php
                $events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
                
                if (mysqli_num_rows($events) > 0) {
                    while ($event = mysqli_fetch_assoc($events)) {
                        $status = (strtotime($event['event_date']) > time()) ? 'upcoming' : 'completed';
                        $status_text = ucfirst($status);
                        ?>
                        <div class="event-card">
                            <div class="event-header">
                                <div>
                                    <h3 class="event-title"><?php echo $event['title']; ?></h3>
                                    <span class="event-sport"><?php echo $event['sport_type']; ?></span>
                                </div>
                                <span class="status-badge status-<?php echo $status; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </div>
                            
                            <div class="event-details">
                                <div class="detail-item">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('l, F j, Y', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo $event['venue']; ?></span>
                                </div>
                                <?php if ($event['max_participants']): ?>
                                <div class="detail-item">
                                    <i class="fas fa-user-friends"></i>
                                    <span>Max Participants: <?php echo $event['max_participants']; ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($event['description']): ?>
                            <div class="event-description">
                                <?php echo $event['description']; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-data">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Events Available</h3>
                        <p>Check back later for upcoming events</p>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- Participants Tab -->
        <div id="participants-tab" class="tab-content">
            <h3 style="margin-bottom: 20px; color: #0d47a1;">
                <i class="fas fa-user-friends"></i> All Participants
            </h3>
            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="participantSearch" class="search-box" placeholder="Search participants by name or email..." onkeyup="searchParticipants()">
            </div>
            
            <table class="participants-table" id="participantsTable">
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Contact</th>
                        <th>Assigned Event</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $participants = mysqli_query($conn, 
                        "SELECT v.*, e.title as event_title 
                         FROM volunteers v 
                         LEFT JOIN events e ON v.event_id = e.id 
                         ORDER BY v.name");
                    
                    if (mysqli_num_rows($participants) > 0) {
                        while ($participant = mysqli_fetch_assoc($participants)) {
                            // Determine role class
                            $role_class = 'role-player';
                            $role_lower = strtolower($participant['assigned_role']);
                            if (strpos($role_lower, 'volunteer') !== false) $role_class = 'role-volunteer';
                            if (strpos($role_lower, 'coach') !== false) $role_class = 'role-coach';
                            if (strpos($role_lower, 'referee') !== false) $role_class = 'role-referee';
                            ?>
                            <tr>
                                <td>
                                    <div class="participant-info">
                                        <div class="participant-avatar">
                                            <?php echo strtoupper(substr($participant['name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo $participant['name']; ?></strong><br>
                                            <small style="color: #666;">ID: <?php echo str_pad($participant['id'], 3, '0', STR_PAD_LEFT); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo $participant['email']; ?></div>
                                    <?php if ($participant['phone']): ?>
                                    <small style="color: #666;"><?php echo $participant['phone']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $participant['event_title'] ?: 'Not assigned'; ?></td>
                                <td>
                                    <span class="role-badge <?php echo $role_class; ?>">
                                        <?php echo $participant['assigned_role'] ?: 'Participant'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($participant['status'] == 'active'): ?>
                                        <span style="color: #2e7d32; font-weight: 600;">Active</span>
                                    <?php else: ?>
                                        <span style="color: #666;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr>
                            <td colspan="5" style="text-align: center; padding: 40px;">
                                <i class="fas fa-user-slash" style="font-size: 2rem; color: #ddd; margin-bottom: 10px; display: block;"></i>
                                <h3 style="color: #666; margin-bottom: 10px;">No Participants Found</h3>
                                <p style="color: #999;">No participants have been registered yet</p>
                            </td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Volunteers Tab -->
        <div id="volunteers-tab" class="tab-content">
            <h3 style="margin-bottom: 20px; color: #2e7d32;">
                <i class="fas fa-hands"></i> Volunteers
            </h3>
            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="volunteerSearch" class="search-box" placeholder="Search volunteers by name or role..." onkeyup="searchVolunteers()">
            </div>
            
            <div class="volunteers-grid" id="volunteersList">
                <?php
                $volunteers = mysqli_query($conn, 
                    "SELECT v.*, e.title as event_title 
                     FROM volunteers v 
                     LEFT JOIN events e ON v.event_id = e.id 
                     WHERE v.assigned_role LIKE '%volunteer%' OR v.assigned_role = 'Volunteer'
                     ORDER BY v.name");
                
                if (mysqli_num_rows($volunteers) > 0) {
                    while ($volunteer = mysqli_fetch_assoc($volunteers)) {
                        ?>
                        <div class="volunteer-card">
                            <div class="volunteer-header">
                                <div class="volunteer-avatar">
                                    <?php echo strtoupper(substr($volunteer['name'], 0, 2)); ?>
                                </div>
                                <div>
                                    <div class="volunteer-name"><?php echo $volunteer['name']; ?></div>
                                    <div class="volunteer-role"><?php echo $volunteer['assigned_role']; ?></div>
                                </div>
                            </div>
                            
                            <div class="volunteer-details">
                                <p><i class="fas fa-envelope"></i> <?php echo $volunteer['email']; ?></p>
                                <?php if ($volunteer['phone']): ?>
                                <p><i class="fas fa-phone"></i> <?php echo $volunteer['phone']; ?></p>
                                <?php endif; ?>
                                <p><i class="fas fa-calendar"></i> <?php echo $volunteer['event_title'] ?: 'Not assigned'; ?></p>
                                <p>
                                    <i class="fas fa-circle" style="color: <?php echo $volunteer['status'] == 'active' ? '#2e7d32' : '#c62828'; ?>; font-size: 0.8rem;"></i>
                                    <?php echo ucfirst($volunteer['status']); ?>
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-data" style="grid-column: 1 / -1;">
                        <i class="fas fa-hands-helping"></i>
                        <h3>No Volunteers Found</h3>
                        <p>No volunteers have been registered yet</p>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- Upcoming Events Tab -->
        <div id="upcoming-tab" class="tab-content">
            <h3 style="margin-bottom: 20px; color: #4caf50;">
                <i class="fas fa-clock"></i> Upcoming Events
            </h3>
            
            <div class="upcoming-list">
                <?php
                $upcoming = mysqli_query($conn, 
                    "SELECT * FROM events 
                     WHERE event_date >= CURDATE() 
                     ORDER BY event_date ASC");
                
                if (mysqli_num_rows($upcoming) > 0) {
                    while ($event = mysqli_fetch_assoc($upcoming)) {
                        $days_until = ceil((strtotime($event['event_date']) - time()) / (60 * 60 * 24));
                        ?>
                        <div class="upcoming-item">
                            <div class="upcoming-info">
                                <h4><?php echo $event['title']; ?></h4>
                                <div style="color: #666; margin-bottom: 5px;"><?php echo $event['sport_type']; ?></div>
                                <div class="upcoming-date">
                                    <i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                    <i class="far fa-clock" style="margin-left: 15px;"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                                </div>
                                <div style="color: #666; margin-top: 5px;">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $event['venue']; ?>
                                </div>
                            </div>
                            <div class="days-left">
                                <?php echo $days_until == 0 ? 'Today!' : ($days_until == 1 ? 'Tomorrow!' : "In $days_until days"); ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-data">
                        <i class="fas fa-calendar-plus"></i>
                        <h3>No Upcoming Events</h3>
                        <p>Check back later for new event announcements</p>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>College Sport Events - Public View</strong></p>
            <p>This is a public view-only interface. No login required.</p>
            <p style="margin-top: 20px;">
                <a href="landing.php" style="color: #0d47a1; text-decoration: none; margin-right: 15px;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="index.html" style="color: #0d47a1; text-decoration: none; margin-right: 15px;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="register.php" style="color: #0d47a1; text-decoration: none;">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </p>
        </div>
    </div>

    <script>
        // Tab Navigation
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Update navigation links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`a[href="#${tabName}"]`).classList.add('active');
        }

        // Search Functions
        function searchEvents() {
            const searchTerm = document.getElementById('eventSearch').value.toLowerCase();
            const events = document.querySelectorAll('.event-card');
            
            events.forEach(event => {
                const text = event.textContent.toLowerCase();
                event.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        }

        function searchParticipants() {
            const searchTerm = document.getElementById('participantSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#participantsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        function searchVolunteers() {
            const searchTerm = document.getElementById('volunteerSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.volunteer-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        }

        // Load tab from URL hash
        window.addEventListener('load', function() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                showTab(hash);
            }
        });
    </script>
</body>
</html>