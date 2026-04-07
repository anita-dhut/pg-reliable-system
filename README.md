# 🏠 PG Reliable System

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![PHPMailer](https://img.shields.io/badge/PHPMailer-FF6C37?style=for-the-badge&logoColor=white)

> A full-stack PG (Paying Guest) accommodation management system with **OTP-based email authentication**, **role-based access control**, and complete **CRUD operations** — built with PHP & MySQL.

---

## 📸 Screenshots

<!-- Replace with your actual screenshot filenames from the Screenshots/ folder -->
![Home Page](Screenshots/home.png)
![User Dashboard](Screenshots/user-dashboard.png)
![Owner Dashboard](Screenshots/owner-dashboard.png)

---

## ✨ Features

### 👤 User Panel
- 📧 **Email OTP Verification** via PHPMailer on registration
- 🔐 Secure login with session management
- 🏘️ Browse available PG/hostel listings
- 📝 Apply for rooms with one click
- 📋 Track application history and status

### 🏢 Owner Panel
- 📧 **Email OTP Verification** on registration
- ➕ Add PG/hostel listings with images
- ✅ Accept or ❌ Reject user room requests
- 📊 View and manage all booking records
- 🖼️ Image upload for property listings

### 🔐 Security & Architecture
- OTP-based two-factor authentication using **PHPMailer**
- **Role-based access control** — separate dashboards for users and owners
- Full **CRUD operations** on listings and requests
- Secure session handling and auth checks

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap |
| Backend | PHP |
| Database | MySQL |
| Email Service | PHPMailer |
| Server | XAMPP (Apache) |
| IDE | VS Code |

---

## 📁 Project Structure

```
pg-reliable-system/
├── User/                   # User-side pages & logic
├── Onwer/                  # Owner-side pages & logic
├── admin/                  # Admin panel
├── PHPMailer/              # Email library
├── assets/                 # Images & static files
├── css/                    # Stylesheets
├── Screenshots/            # App screenshots
├── index.php               # Landing page
├── login.php               # Login page
├── otp_verification.php    # OTP verification logic
├── otp_password.php        # OTP password reset
├── email_verification.php  # Email verification handler
├── connection.php          # Database connection
├── auth_check.php          # Auth middleware
├── query.php               # Database queries
└── pg_regal.sql            # Database file
```

---

## ⚙️ How to Run Locally

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- Any modern browser
- Gmail account (for PHPMailer SMTP)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/anita-dhut/pg-reliable-system.git
   ```

2. **Move to XAMPP's htdocs**
   ```
   C:/xampp/htdocs/pg-reliable-system
   ```

3. **Set up the database**
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a new database (e.g., `pg_regal`)
   - Import `pg_regal (1).sql`

4. **Configure PHPMailer** in `email_verification.php`:
   ```php
   $mail->Username = 'your-email@gmail.com';
   $mail->Password = 'your-app-password'; // Gmail App Password
   ```

5. **Run the app**
   ```
   http://localhost/pg-reliable-system
   ```

---

## 💡 Key Highlights

- Implements **OTP-based two-factor authentication** using PHPMailer — a real-world security feature used in production applications
- **Role-based access control** separates user and owner functionalities cleanly
- Supports **image uploads** for property listings
- Complete **request lifecycle management** — from room application to acceptance/rejection
- Structured codebase with **auth middleware** (`auth_check.php`) protecting restricted pages

---

## 🙋‍♀️ Author

**Anita Dhut**
- 🌐 [GitHub](https://github.com/anita-dhut)
- 💼 [LinkedIn](https://www.linkedin.com/in/anitadhut/)
- 📧 anitadhut12@gmail.com

---

*⭐ If you found this project helpful, consider giving it a star!*
