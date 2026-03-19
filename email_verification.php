<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/email_verification.css">
    <title>PG reliable - Email Verification</title>

</head>
<body>
    <div class="verification-container">
        <!-- Left Side - Welcome Section -->
        <div class="welcome-section">
            <div class="decorative-elements"></div>
            <div class="welcome-content">
                <div class="logo">PG reliable</div>
                <h1 class="welcome-title">Email Verification</h1>
                <h2 class="welcome-subtitle">Secure Your Account Access</h2>
                <p class="welcome-description">
                    We'll send a secure verification code to your email address to ensure account security and verify your identity before proceeding.
                </p>
                <ul class="verification-benefits">
                    <li>Instant OTP delivery to your email</li>
                    <li>Secure encrypted verification process</li>
                    <li>Valid for 10 minutes for your safety</li>
                    <li>No spam - only verification emails</li>
                    <li>One-click verification in email</li>
                    <li>24/7 customer support available</li>
                </ul>
            </div>
        </div>

        <!-- Right Side - Email Verification Form -->
        <div class="email-section">
            <div class="email-form-container">
                <div class="form-header">
                    <div class="verification-icon">✉️</div>
                    <h2 class="form-title">Verify Your Email</h2>
                    <p class="form-subtitle">
                        Enter your email address below and we'll send you a verification code to complete your account setup.
                    </p>
                    <div class="security-note">
                        🔒 Your email address will be kept secure and private
                    </div>
                </div>

                <div class="success-message" id="successMessage">
                    OTP sent successfully! Please check your email inbox.
                </div>

                <div class="error-message" id="errorMessage">
                    Failed to send OTP. Please check your email and try again.
                </div>

                <form action="./query.php" id="emailForm" method="POST" >
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="Enter your email address" required >
                            <span class="input-icon">📧</span>
                        </div>
                        <div class="email-validation" id="emailValidation"></div>
                    </div>

                    <button type="submit" name="email_verification" class="send-otp-btn" id="sendOtpBtn" disabled>
                        Send Verification Code
                    </button>
                </form>

                <div class="info-section">
                    <div class="info-title">What happens next?</div>
                    <ul class="info-list">
                        <li>We'll send a 5-digit OTP to your email</li>
                        <li>Check your inbox (and spam folder)</li>
                        <li>Enter the code on the next page</li>
                        <li>Your account will be verified instantly</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<script>
    const emailInput = document.getElementById('email');
    const emailValidation = document.getElementById('emailValidation');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const emailForm = document.getElementById('emailForm');

    // Email validation regex
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Real-time validation
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();

        if (validateEmail(email)) {
            emailValidation.className = 'email-validation valid';
            emailValidation.textContent = '✓ Valid email address';
            sendOtpBtn.disabled = false;
        } else {
            emailValidation.className = 'email-validation invalid';
            emailValidation.textContent = '✗ Invalid email address';
            sendOtpBtn.disabled = true;
        }
    });

    // On submit
    emailForm.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        if (!validateEmail(email)) {
            e.preventDefault(); // stop submission
            alert('Please enter a valid email address.');
        }
    });
</script>

</body>
</html>