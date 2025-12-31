<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - College Sport Events</title>
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
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link:hover {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .nav-link.active {
            background: #0d47a1;
            color: white;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: #0d47a1;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .logout-btn {
            background: #ff5252;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #ff1744;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        /* Welcome Section */
        .welcome-banner {
            background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-text h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .welcome-text p {
            opacity: 0.9;
        }

        .welcome-stats {
            display: flex;
            gap: 30px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        /* Main Content Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        /* Left Sidebar - My Status */
        .dashboard-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .my-events-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            color: #0d47a1;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .my-event-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #0d47a1;
        }

        .my-event-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .my-event-meta {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
            display: flex;
            gap: 15px;
        }

        .my-event-role {
            display: inline-block;
            padding: 3px 8px;
            background: #e3f2fd;
            color: #0d47a1;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Main Feed area - All Events */
        .events-feed {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .feed-title {
            font-size: 1.5rem;
            color: #333;
        }

        .search-box {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 300px;
            font-size: 0.9rem;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .event-info {
            flex: 1;
        }

        .event-main {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .event-icon {
            width: 50px;
            height: 50px;
            background: #e3f2fd;
            color: #0d47a1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .event-title-lg {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .event-sport-tag {
            background: #eee;
            color: #666;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        .event-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            color: #666;
            font-size: 0.9rem;
            margin-left: 65px;
        }

        .detail-point i {
            color: #0d47a1;
            margin-right: 5px;
            width: 15px;
        }

        .event-action {
            margin-left: 20px;
            text-align: right;
        }

        .join-btn {
            background: #4caf50;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .join-btn:hover {
            background: #388e3c;
        }

        .joined-badge {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .completed-badge {
            background: #f5f5f5;
            color: #999;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
        }

        /* Tabs support for mobile or alternative views */
        .tab-nav {
            display: none; /* Hidden on desktop by default as we show grid */
            margin-bottom: 20px;
            background: white;
            padding: 10px;
            border-radius: 8px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: none;
            font-weight: 600;
            color: #666;
            cursor: pointer;
        }

        .tab-btn.active {
            color: #0d47a1;
            border-bottom: 2px solid #0d47a1;
        }

        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-sidebar {
                order: 2;
            }

            .tab-nav {
                display: flex;
                overflow-x: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-running" style="color: #0d47a1; font-size: 1.5rem;"></i>
            <h1>Sport Events</h1>
        </div>
        
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-check"></i> My Events
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-bell"></i> Notifications
            </a>
        </div>
        
        <div class="user-menu">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user_name, 0, 2)); ?>
            </div>
            <div style="font-size: 0.9rem;">
                <strong><?php echo htmlspecialchars($user_name); ?></strong>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Welcome Banner -->
        <?php
        // Count user's active events
        $my_events_count_query = "SELECT COUNT(*) as count FROM volunteers WHERE email = ? AND status = 'active'";
        $stmt = $conn->prepare($my_events_count_query);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $my_events_count = $stmt->get_result()->fetch_assoc()['count'];
        
        // Count total upcoming events
        $upcoming_count = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['cnt'];
        ?>
        
        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Hello, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                <p>Ready to participate in some amazing sports events?</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $my_events_count; ?></div>
                    <div class="stat-label">My Events</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $upcoming_count; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
            </div>
        </div>

        <!-- Tab Content Sections -->
        <div id="home-section" class="tab-section active">
            <div class="dashboard-grid">
                <!-- Sidebar: My Events -->
                <div class="dashboard-sidebar">
                    <div class="my-events-card">
                        <div class="card-header">
                            <h3><i class="fas fa-ticket-alt"></i> My Registrations</h3>
                        </div>
                        
                        <?php
                        // Get user's registered events
                        $my_reg_query = "SELECT v.*, e.title, e.event_date, e.event_time 
                                         FROM volunteers v 
                                         JOIN events e ON v.event_id = e.id 
                                         WHERE v.email = ? 
                                         ORDER BY e.event_date DESC LIMIT 5";
                        $stmt = $conn->prepare($my_reg_query);
                        $stmt->bind_param("s", $user_email);
                        $stmt->execute();
                        $my_regs = $stmt->get_result();

                        if ($my_regs->num_rows > 0) {
                            while ($reg = $my_regs->fetch_assoc()) {
                                $is_upcoming = strtotime($reg['event_date']) >= strtotime('today');
                                ?>
                                <div class="my-event-item">
                                    <div class="my-event-title"><?php echo htmlspecialchars($reg['title']); ?></div>
                                    <div class="my-event-meta">
                                        <span><i class="far fa-calendar"></i> <?php echo date('M d', strtotime($reg['event_date'])); ?></span>
                                        <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($reg['event_time'])); ?></span>
                                    </div>
                                    <div>
                                        <span class="my-event-role"><?php echo htmlspecialchars($reg['assigned_role']); ?></span>
                                        <?php if ($is_upcoming): ?>
                                            <span style="color: #2e7d32; font-size: 0.8rem; margin-left: 10px;">
                                                <i class="fas fa-check-circle"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 0.8rem; margin-left: 10px;">
                                                <i class="fas fa-history"></i> Completed
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div style="text-align:center; padding: 20px; color: #999;">
                                    <i class="fas fa-clipboard-list" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                    <p>You haven\'t joined any events yet.</p>
                                  </div>';
                        }
                        ?>
                    </div>
                    
                    <!-- Quick Contact Card -->
                    <div class="my-events-card">
                        <div class="card-header">
                            <h3><i class="fas fa-headset"></i> Need Help?</h3>
                        </div>
                        <p style="color: #666; font-size: 0.9rem; line-height: 1.5;">
                            Have questions about an event? Contact the admin team.
                        </p>
                        <a href="mailto:admin@college.edu" style="display: block; margin-top: 15px; color: #0d47a1; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-envelope"></i> Contact Admin
                        </a>
                    </div>
                </div>

                <!-- Main Feed: All Events -->
                <div class="events-feed">
                    <div class="feed-header">
                        <h2 class="feed-title">Upcoming Events</h2>
                        <input type="text" placeholder="Search events..." class="search-box" id="search-events">
                    </div>

                    <?php
                    // Fetch all events
                    $all_events_query = "SELECT * FROM events ORDER BY event_date DESC";
                    $all_events = mysqli_query($conn, $all_events_query);

                    if (mysqli_num_rows($all_events) > 0) {
                        while ($event = mysqli_fetch_assoc($all_events)) {
                            $is_upcoming = strtotime($event['event_date']) >= strtotime('today');
                            
                            // Check if user is already joined
                            $check_join = $conn->prepare("SELECT id FROM volunteers WHERE event_id = ? AND email = ?");
                            $check_join->bind_param("is", $event['id'], $user_email);
                            $check_join->execute();
                            $is_joined = $check_join->get_result()->num_rows > 0;
                            ?>
                            <div class="event-card">
                                <div class="event-info">
                                    <div class="event-main">
                                        <div class="event-icon">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                        <div>
                                            <div class="event-title-lg">
                                                <?php echo htmlspecialchars($event['title']); ?>
                                                <span class="event-sport-tag"><?php echo htmlspecialchars($event['sport_type']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="event-details-grid">
                                        <div class="detail-point">
                                            <i class="far fa-calendar-alt"></i> 
                                            <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                        </div>
                                        <div class="detail-point">
                                            <i class="far fa-clock"></i> 
                                            <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                                        </div>
                                        <div class="detail-point">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?php echo htmlspecialchars($event['venue']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="event-action">
                                    <?php if ($is_joined): ?>
                                        <span class="joined-badge">
                                            <i class="fas fa-check"></i> Registered
                                        </span>
                                    <?php elseif ($is_upcoming): ?>
                                        <button class="join-btn" onclick="openBookingModal(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['title'])); ?>')">
                                            Join Event
                                        </button>
                                    <?php else: ?>
                                        <span class="completed-badge">
                                            Completed
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="event-card" style="text-align: center; color: #666;">No events available at the moment.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- My Events Section -->
        <div id="my-events-section" class="tab-section">
            <h2 style="color: #0d47a1; margin-bottom: 30px;"><i class="fas fa-calendar-check"></i> My Events</h2>
            
            <?php
            // Get all user's registered events with full details
            $my_events_query = "SELECT v.*, e.* 
                                FROM volunteers v 
                                JOIN events e ON v.event_id = e.id 
                                WHERE v.email = ? 
                                ORDER BY e.event_date DESC";
            $stmt = $conn->prepare($my_events_query);
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $my_events = $stmt->get_result();

            if ($my_events->num_rows > 0) {
                echo '<div class="events-grid" style="grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">';
                while ($event = $my_events->fetch_assoc()) {
                    $is_upcoming = strtotime($event['event_date']) >= strtotime('today');
                    $status_class = $is_upcoming ? 'upcoming' : 'completed';
                    $status_text = $is_upcoming ? 'Upcoming' : 'Completed';
                    ?>
                    <div class="event-card" style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-left: 5px solid #0d47a1;">
                        <div class="event-header" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <div>
                                <h3 style="color: #0d47a1; font-size: 1.3rem; margin-bottom: 5px;"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <span style="background: #e3f2fd; color: #666; padding: 3px 10px; border-radius: 15px; font-size: 0.9rem; display: inline-block;">
                                    <?php echo htmlspecialchars($event['sport_type']); ?>
                                </span>
                            </div>
                            <span class="status-badge status-<?php echo $status_class; ?>" style="padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; height: fit-content;">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #555;">
                                <i class="far fa-calendar" style="color: #0d47a1; width: 20px;"></i>
                                <span><?php echo date('l, F j, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #555;">
                                <i class="far fa-clock" style="color: #0d47a1; width: 20px;"></i>
                                <span><?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #555;">
                                <i class="fas fa-map-marker-alt" style="color: #0d47a1; width: 20px;"></i>
                                <span><?php echo htmlspecialchars($event['venue']); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; color: #555;">
                                <i class="fas fa-user-tag" style="color: #0d47a1; width: 20px;"></i>
                                <span><strong>Your Role:</strong> <?php echo htmlspecialchars($event['assigned_role']); ?></span>
                            </div>
                        </div>
                        
                        <?php if ($event['description']): ?>
                        <div style="color: #666; line-height: 1.6; padding-top: 15px; border-top: 1px solid #eee;">
                            <?php echo htmlspecialchars($event['description']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                echo '<div style="text-align: center; padding: 60px 20px; color: #666;">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; color: #ddd; margin-bottom: 20px; display: block;"></i>
                        <h3>No Events Yet</h3>
                        <p>You haven\'t registered for any events. Check out the Home tab to browse available events!</p>
                      </div>';
            }
            ?>
        </div>

        <!-- Profile Section -->
        <div id="profile-section" class="tab-section">
            <h2 style="color: #0d47a1; margin-bottom: 30px;"><i class="fas fa-user-circle"></i> My Profile</h2>
            
            <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
                <!-- Profile Card -->
                <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; height: fit-content;">
                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #0d47a1, #42a5f5); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 3rem; font-weight: bold;">
                        <?php echo strtoupper(substr($user_name, 0, 2)); ?>
                    </div>
                    <h3 style="color: #333; margin-bottom: 5px;"><?php echo htmlspecialchars($user_name); ?></h3>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;"><?php echo htmlspecialchars($user_email); ?></p>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                        <div style="color: #666; font-size: 0.85rem; margin-bottom: 5px;">Member Since</div>
                        <div style="color: #333; font-weight: 600;">
                            <?php
                            $user_query = "SELECT created_at FROM users WHERE email = ?";
                            $stmt = $conn->prepare($user_query);
                            $stmt->bind_param("s", $user_email);
                            $stmt->execute();
                            $user_data = $stmt->get_result()->fetch_assoc();
                            echo date('F Y', strtotime($user_data['created_at'] ?? 'now'));
                            ?>
                        </div>
                    </div>
                    <div style="padding: 15px; background: #e3f2fd; border-radius: 8px;">
                        <div style="color: #0d47a1; font-size: 0.85rem; margin-bottom: 5px;">Role</div>
                        <div style="color: #0d47a1; font-weight: 600; text-transform: capitalize;">Student</div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e3f2fd;">
                        <i class="fas fa-info-circle"></i> Account Information
                    </h3>
                    
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; color: #666; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                                <?php echo htmlspecialchars($user_name); ?>
                            </div>
                        </div>
                        
                        <div>
                            <label style="display: block; color: #666; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                                <?php echo htmlspecialchars($user_email); ?>
                            </div>
                        </div>
                        
                        <div>
                            <label style="display: block; color: #666; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                                <i class="fas fa-chart-line"></i> Activity Summary
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 10px;">
                                <div style="padding: 20px; background: #e3f2fd; border-radius: 8px; text-align: center;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #0d47a1;"><?php echo $my_events_count; ?></div>
                                    <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">Total Events</div>
                                </div>
                                <div style="padding: 20px; background: #e8f5e8; border-radius: 8px; text-align: center;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #2e7d32;">
                                        <?php
                                        $active_query = "SELECT COUNT(*) as cnt FROM volunteers v JOIN events e ON v.event_id = e.id WHERE v.email = ? AND e.event_date >= CURDATE()";
                                        $stmt = $conn->prepare($active_query);
                                        $stmt->bind_param("s", $user_email);
                                        $stmt->execute();
                                        echo $stmt->get_result()->fetch_assoc()['cnt'];
                                        ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">Active</div>
                                </div>
                                <div style="padding: 20px; background: #fff3e0; border-radius: 8px; text-align: center;">
                                    <div style="font-size: 2rem; font-weight: bold; color: #ef6c00;">
                                        <?php
                                        $completed_query = "SELECT COUNT(*) as cnt FROM volunteers v JOIN events e ON v.event_id = e.id WHERE v.email = ? AND e.event_date < CURDATE()";
                                        $stmt = $conn->prepare($completed_query);
                                        $stmt->bind_param("s", $user_email);
                                        $stmt->execute();
                                        echo $stmt->get_result()->fetch_assoc()['cnt'];
                                        ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">Completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching with sections
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active to clicked
                this.classList.add('active');
                
                // Hide all sections
                document.querySelectorAll('.tab-section').forEach(section => section.classList.remove('active'));
                
                // Show corresponding section
                const linkText = this.textContent.trim().toLowerCase();
                if (linkText.includes('home')) {
                    document.getElementById('home-section').classList.add('active');
                } else if (linkText.includes('my events')) {
                    document.getElementById('my-events-section').classList.add('active');
                } else if (linkText.includes('profile')) {
                    document.getElementById('profile-section').classList.add('active');
                } else if (linkText.includes('notifications')) {
                    document.getElementById('notifications-section').classList.add('active');
                }
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        // Simple search functionality
        const searchBox = document.getElementById('search-events');
        if (searchBox) {
            searchBox.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const eventCards = document.querySelectorAll('#home-section .event-card');
                
                eventCards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Booking Modal
        function openBookingModal(eventId, eventTitle) {
            document.getElementById('booking-event-id').value = eventId;
            document.getElementById('booking-event-title').value = eventTitle;
            document.getElementById('booking-modal').style.display = 'flex';
        }
        
        function closeBookingModal() {
            document.getElementById('booking-modal').style.display = 'none';
        }
        
        // Close modal on outside click
        window.onclick = function(event) {
            if (event.target == document.getElementById('booking-modal')) {
                closeBookingModal();
            }
        }
    </script>

    <!-- Booking Modal -->
    <div id="booking-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
        <div style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #0d47a1; margin: 0;">Book Event Ground</h3>
                <button onclick="closeBookingModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <form action="book_event.php" method="POST">
                <input type="hidden" id="booking-event-id" name="event_id">
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Event</label>
                    <input type="text" id="booking-event-title" readonly style="width: 100%; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #666;">Select Ground</label>
                    <select name="ground_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">-- Choose a Ground --</option>
                        <?php
                        $g_query = "SELECT * FROM grounds WHERE status = 'available'";
                        $grounds = mysqli_query($conn, $g_query);
                        while($g = mysqli_fetch_assoc($grounds)) {
                            echo "<option value='".$g['id']."'>".$g['name']." (" . $g['location'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; color: #666;">Date</label>
                        <input type="date" name="booking_date" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; color: #666;">Time</label>
                        <input type="time" name="booking_time" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </div>
                
                <button type="submit" name="book_event" style="width: 100%; padding: 12px; background: #0d47a1; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <!-- Notifications Section -->
    <div id="notifications-section" class="tab-section">
        <h2 style="color: #0d47a1; margin-bottom: 30px;"><i class="fas fa-bell"></i> Notifications</h2>
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.05);">
            <?php
            $n_query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
            $n_stmt = $conn->prepare($n_query);
            $n_stmt->bind_param("i", $user_id);
            $n_stmt->execute();
            $notifs = $n_stmt->get_result();
            
            if ($notifs->num_rows > 0) {
                while($n = $notifs->fetch_assoc()) {
                    ?>
                    <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: start; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0d47a1;">
                            <i class="fas fa-info"></i>
                        </div>
                        <div>
                            <p style="margin-bottom: 5px; color: #333;"><?php echo htmlspecialchars($n['message']); ?></p>
                            <small style="color: #999;"><?php echo date('M d, h:i A', strtotime($n['created_at'])); ?></small>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="text-align: center; color: #666; padding: 20px;">No notifications yet.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
