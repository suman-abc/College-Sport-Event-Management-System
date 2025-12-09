<?php
$host = "localhost";
$user = "root";
$pass = "suman";
$db = "sport_event";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function createUser($conn, $name, $email, $password, $role) {
    // Check if user exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo "User $email already exists.<br>";
        
        // Update role if needed (just to be sure)
        $update = $conn->prepare("UPDATE users SET role = ?, password = ? WHERE email = ?");
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update->bind_param("sss", $role, $hashed, $email);
        if ($update->execute()) {
             echo "Updated role/password for $email to $role.<br>";
        }
    } else {
        // Create user
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        
        if ($stmt->execute()) {
            echo "Created user $email with role $role.<br>";
        } else {
            echo "Error creating $email: " . $conn->error . "<br>";
        }
    }
}

// Create Admin
createUser($conn, "Admin User", "admin@gmail.com", "admin123", "admin");

// Create Standard User
createUser($conn, "Test Student", "user@gmail.com", "user123", "student");

echo "Setup complete.";
?>
