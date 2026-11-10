# LAB 05 - Event Registration

## Description

This project is an event registration system developed
using HTML, CSS, PHP and MySQL.

Participants can register for workshops and select
their preferred attendance mode.

## Technologies

- HTML
- CSS
- PHP
- MySQL
- PDO

## Database Setup

1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin.
3. Select the SQL tab.
4. Execute database.sql.
5. The database lab05_events will be created.

## Project Setup

1. Copy the project folder into C:\xampp\htdocs.
2. Start Apache and MySQL.
3. Open your browser.
4. Visit:

http://localhost/LAB05_Event_Registration/

## Validation Rules

- Participant name is required.
- Email is required and must be valid.
- Workshop is required.
- Attendance mode is required.
- Number of seats must be between 1 and 3.
- Special requirements cannot exceed 300 characters.
- Event terms must be accepted.
- The same email cannot register twice for the same workshop.

Phone number is included but optional.

## Database

Database: lab05_events

Table: registrations

## Security

- PDO is used for database connections.
- Prepared statements are used for insertion.
- User input is validated.
- Output is escaped using htmlspecialchars().

## Extension

The system displays registration totals and total seats
for each workshop.