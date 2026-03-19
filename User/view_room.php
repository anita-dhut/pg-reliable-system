<?php
include_once('../connection.php');

// Get the template ID from URL
$templateId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($templateId == 0) {
    echo "<script>alert('Invalid template!'); window.location.href='available_rooms.php';</script>";
    exit;
}

// Fetch owner details and rooms_photo
$query = "SELECT name, bulding_name, bulding_photo, bio, rooms_photo, email 
          FROM onwer_room_template 
          WHERE id = $templateId 
          LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Template not found!'); window.location.href='available_rooms.php';</script>";
    exit;
}

$owner = mysqli_fetch_assoc($result);
$roomsPhotos = [];

if (!empty($owner['rooms_photo'])) {
    $roomsPhotos = explode(',', $owner['rooms_photo']);
    $roomsPhotos = array_map('trim', $roomsPhotos);
    $roomsPhotos = array_filter($roomsPhotos);
}

// Handle Send Request
// Handle Send Request
if (isset($_POST['sendRequest'])) {
    session_start();
    $userEmail = $_SESSION['user_email'] ?? '';

    if (empty($userEmail)) {
        echo "<script>alert('Please login first!'); window.location.href='user_login.php';</script>";
        exit;
    }

    // Fetch user details from user_registration
    $userQuery = "SELECT name, email, mobile FROM user_registration WHERE email = '$userEmail' LIMIT 1";
    $userResult = mysqli_query($conn, $userQuery);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);

        // Insert request
        $insertQuery = "INSERT INTO room_request (name, email, mobile, status, onwer_name, onwer_email, bulding_name) 
                        VALUES (
                            '" . mysqli_real_escape_string($conn, $user['name']) . "',
                            '" . mysqli_real_escape_string($conn, $user['email']) . "',
                            '" . mysqli_real_escape_string($conn, $user['mobile']) . "',
                            'requesting',
                            '" . mysqli_real_escape_string($conn, $owner['name']) . "',
                            '" . mysqli_real_escape_string($conn, $owner['email']) . "',
                            '" . mysqli_real_escape_string($conn, $owner['bulding_name']) . "'
                        )";

        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Request sent successfully!'); window.location.href='View_room.php?id=$templateId';</script>";
        } else {
            echo "<script>alert('Failed to send request!');</script>";
        }
    } else {
        echo "<script>alert('User not found! Please login again.'); window.location.href='user_login.php';</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($owner['name']); ?>'s Rooms</title>
    <link rel="stylesheet" href="./user_css/view_rooms.css">

</head>
<body>
<div class="content-wrapper">
    <div class="button-container">
        <a href="available_rooms.php" class="back-button">← Back to Templates</a>
        
        <form method="POST" style="display: inline;">
            <input type="hidden" name="userEmail" id="userEmailInput">
            <button type="submit" name="sendRequest" class="request-button">📩 Send Request</button>
        </form>
    </div>

    <div class="owner-header">
        <img src="../assets/<?php echo htmlspecialchars($owner['bulding_photo']); ?>" 
             alt="Building" class="building-image">
        <div class="owner-details">
            <h1><?php echo htmlspecialchars($owner['name']); ?>'s <?php echo htmlspecialchars($owner['bulding_name']); ?></h1>
            <p><?php echo htmlspecialchars($owner['bio']); ?></p>
        </div>
    </div>

    <h2 class="page-title">Available Rooms</h2>

    <div class="rooms-grid">
        <?php if (count($roomsPhotos) > 0) { ?>
            <?php foreach ($roomsPhotos as $index => $photo) { ?>
                <div class="room-card">
                    <img src="../assets/<?php echo htmlspecialchars($photo); ?>" 
                         alt="Room <?php echo $index + 1; ?>" 
                         class="room-image">
                    <div class="room-info">
                        <div class="room-name">Room <?php echo $index + 1; ?></div>
                        <p style="color: #7f8c8d;">Available for viewing</p>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="empty-state">
                <h3>📸 No Rooms Available</h3>
                <p>This property hasn't uploaded any room photos yet.</p>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>