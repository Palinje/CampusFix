# CampusFix

A modern, role-based campus maintenance request tracking system designed to streamline facility reporting and repairs.

## 🚀 Features

- **Role-Based Access Control (RBAC):** Distinct dashboards and capabilities for Students, Staff/Teachers, Maintenance workers, and Super Admins.
- **Smart Request Grouping:** Automatically groups duplicate maintenance requests (same location and problem type) to declutter the admin dashboard.
- **Secure Image Uploads:** Attach photos to requests (max 5MB) with strict MIME type validation and safe hashed filenames.
- **CSRF & XSS Protection:** Global CSRF token verification and robust output sanitization.
- **Rate Limiting:** Prevents spam by limiting users to a maximum number of requests per day and preventing excessive duplicate reports.

## 👥 User Roles

- **Student / Teacher / Staff:** Can submit new maintenance requests, attach photos, and track the status of their own requests.
- **Maintenance Worker:** Can view all campus requests, manage their status (Pending, In Progress, Completed), and delete grouped tickets.
- **Super Admin:** Has all Maintenance privileges, plus the ability to register new users, update user roles, and securely delete users and their associated data.

## 🛠️ Tech Stack

- **Backend:** PHP 8+ (Procedural structure with include-based guards)
- **Database:** MySQL / MariaDB (Interfaced via secure PDO prepared statements)
- **Frontend:** HTML5, CSS3 (Glassmorphism design system), Bootstrap 5
- **Icons:** FontAwesome

## ⚙️ Setup & Installation

1. Ensure you have a local server environment (like XAMPP, MAMP, or LAMP).
2. Clone this repository into your server's public directory (e.g., `htdocs` or `www`).
3. Set up the database:
   - The application automatically initializes the required tables via `config/db.php` upon first run.
   - Configure your database credentials in `config/db.php` if different from the default (`root` / no password).
4. **Initial Admin Account:** Since registration is guarded by `requireSuperAdmin()`, you will need to manually insert your first Admin account into the database using a tool like phpMyAdmin. Make sure to hash the password using PHP's `password_hash()` before inserting.

## 🐛 Recent Fixes

- Fixed admin dashboard desynchronization by ensuring status updates apply to all grouped duplicate tickets simultaneously.
- Resolved "ghost ticket" bugs during admin deletion workflows.
- Improved database integrity with strict cascading deletion when removing a user account.
- Enhanced status input validation.
