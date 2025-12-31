-- Create grounds table
CREATE TABLE IF NOT EXISTS grounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('available', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    ground_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (ground_id) REFERENCES grounds(id) ON DELETE CASCADE
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add some sample grounds if table is empty
INSERT INTO grounds (name, location, description, status) 
SELECT * FROM (SELECT 'Main Stadium', 'North Campus', 'Primary stadium for football and athletics', 'available') AS tmp
WHERE NOT EXISTS (
    SELECT name FROM grounds WHERE name = 'Main Stadium'
) LIMIT 1;

INSERT INTO grounds (name, location, description, status) 
SELECT * FROM (SELECT 'Basketball Court 1', 'Sports Complex', 'Standard basketball court', 'available') AS tmp
WHERE NOT EXISTS (
    SELECT name FROM grounds WHERE name = 'Basketball Court 1'
) LIMIT 1;

INSERT INTO grounds (name, location, description, status) 
SELECT * FROM (SELECT 'Tennis Court A', 'South Wing', 'Clay court', 'maintenance') AS tmp
WHERE NOT EXISTS (
    SELECT name FROM grounds WHERE name = 'Tennis Court A'
) LIMIT 1;
