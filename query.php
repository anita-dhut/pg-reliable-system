<?php 

    session_start();
    include("./connection.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require './PHPMailer/src/Exception.php';
    require './PHPMailer/src/PHPMailer.php';
    require './PHPMailer/src/SMTP.php';




// Redirect if already logged in
if (isset($_SESSION['user_type']) && isset($_SESSION['user_email'])) {
    $path = $_SESSION['user_type'] === 'owner' ? './Onwer/onwer_dashboard.php' : './User/user_dashboard.php';
    header("Location: $path");
    exit();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'] ?? '';
    $role = mysqli_real_escape_string($conn, $_POST['user_type'] ?? '');

    // Validate required fields
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = 'Missing required fields.';
        header("Location: ./login.php");
        exit();
    }

    // Determine table and dashboard
    if ($role === "owner") {
        $table = "owner_registration";
        $dashboard = "./Onwer/onwer_dashboard.php";
    } elseif ($role === "user") {
        $table = "user_registration";
        $dashboard = "./User/user_dashboard.php";
    } else {
        $_SESSION['error_message'] = 'Invalid user type.';
        header("Location: ./login.php");
        exit();
    }

    $query = "SELECT * FROM $table WHERE email='$email'";
    $res = mysqli_query($conn, $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);

        // Verify password (use password_verify() if hashed)
        if ($user['password'] === $password) {
            // ✅ Set session variables
            $_SESSION['user_type'] = $role;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            session_regenerate_id(true); // security

            // ✅ Clear localStorage on login to avoid conflicts
            echo "<script>
                localStorage.clear();
                window.location.href='$dashboard';
            </script>";
            exit();
        } else {
            $_SESSION['error_message'] = 'Invalid credentials.';
            header("Location: ./login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = ucfirst($role) . ' account not found!';
        header("Location: ./login.php");
        exit();
    }
}


if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    // Optional: Record logout time in DB
    if (isset($_SESSION['user_email'])) {
        $email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
        $update = "UPDATE user_registration SET last_logout = NOW() WHERE email = '$email'";
        mysqli_query($conn, $update);
    }

    // Clear all session data
    session_unset();
    session_destroy();

    // Redirect to index page after logout
    header("Location: ./index.php");
    exit();
}


// OTP Verification
if (isset($_POST['otp_verify'])) {
    $enteredOtp = mysqli_real_escape_string($conn, $_POST['otp']);

    // 🔹 Detect user type automatically from session
    if (isset($_SESSION['temp_owner'])) {
        $userType   = 'Owner';
        $tempData   = $_SESSION['temp_owner'];
        $table      = 'owner_registration';
        $sessionOtp = $_SESSION['owner_otp'] ?? null;
    } elseif (isset($_SESSION['temp_user'])) {
        $userType   = 'User';
        $tempData   = $_SESSION['temp_user'];
        $table      = 'user_registration';
        $sessionOtp = $_SESSION['user_otp'] ?? null;
    } else {
        echo "<script>alert('No registration session found. Please register again.'); window.location.href='./index.php';</script>";
        exit();
    }

    // 🔹 Check if OTP exists in session
    if (!$sessionOtp) {
        echo "<script>alert('OTP session expired. Please register again.'); window.location.href='./index.php';</script>";
        exit();
    }

    // 🔹 Verify OTP
    if ($enteredOtp != $sessionOtp) {
        echo "<script>alert('Invalid OTP! Please try again.'); window.location.href='./otp_verification.php';</script>";
        exit();
    }

    // 🔹 Remove 'role' field if it exists
    if (isset($tempData['role'])) {
        unset($tempData['role']);
    }

    // 🔹 Prepare insert query (clean data)
    $columns = implode(", ", array_keys($tempData));
    $values  = implode("', '", array_map(function($v) use ($conn) {
        return mysqli_real_escape_string($conn, $v);
    }, array_values($tempData)));

    $insertQuery = "INSERT INTO $table ($columns) VALUES ('$values')";

    // 🔹 Execute insert query
    if (mysqli_query($conn, $insertQuery)) {
        // 🔹 Clear sessions after successful registration
        if ($userType === 'Owner') {
            unset($_SESSION['temp_owner'], $_SESSION['owner_otp'], $_SESSION['owner_email']);
        } else {
            unset($_SESSION['temp_user'], $_SESSION['user_otp'], $_SESSION['user_email']);
        }

        echo "<script>
            alert('$userType registered successfully! You can now login.');
            window.location.href='./login.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Failed to save $userType data. Please try again.');
            window.location.href='./otp_verification.php';
        </script>";
        exit();
    }
}





