<?php
session_start();

// Display session messages
$successMessage = $_SESSION['success'] ?? '';
$errorMessage   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/user_registration.css">
<title>PG Reliable - User Registration</title>
<style>
  .feedback { font-size: 0.9em; margin-top: 5px; }
  .feedback.error { color: red; }
  .feedback.success { color: green; }
</style>
</head>
<body>

<div class="registration-container">
  <div class="form-header">
    <div class="logo">PG Reliable</div>
    <h2>User Registration</h2>
    <p>Create your account to find your perfect stay</p>
  </div>

  <?php if($successMessage): ?>
    <div class="message success-message"><?= htmlspecialchars($successMessage) ?></div>
  <?php elseif($errorMessage): ?>
    <div class="message error-message"><?= htmlspecialchars($errorMessage) ?></div>
  <?php endif; ?>

  <form id="registrationForm" method="POST" action="./user_query.php">
    <input type="hidden" name="role" value="user">

    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" required placeholder="Enter your full name" minlength="3">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email">
        <div id="emailFeedback" class="feedback"></div>
      </div>

      <div class="form-group">
        <label>Mobile Number *</label>
        <input type="tel" name="mobile" maxlength="10" pattern="[0-9]{10}" required placeholder="10-digit number">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Password *</label>
        <div class="input-wrapper">
          <input type="password" name="password" id="password" minlength="8" required placeholder="Create password">
          <span class="password-toggle" id="passwordToggle">👁️</span>
        </div>
        <div class="feedback" id="passwordStrength"></div>
      </div>

      <div class="form-group">
        <label>Confirm Password *</label>
        <div class="input-wrapper">
          <input type="password" id="confirmPassword" required placeholder="Confirm password">
          <span class="password-toggle" id="confirmPasswordToggle">👁️</span>
        </div>
        <div class="feedback" id="passwordMatch"></div>
      </div>
    </div>

    <div class="form-group">
      <label>Gender *</label>
      <select name="gender" id="gender" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>
    </div>

    <div class="button-group">
      <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
      <button type="submit" name="register" id="registerBtn" class="btn btn-primary">Create Account</button>
    </div>
  </form>
</div>

<script>
  // Password toggle
  function setupPasswordToggle(toggleId, inputId){
    const toggle = document.getElementById(toggleId);
    const input  = document.getElementById(inputId);
    toggle.addEventListener('click', () => {
      if(input.type === 'password'){
        input.type = 'text';
        toggle.textContent = '🙈';
      } else {
        input.type = 'password';
        toggle.textContent = '👁️';
      }
    });
  }
  setupPasswordToggle('passwordToggle','password');
  setupPasswordToggle('confirmPasswordToggle','confirmPassword');

  // Confirm password validation
  const form = document.getElementById('registrationForm');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const emailField = document.getElementById("email");
  const emailFeedback = document.getElementById("emailFeedback");

  // Check if email already exists
  emailField.addEventListener("blur", async () => {
      const email = emailField.value.trim();
      if(!email) return;

      const formData = new FormData();
      formData.append("email", email);
      formData.append("action", "check_email");

      try {
          const res = await fetch("./user_query.php", {
              method: "POST",
              body: formData
          });
          const data = await res.json();
          if(data.exists){
              emailFeedback.textContent = "Email already exists. Please use another.";
              emailFeedback.className = "email-feedback error";
          } else {
              emailFeedback.textContent = "Email available.";
              emailFeedback.className = "email-feedback success";
          }
      } catch(err){
          console.error("Email check error:", err);
          emailFeedback.textContent = "Error checking email.";
          emailFeedback.className = "email-feedback error";
      }
  });

  // Form submission validation
  form.addEventListener('submit', (e) => {
      if(password.value !== confirmPassword.value){
          e.preventDefault();
          alert("Passwords do not match.");
          return;
      }
      if(emailFeedback.className.includes("error")){
          e.preventDefault();
          alert("Email already exists. Please use another.");
          return;
      }
  });

  // Cancel button
  document.getElementById("cancelBtn").addEventListener("click", () => {
    if(confirm("Cancel registration?")){
      form.reset();
      window.location.href = "../index.php";
    }
  });
</script>


</body>
</html>
