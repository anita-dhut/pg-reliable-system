# 🏠 PG Reliable System

## 📌 Description
PG Reliable System is a web-based application developed using PHP and MySQL that allows users to search and apply for PG (Paying Guest) accommodations, while enabling owners to manage listings and user requests efficiently.

The system includes secure authentication with email OTP verification and role-based functionalities for both users and owners.

---

## 🚀 Key Features

### 👤 User Features
- Email OTP verification (using PHPMailer)
- User registration & login
- Dashboard access
- View available PG rooms
- Apply for rooms
- View application history

---

### 🏢 Owner Features
- Email OTP verification
- Add PG/hostel listings with images
- Manage user requests (Accept/Reject)
- View booking history and records

---

### 🔐 Common Features
- Secure authentication system
- Image upload functionality
- Full CRUD operations on PG listings

---

## 🔄 User Flow
1. User registers → OTP sent to email  
2. Verifies OTP  
3. Logs in and accesses dashboard  
4. Views available rooms  
5. Applies for a room  
6. Tracks application history  

---

## 🔄 Owner Flow
1. Owner registers → OTP sent to email  
2. Verifies OTP  
3. Logs in  
4. Adds PG/hostel listings  
5. Manages user requests (Accept/Reject)  
6. Views request history  

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, JavaScript, Bootstrap  
- **Backend:** PHP  
- **Database:** MySQL  
- **Email Service:** PHPMailer  
- **Tools:** XAMPP, VS Code  

---
## ⚙️ How to Run the Project

1. Install XAMPP and start Apache & MySQL
2. Copy project folder to htdocs
3. Import database file into phpMyAdmin
4. Open browser and run:
   http://localhost/pg-reliable-system

## 📸 Note
This project was developed as an academic project to demonstrate full-stack web development skills, including authentication, database integration, and role-based access control.