// Determine user type and fetch OTP from session
if (isset($_SESSION['temp_user'])) {
    $userType = 'Owner';
    $sessionOtp = $_SESSION['owner_otp'] ?? null;
} elseif (isset($_SESSION['temp_user'])) {
    $userType = 'User';
    $sessionOtp = $_SESSION['user_otp'] ?? null;
} else {
    echo "<script>alert('Session expired or no registration found.'); window.location.href='./index.php';</script>";
    exit();
}

if (isset($_POST['otp_verify'])) {
    $enteredOtp = trim($_POST['otp']);

    // DEBUG: show entered OTP and session OTP
       // 🟦 Prepare readable session data for alert
    $sessionDataText = '';
    foreach ($tempData as $key => $value) {
        $sessionDataText .= ucfirst($key) . ': ' . $value . "\\n";
    }

    // 🟨 Show entered OTP, session OTP, and all session data
    echo "<script>
        alert(
            '🔍 DEBUG INFO:\\n' +
            'Entered OTP: $enteredOtp\\n' +
            'Session OTP: $sessionOtp\\n\\n' +
            'User Type: $userType\\n\\n' +
            '--- SESSION DATA ---\\n' +
            '$sessionDataText'
        );
    </script>";

    if (!$sessionOtp) {
        echo "<script>alert('OTP expired. Please register again.'); window.location.href='./index.php';</script>";
        exit();
    }

    if ($enteredOtp === (string)$sessionOtp) {
        echo "<script>
            alert('OTP verified successfully for $userType!');
            window.location.href='./login.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Invalid OTP. Please try again.');
            window.location.href='./otp_verification.php';
        </script>";
        exit();
    }
}



if (isset($_POST['email_verification'])) {
    $email = $_POST['email'];

    // Generate 5-digit OTP
    $otp = substr(str_shuffle("0123456789"), 0, 5);

    $query = "UPDATE owner_registration SET otp = '$otp' WHERE email = '$email'";
    $run = mysqli_query($conn, $query);

    if ($run) {
        try {
            $mail = new PHPMailer(true);

            $mail->SMTPDebug  = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ankushdhupe2022@gmail.com';
            $mail->Password   = 'ampjxclunkvhplsc'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('ankushdhupe2022@gmail.com', 'PG Reliable');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - PG Reliable';
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>We received a request to reset your password for your PG reliable account.</p>
                <p>Your OTP is:</p>
                <h3 style='color:#2d89ef; letter-spacing:2px;'>$otp</h3>
            ";
            $mail->AltBody = "Your password reset OTP is: $otp (valid for 10 minutes).";

            $mail->send();

            echo "<script>
                alert('An OTP has been send to your registered email Id ');
                window.location.href = './otp_password.php?email=$email';
            </script>";
        } catch (Exception $e) {
            echo "<script>
                alert('Error sending OTP: {$mail->ErrorInfo}');
                window.location.href = './email_verification.php';
            </script>";
        }
    } else {
        echo "<script> 
            alert('Email not found in our records. Please try again.');
            window.location.href = './email_verification.php';
        </script>";
        exit();
    }
}

    if(isset($_POST['reset_password'])){
        $newPassword  = $_POST['newPassword'];
        $otp          = $_POST['otp'];

        $query = "UPDATE owner_registration SET password = '$newPassword' WHERE otp = '$otp'";

        $login_query = "INSERT INTO login (email, password) VALUES ('$email', '$password')";
        
        $run = mysqli_query($conn, $query);
        $login_run   = mysqli_query($conn, $login_query);

        if($run && $login_run){
            echo "<script>
                alert('Password change successful! You can now login.');
                window.location.href = './login.php';
            </script>";
        }
    }



?>