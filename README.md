# NeonTask: Futuristic Task Management System

## Project Overview
A secure, neon-themed To-Do List web application built with PHP, featuring:
- User and Admin dashboards
- Multi-factor authentication (MFA)
- Real-time clock
- Task statistics and management
- Admin monitoring of user tasks

## Features
- User registration, login, and MFA
- Neon UI for all pages
- Add, edit, delete, and complete tasks
- Task statistics (total, completed, pending, completion rate)
- Real-time clock on dashboard
- Admin dashboard to monitor all users' tasks

## Project Structure
```
/SDLC2/
│
├── /assets/
│   ├── /css/
│   │   ├── neon.css
│   │   └── admin.css
│   └── /js/
│       ├── main.js
│       ├── clock.js
│       └── mfa.js
│
├── /includes/
│   ├── db.php
│   ├── auth.php
│   └── functions.php
│
├── /admin/
│   └── dashboard.php
│
├── /user/
│   └── dashboard.php
│
├── index.php         (Login page)
├── mfa.php           (MFA verification)
├── register.php      (User registration)
├── logout.php
└── README.md
```

## Setup Instructions
1. Place the `SDLC2` folder in your `htdocs` directory.
2. Import the provided SQL file into your database (see `/includes/db.php` for connection details).
3. Access the app via `http://localhost/SDLC2/`.

---

For more details, see the code and comments in each file. 