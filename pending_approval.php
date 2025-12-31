<?php
session_start();

if (!isset($_SESSION['pending_approval'])) {
    header("Location: register.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Approval - College Sport Events</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }
        
        .container { 
            background: white; 
            padding: 50px; 
            border-radius: 15px; 
            width: 100%;
            max-width: 600px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 5px solid #0d47a1;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 2.5rem;
            color: #2e7d32;
        }
        
        h1 {
            color: #0d47a1;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        p {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        
        .info-box {
            background: #fff3e0;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #ff9800;
            text-align: left;
        }
        
        .info-box h3 {
            color: #ef6c00;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box ul {
            padding-left: 20px;
            color: #666;
        }
        
        .info-box li {
            margin-bottom: 8px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            background: #0d47a1;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .btn:hover {
            background: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.2);
        }
        
        .btn-secondary {
            background: #757575;
            margin-left: 15px;
        }
        
        .btn-secondary:hover {
            background: #616161;
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        
        .contact-info p {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="success-icon">
        <i class="fas fa-hourglass-half"></i>
    </div>
    
    <h1>Registration Submitted Successfully!</h1>
    <p>Your account registration has been received and is pending admin approval.</p>
    
    <div class="info-box">
        <h3><i class="fas fa-clock"></i> What happens next?</h3>
        <ul>
            <li>Your registration has been submitted to the admin for review</li>
            <li>You will receive an email notification once your account is approved</li>
            <li>Approval usually takes 24-48 hours during working days</li>
            <li>You can check your email for approval status updates</li>
        </ul>
    </div>
    
    <div style="margin-top: 30px;">
        <a href="index.html" class="btn">
            <i class="fas fa-home"></i> Return to Home
        </a>
        <a href="contact.php" class="btn btn-secondary">
            <i class="fas fa-envelope"></i> Contact Admin
        </a>
    </div>
    
    <div class="contact-info">
        <p><i class="fas fa-info-circle"></i> If you don't receive approval within 48 hours, please contact:</p>
        <p><strong>Email:</strong> sports@college.edu</p>
        <p><strong>Phone:</strong> (123) 456-7890</p>
    </div>
</div>
</body>
</html>
<?php
// Clear the pending approval flag
unset($_SESSION['pending_approval']);
?>