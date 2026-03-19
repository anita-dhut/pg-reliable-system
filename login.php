<?php
session_start();

// Prevent caching - force browser to not cache this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include('connection.php'); // Your DB connection file

// If already logged in, redirect to respective dashboard
if (isset($_SESSION['user_type']) && isset($_SESSION['user_email']) && isset($_SESSION['logged_in'])) {
    $path = $_SESSION['user_type'] == 'owner' ? './Onwer/onwer_dashboard.php' : './User/user_dashboard.php';
    header("Location: $path");
    exit();
}

// Get error message if exists
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="./css/login.css">
    <title>PG Reliable - Login</title>
</head>
<body>
<div class="login-container">
    <!-- Left Side - Welcome Section -->
    <div class="welcome-section">
        <div class="decorative-elements"></div>
        <div class="welcome-content">
            <div class="logo">PG Reliable</div>
            <h1 class="welcome-title">Welcome Back!</h1>
            <h2 class="welcome-subtitle">Your Home Away From Home</h2>
            <p class="welcome-description">
                Experience comfortable and affordable paying guest accommodation designed specifically for students and professionals in Mumbai.
            </p>
            <ul class="features-list">
                <li>Fully furnished rooms with modern amenities</li>
                <li>24/7 security and safety measures</li>
                <li>High-speed WiFi and study areas</li>
                <li>Nutritious meals and kitchen facilities</li>
                <li>Prime location with easy transport access</li>
                <li>Friendly community environment</li>
            </ul>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-section">
        <div class="login-form-container">
            <div class="form-header">
                <h2 class="form-title">Sign In</h2>
                <p class="form-subtitle">Access your account to manage your stay</p>
                <!-- User Type Toggle -->
                <div class="user-type-toggle">
                    <div class="toggle-container">
                        <div class="toggle-slider" id="toggleSlider"></div>
                        <div class="toggle-option active" id="userToggle" data-type="user">User</div>
                        <div class="toggle-option" id="ownerToggle" data-type="owner">Owner</div>
                    </div>
                </div>
                <div class="user-type-badge" id="userTypeBadge">Login as User</div>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="display: block;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="./query.php">
                <input type="hidden" name="user_type" id="userType" value="user">
                <input type="hidden" name="login" value="1"> <!-- Hidden trigger for PHP -->
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" id="passwordToggle">👁️</span>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="rememberMe"> Remember me
                    </label>
                    <a href="./email_verification.php" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="login-btn" id="loginBtn">Sign In</button>
            </form>

            <div class="divider"><span>or</span></div>
            <div class="signup-link">
                Don't have an account? <a href="./index.php" id="signupLink">Sign up here</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Prevent browser back button after logout
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };

    // User type toggle functionality
    const userToggle = document.getElementById('userToggle');
    const ownerToggle = document.getElementById('ownerToggle');
    const toggleSlider = document.getElementById('toggleSlider');
    const userTypeBadge = document.getElementById('userTypeBadge');
    const userTypeInput = document.getElementById('userType');
    
    userToggle.addEventListener('click', function() {
        if (!this.classList.contains('active')) {
            toggleSlider.classList.remove('owner');
            userToggle.classList.add('active');
            ownerToggle.classList.remove('active');
            userTypeBadge.textContent = 'Login as User';
            userTypeInput.value = 'user';
        }
    });

    ownerToggle.addEventListener('click', function() {
        if (!this.classList.contains('active')) {
            toggleSlider.classList.add('owner');
            ownerToggle.classList.add('active');
            userToggle.classList.remove('active');
            userTypeBadge.textContent = 'Login as Owner';
            userTypeInput.value = 'owner';
        }
    });

    // Password toggle functionality
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');

    passwordToggle.addEventListener('click', function() {
        if(passwordInput.type === 'password'){
            passwordInput.type = 'text';
            passwordToggle.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            passwordToggle.textContent = '👁️';
        }
    });

    // Form submission with loading state
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', function(e) {
        // Show loading state
        loginBtn.disabled = true;
        loginBtn.textContent = 'Signing In...';
        loginBtn.style.opacity = '0.7';
    });
</script>
</body>
</html>