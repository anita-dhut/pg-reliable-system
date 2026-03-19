<?php
session_start();

// Handle logout
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    // Destroy PHP session
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_email'])) {
    header("Location: ../index.php");
    exit();
}

// Get user email from session
$userEmail = $_SESSION['user_email'] ?? '';

// Optional: Fetch user details
include('../connection.php');
$userQuery = "SELECT * FROM user_registration WHERE email = '" . mysqli_real_escape_string($conn, $userEmail) . "' LIMIT 1";
$userRes = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($userRes);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./user_css/user_dashboard.css">
    <title>User Dashboard - PG Reliable</title>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-section">
            <div class="logo">PG Reliable</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item active" onclick="loadTab('./available_rooms.php', event)">
                <span class="nav-icon">🏠</span>
                <span>Available Rooms</span>
            </li>
            <li class="nav-item" onclick="loadTab('./current_request.php', event)">
                <span class="nav-icon">📋</span>
                <span>Current Requested</span>
            </li>
            <li class="nav-item" onclick="loadTab('./accepted_request.php', event)">
                <span class="nav-icon">✅</span>
                <span>Accepted Requests</span>
            </li>
            <li class="nav-item" onclick="loadTab('./history.php', event)">
                <span class="nav-icon">📊</span>
                <span>History</span>
            </li>
        </ul>

        <div class="logout-section">
            <!-- Logout button -->
            <form method="POST" id="logoutForm">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="nav-item logout-btn">
                    <span class="nav-icon">🚪</span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-message">
                <div class="welcome-text">Welcome back,</div>
                <div class="user-name" id="userName">
                    <?php echo htmlspecialchars($userData['name'] ?? 'User'); ?>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <iframe class="tab-iframe" id="tabIframe" src="available_rooms.php"></iframe>
        </div>
    </div>

    <script>
    // Load tabs inside iframe
    function loadTab(url, event) {
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        event.target.closest('.nav-item').classList.add('active');
        document.getElementById('tabIframe').src = url;
    }

    // Clear local/session storage when logout
    document.getElementById('logoutForm').addEventListener('submit', function() {
        localStorage.clear();
        sessionStorage.clear();
    });
    </script>
</body>

</html>