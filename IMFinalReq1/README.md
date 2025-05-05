# Multi-User System

## Overview
This is a multi-user system with three roles: Admin, Teacher, and User. It is built with PHP, MySQL, and Bootstrap for frontend styling. The system supports role-based login, dashboards, contact management, email verification, and security features.

## Features
- Role-based login and dashboard redirection
- Admin dashboard with user management, activation/deactivation, and logs
- Teacher dashboard with contact management
- User dashboard with contact management
- Email verification using PHPMailer
- Password hashing and session management
- Audit trail and activity logs
- Backup contacts as CSV (Admin only) - to be implemented
- Basic analytics with Chart.js - to be implemented

## Setup Instructions

1. Import the database schema:
   - Use the `database.sql` file to create the database and tables.

2. Configure database and mail settings:
   - Edit `config.php` with your MySQL credentials and SMTP mail settings.

3. Install PHPMailer:
   - Run `composer require phpmailer/phpmailer` in the project root.

4. Deploy on XAMPP or any PHP hosting.

5. Access the system via `login.php` and register new users.

## Notes
- Admin user must be created manually in the database or via admin panel (to be implemented).
- Some advanced features like CSV export and analytics are planned for future updates.

## License
MIT License
