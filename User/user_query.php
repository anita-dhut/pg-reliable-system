<?php
session_start();
include("../connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// 1️⃣ Email check (AJAX)
if(isset($_POST['action']) && $_POST['action'] === 'check_email'){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $check = mysqli_query($conn, "SELECT id FROM user_registration WHERE email='$email' LIMIT 1");
    echo json_encode(['exists' => mysqli_num_rows($check) > 0]);
    exit;
}

// 2️⃣ Registration & OTP sending
if(isset($_POST['register'])){
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile   = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $role     = $_POST['role'] ?? 'user';

    // Double-check email existence
    $checkEmailQuery = "SELECT id FROM user_registration WHERE email='$email' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkEmailQuery);
    if(mysqli_num_rows($checkResult) > 0){
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header("Location: ./user_registration.php");
        exit();
    }

    // Generate OTP
    $otp = rand(10000,99999);
    $_SESSION['user_otp']   = $otp;
    $_SESSION['user_email'] = $email;


    // Store in session temporarily
    $_SESSION['temp_user'] = [
        'name'     => $name,
        'email'    => $email,
        'mobile'   => $mobile,
        'password' => $password,
        'gender'   => $gender,
        'role'     => $role
    ];

  // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug  = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ankushdhupe2022@gmail.com';
        $mail->Password   = 'ampjxclunkvhplsc'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('ankushdhupe2022@gmail.com', 'PG Reliable');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification - PG Reliable';
        $mail->Body = "
            <h2>Hello $name,</h2>
            <p>Your OTP for account verification is:</p>
            <h3 style='color:#2d89ef;'>$otp</h3>
        ";
        $mail->AltBody = "Hello $name, Your OTP is: $otp";

        $mail->send();

        // Set success message and redirect to OTP verification page
        echo "<script>alert('Registration successful! OTP sent to your email.');
                window.location.href = '../otp_verification.php';
                </script>";
        exit;
    } catch (Exception $e) {
        // Set error message and redirect back to registration page
        echo "  <script>alert('Registration failed, please try again..');
                    window.location.href = '../user_registration.php';
                </script>";
        exit;
    }
}
?>



