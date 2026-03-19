<?php
session_start();
include("./connection.php"); // Database connection

// Check if user or owner is registering (session should contain temporary data)
if (isset($_SESSION['temp_owner'])) {
    $userType = 'Owner';
    $email = $_SESSION['temp_owner']['email'];
    $userName  = $_SESSION['temp_owner']['name'] ?? 'User';
    $userEmail = $_SESSION['temp_owner']['email'] ?? 'User Email';
    $userType  = $_SESSION['temp_owner']['user_type'] ?? 'Owner';

} elseif (isset($_SESSION['temp_user'])) {
    $userType = 'User';
    $email = $_SESSION['temp_user']['email'];
    $userName  = $_SESSION['temp_user']['name'] ?? 'User';
    $userEmail = $_SESSION['temp_user']['email'] ?? 'User Email';
    $userType  = $_SESSION['temp_user']['user_type'] ?? 'Owner';

} else {
    // No session data, redirect to index
    header("Location: ./index.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./css/otp_verification.css">
<title>PG reliable - OTP Verification</title>
</head>
<body>
<div class="verification-container">
    <!-- Left Side - Welcome Section -->
    <div class="welcome-section">
        <div class="decorative-elements"></div>
        <div class="welcome-content">
            <div class="logo">PG reliable</div>
            <h1 class="welcome-title">Secure Verification</h1>
            <h2 class="welcome-subtitle">Account Protection in Progress</h2>
            <p class="welcome-description">
                We've sent a secure verification code to protect your account and ensure only you can access your PG reliable dashboard.
            </p>
            <ul class="security-features">
                <li>Your account security is our top priority</li>
                <li>Encrypted verification process</li>
                <li>No personal data shared via SMS</li>
                <li>One-time use code for maximum security</li>
            </ul>
        </div>
    </div>

    <!-- Right Side - OTP Verification Form -->
    <div class="verification-section">
        <div class="verification-form-container">
            <div class="form-header">
                <div class="verification-icon">📱</div>
                <h2 class="form-title">Verify Your Account</h2>
                <div class="user-greeting">Hello, <?= $userName ?>!</div>
                <p class="form-subtitle">
                    We've sent a 5-digit verification code to your registered Email
                </p>
                <div class="email-info"><?= $userEmail ?></div>
            </div>

            <div class="success-message" id="successMessage">
                Verification successful! Redirecting to your dashboard...
            </div>

            <div class="error-message" id="errorMessage">
                Invalid OTP. Please check and try again.
            </div>

            <form action="./query.php" id="otpForm" autocomplete="off" method="POST">
                <div class="otp-container">
                    <div class="otp-inputs">
                        <input type="text" class="otp-input" maxlength="1" id="otp1">
                        <input type="text" class="otp-input" maxlength="1" id="otp2">
                        <input type="text" class="otp-input" maxlength="1" id="otp3">
                        <input type="text" class="otp-input" maxlength="1" id="otp4">
                        <input type="text" class="otp-input" maxlength="1" id="otp5">
                    </div>
                </div>

                <!-- Hidden input to store full OTP -->
                <input type="hidden" name="otp" id="hiddenOtp">
                <input type="hidden" name="user_type" value="<?= $userType ?>">

                <button type="submit" name="otp_verify" class="verify-btn" id="verifyBtn" disabled>
                    Verify Code
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const otpInputs = document.querySelectorAll('.otp-input');
    const verifyBtn = document.getElementById('verifyBtn');

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            if (!/^\d$/.test(value)) { e.target.value = ''; return; }
            if (value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
            checkOTPComplete();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpInputs[index - 1].focus();
                otpInputs[index - 1].value = '';
                checkOTPComplete();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 5);
            for (let i = 0; i < pasted.length && (index + i) < otpInputs.length; i++) {
                otpInputs[index + i].value = pasted[i];
            }
            checkOTPComplete();
        });
    });

    function checkOTPComplete() {
        const otpValue = Array.from(otpInputs).map(i => i.value).join('');
        verifyBtn.disabled = otpValue.length !== 5;
    }

    document.getElementById('otpForm').addEventListener('submit', function(e){
        const otpValue = Array.from(otpInputs).map(i => i.value).join('');
        document.getElementById('hiddenOtp').value = otpValue;
    });
</script>
</body>
</html>
