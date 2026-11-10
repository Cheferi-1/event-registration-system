-- Create the event registration database
CREATE DATABASE IF NOT EXISTS lab05_events
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE lab05_events;

-- Create the event registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    event_name VARCHAR(150) NOT NULL,
    workshop VARCHAR(150) NOT NULL,
    registration_date DATE NOT NULL,
    special_requirements TEXT,
    status ENUM('Registered', 'Cancelled')
        DEFAULT 'Registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email_workshop (email, workshop)
);