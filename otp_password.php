<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/otp_password.css">
  <title>PG reliable - Password Reset</title>
</head>
<body>
  <div class="reset-container">
    <!-- Left Side -->
    <div class="welcome-section">
      <div class="decorative-elements"></div>
      <div class="welcome-content">
        <div class="logo">PG reliable</div>
        <h1 class="welcome-title">Reset Password</h1>
        <h2 class="welcome-subtitle">Secure Account Recovery</h2>
        <p class="welcome-description">
          Follow our secure process to reset your password and regain access to your PG reliable account.
        </p>
      </div>
    </div>

    <!-- Right Side -->
    <div class="reset-section">
      <div class="reset-form-container">
        <div class="form-header">
          <div class="reset-icon">🔑</div>
          <h2 class="form-title">Reset Your Password</h2>
          <p class="form-subtitle">Enter the OTP you received and create a new password</p>
          <div class="email-info" id="emailInfo"></div>
        </div>

        <div class="error-message" id="errorMessage" style="display:none; color:red;">
          Invalid OTP or password mismatch. Please try again.
        </div>

        <form action="./query.php" id="resetForm" autocomplete="off" method="POST">
          <!-- OTP Section -->
          <div class="otp-section">
            <div class="otp-title">Step 1: Enter OTP Code</div>
            <div class="otp-inputs">
              <input type="text" class="otp-input" maxlength="1">
              <input type="text" class="otp-input" maxlength="1">
              <input type="text" class="otp-input" maxlength="1">
              <input type="text" class="otp-input" maxlength="1">
              <input type="text" class="otp-input" maxlength="1">
            </div>
            <input type="hidden" name="otp" id="hiddenOtp">
            <input type="hidden" name="email" id="hiddenEmail">
          </div>

          <!-- Password Section -->
          <div class="password-section">
            <div class="section-title">Step 2: Create New Password</div>
            
            <div class="form-group">
              <label for="newPassword">New Password <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" minlength="8" required>
                <span class="password-toggle" id="newPasswordToggle">👁️</span>
              </div>
              <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirm New Password <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required>
                <span class="password-toggle" id="confirmPasswordToggle">👁️</span>
              </div>
              <div class="password-match" id="passwordMatch"></div>
            </div>
          </div>

          <button type="submit" name="reset_password" class="reset-btn" id="resetBtn" disabled>
            Reset Password
          </button>
        </form>

        <div class="divider"><span>or</span></div>
        <div class="back-link">Remember your password? <a href="login.php">Back to Login</a></div>
      </div>
    </div>
  </div>

  <script>
    // ✅ Fill email from URL
    const urlParams = new URLSearchParams(window.location.search);
    const userEmail = urlParams.get('email') || '';
    document.getElementById('emailInfo').textContent = userEmail;
    document.getElementById('hiddenEmail').value = userEmail;

    // OTP Handling
    const otpInputs = document.querySelectorAll('.otp-input');
    const hiddenOtp = document.getElementById('hiddenOtp');

    window.addEventListener("DOMContentLoaded", () => {
      if (otpInputs.length > 0) otpInputs[0].focus();
    });

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', function(e) {
        const value = e.target.value;
        if (!/^\d$/.test(value)) { e.target.value = ''; return; }
        if (value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
        updateOtpValue();
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
          otpInputs[index - 1].focus();
          otpInputs[index - 1].value = '';
          updateOtpValue();
        }
      });
    });

    function updateOtpValue() {
      hiddenOtp.value = Array.from(otpInputs).map(i => i.value).join('');
    }

    // Password validation
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');
    const resetBtn = document.getElementById('resetBtn');

    newPasswordInput.addEventListener('input', function() {
      const value = this.value;
      let strength = 0;
      if (value.length >= 8) strength++;
      if (/[A-Z]/.test(value)) strength++;
      if (/[a-z]/.test(value)) strength++;
      if (/[0-9]/.test(value)) strength++;
      if (/[^A-Za-z0-9]/.test(value)) strength++;

      if (strength < 3) passwordStrength.textContent = 'Weak password';
      else if (strength < 4) passwordStrength.textContent = 'Medium password';
      else passwordStrength.textContent = 'Strong password';

      checkPasswordMatch();
      updateResetButton();
    });

    confirmPasswordInput.addEventListener('input', function() {
      checkPasswordMatch();
      updateResetButton();
    });

    function checkPasswordMatch() {
      const newPass = newPasswordInput.value;
      const confirmPass = confirmPasswordInput.value;
      if (newPass === confirmPass && confirmPass.length > 0) {
        passwordMatch.textContent = 'Passwords match';
        passwordMatch.style.color = 'green';
      } else if (confirmPass.length > 0) {
        passwordMatch.textContent = 'Passwords do not match';
        passwordMatch.style.color = 'red';
      } else {
        passwordMatch.textContent = '';
      }
    }

    function updateResetButton() {
      const newPass = newPasswordInput.value;
      const confirmPass = confirmPasswordInput.value;
      const passwordsMatch = newPass === confirmPass;
      const passwordValid = newPass.length >= 8;
      resetBtn.disabled = !(passwordValid && passwordsMatch);
    }

    // Password toggle
    function setupPasswordToggle(toggleId, inputId) {
      const toggle = document.getElementById(toggleId);
      const input = document.getElementById(inputId);
      toggle.addEventListener('click', () => {
        if (input.type === 'password') {
          input.type = 'text'; toggle.textContent = '🙈';
        } else {
          input.type = 'password'; toggle.textContent = '👁️';
        }
      });
    }
    setupPasswordToggle('newPasswordToggle', 'newPassword');
    setupPasswordToggle('confirmPasswordToggle', 'confirmPassword');
  </script>
</body>
</html>
