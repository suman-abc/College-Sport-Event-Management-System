<?php
// Simple landing page with options
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Sport Events - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        /* Header */
        .header {
            margin-bottom: 50px;
            animation: fadeIn 1s ease-out;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: #0d47a1;
        }

        .header h1 {
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Options Grid */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .option-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
        }

        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            background: white;
        }

        .card-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.5rem;
        }

        .view-events .card-icon { background: #e3f2fd; color: #0d47a1; }
        .view-participants .card-icon { background: #f3e5f5; color: #7b1fa2; }
        .admin-login .card-icon { background: #e8f5e8; color: #2e7d32; }
        .user-login .card-icon { background: #fff3e0; color: #ef6c00; }

        .option-card h2 {
            margin-bottom: 15px;
            color: #0d47a1;
            font-size: 1.8rem;
        }

        .option-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            background: #0d47a1;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            background: #1565c0;
            transform: scale(1.05);
        }

        /* Quick Stats */
        .stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stats h2 {
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
        }

        .footer p {
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        .delay-4 { animation-delay: 0.8s; }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header p {
                font-size: 1.1rem;
            }
            
            .options-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .option-card {
                padding: 30px 20px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-links {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-running"></i>
                </div>
            </div>
            <h1>🏆 College Sport Events</h1>
            <p>Welcome to our Sports Event Management System. Choose an option below to get started!</p>
        </div>

        <!-- Main Options -->
        <div class="options-grid">
            <!-- View Events Option -->
            <a href="public_view.php" class="option-card view-events fade-in">
                <div class="card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2>View Events</h2>
                <p>Browse all upcoming and past sports events. See schedules, venues, and event details. No login required!</p>
                <div class="btn">
                    <i class="fas fa-eye"></i> Browse Events
                </div>
            </a>

            <!-- View Participants Option -->
            <a href="public_view.php#participants" class="option-card view-participants fade-in delay-1">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2>View Participants</h2>
                <p>See all registered participants and volunteers. View their assigned events and roles. Public access available.</p>
                <div class="btn">
                    <i class="fas fa-user-friends"></i> View Lists
                </div>
            </a>

            <!-- Admin Login Option -->
            <a href="index.html" class="option-card admin-login fade-in delay-2">
                <div class="card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2>Admin Login</h2>
                <p>Administrators can login here to manage events, participants, and volunteers. Full management access.</p>
                <div class="btn">
                    <i class="fas fa-sign-in-alt"></i> Admin Login
                </div>
            </a>

            <!-- User Registration Option -->
            <a href="register.php" class="option-card user-login fade-in delay-3">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Register / Login</h2>
                <p>Register as a new participant or volunteer, or login to your existing account to manage your profile.</p>
                <div class="btn">
                    <i class="fas fa-user-check"></i> Get Started
                </div>
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="stats fade-in delay-4">
            <h2><i class="fas fa-chart-line"></i> Quick Statistics</h2>
            <div class="stats-grid">
                <?php
                // Database connection for stats
                $host = "localhost";
                $user = "root";
                $pass = "suman";
                $db = "sport_event";
                $conn = mysqli_connect($host, $user, $pass, $db);
                
                if ($conn) {
                    $total_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
                    $upcoming_events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['total'];
                    $total_participants = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers")->fetch_assoc()['total'];
                    $active_volunteers = mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteers WHERE status = 'active'")->fetch_assoc()['total'];
                } else {
                    $total_events = $upcoming_events = $total_participants = $active_volunteers = 0;
                }
                ?>
                
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_events; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number"><?php echo $upcoming_events; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_participants; ?></div>
                    <div class="stat-label">Total Participants</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number"><?php echo $active_volunteers; ?></div>
                    <div class="stat-label">Active Volunteers</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>College Sport Events Management System © <?php echo date('Y'); ?></p>
            <p>Designed for students, participants, and administrators</p>
            
            <div class="footer-links">
                <a href="public_view.php">
                    <i class="fas fa-external-link-alt"></i> Public Access
                </a>
                <a href="index.html">
                    <i class="fas fa-lock"></i> Secure Login
                </a>
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Register
                </a>
                <a href="#">
                    <i class="fas fa-question-circle"></i> Help
                </a>
            </div>
        </div>
    </div>
</body>
</html>