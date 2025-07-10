# NeonTask - Secure Task Management System

A modern, secure task management system with neon aesthetics, built with PHP and MySQL.

## Features

### 🔐 Security Features
- **Multi-Factor Authentication (MFA)** - 6-digit code verification
- **Account Lockout Protection** - Automatic lockout after 5 failed attempts (15-minute lockout)
- **Rate Limiting** - Prevents brute force attacks on login, registration, and MFA
- **Password Reset System** - Secure token-based password recovery
- **Input Sanitization** - Comprehensive XSS and SQL injection protection
- **CSRF Protection** - Cross-site request forgery prevention
- **Password Validation** - Strong password requirements (8+ chars, uppercase, lowercase, numbers)
- **Session Management** - Secure session handling

### 📋 Task Management
- Create, edit, delete, and complete tasks
- Task categorization (Personal, Work, Education, Health, Finance)
- Priority levels (Low, Medium, High)
- Due date tracking
- Task filtering and search
- Progress statistics and completion rates

### 👥 User Management
- User registration with validation
- Role-based access control (User/Admin)
- Admin dashboard with user monitoring
- Failed login attempt tracking

### 🎨 User Interface
- Neon cyberpunk theme
- Responsive design
- Real-time clock
- Interactive task filtering
- Success/error message notifications

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone or download the project**
   ```bash
   git clone <repository-url>
   cd SDLCpt2
   ```

2. **Configure Database**
   - Create a MySQL database named `neontask`
   - Update database credentials in `includes/db.php`:
     ```php
     $host = 'localhost';
     $db   = 'neontask';
     $user = 'your_username';
     $pass = 'your_password';
     ```

3. **Run Setup Script**
   - Navigate to `http://your-domain/setup.php`
   - This will create all necessary tables and an admin account
   - **Default Admin Credentials:**
     - Username: `admin`
     - Password: `Admin123!`
   - **IMPORTANT:** Delete `setup.php` after successful installation

4. **Access the Application**
   - Navigate to `http://your-domain/`
   - Login with admin credentials or register a new user account

## Security Features Explained

### Account Lockout System
- Tracks failed login attempts per user
- Locks account for 15 minutes after 5 failed attempts
- Automatically resets after lockout period expires

### Rate Limiting
- **Login:** 5 attempts per 5 minutes per IP
- **Registration:** 3 attempts per 10 minutes per IP
- **MFA Verification:** 5 attempts per 5 minutes per IP
- **Password Reset:** 3 attempts per 10 minutes per IP

### Input Validation
- Username: 3-20 characters, alphanumeric and underscores only
- Email: Valid email format validation
- Password: Minimum 8 characters with uppercase, lowercase, and numbers
- Task data: Length limits and category/priority validation

### Password Reset Process
1. User requests password reset via email
2. System generates secure random token
3. Token expires after 1 hour
4. User clicks reset link and sets new password
5. Token is invalidated after use

## File Structure

```
SDLCpt2/
├── admin/
│   └── dashboard.php          # Admin dashboard
├── assets/
│   ├── css/
│   │   ├── neon.css          # Main theme styles
│   │   └── admin.css         # Admin-specific styles
│   └── js/
│       ├── clock.js          # Real-time clock
│       ├── main.js           # Main JavaScript
│       └── mfa.js            # MFA timer functionality
├── includes/
│   ├── auth.php              # Authentication functions
│   ├── db.php                # Database connection
│   ├── functions.php         # Security and utility functions
│   └── schema.sql            # Database schema
├── user/
│   ├── dashboard.php         # User dashboard
│   ├── add_task.php          # Add new task
│   ├── edit_task.php         # Edit existing task
│   ├── update_task.php       # Update task status
│   └── delete_task.php       # Delete task
├── index.php                 # Login page
├── register.php              # User registration
├── mfa.php                   # MFA verification
├── forgot_password.php       # Password reset request
├── reset_password.php        # Password reset form
├── logout.php                # Logout functionality
├── setup.php                 # Database setup (delete after use)
└── README.md                 # This file
```

## Security Best Practices Implemented

1. **Prepared Statements** - All database queries use prepared statements
2. **Password Hashing** - bcrypt password hashing with PHP's password_hash()
3. **Session Security** - Secure session management and validation
4. **Input Sanitization** - Comprehensive input cleaning and validation
5. **Output Encoding** - HTML entity encoding for all user output
6. **Access Control** - Role-based permissions and ownership validation
7. **Error Handling** - Secure error messages without information disclosure

## Usage

### For Users
1. Register a new account or login
2. Complete MFA verification
3. Create and manage tasks from the dashboard
4. Use filters to organize tasks by status or priority
5. Track your progress with completion statistics

### For Administrators
1. Login with admin credentials
2. Monitor all users and their task statistics
3. View system-wide metrics
4. Track failed login attempts across users

## Maintenance

### Regular Tasks
- Monitor failed login attempts in admin dashboard
- Clean up old rate limit records (automatic)
- Review user activity and task completion rates

### Security Updates
- Regularly update PHP and MySQL versions
- Monitor for security patches
- Review and update password policies as needed

## Troubleshooting

### Common Issues
1. **Database Connection Error** - Check database credentials in `includes/db.php`
2. **Setup Script Not Working** - Ensure database exists and user has proper permissions
3. **MFA Not Working** - Check email configuration (currently simulated for demo)

### Support
For issues or questions, please check the code comments or create an issue in the repository.

## License

This project is open source and available under the MIT License.

---

**Note:** This is a demonstration system. For production use, implement proper email functionality for MFA and password reset features. 