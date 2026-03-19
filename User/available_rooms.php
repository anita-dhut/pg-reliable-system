<?php
include_once('../connection.php');

// Fetch owner templates from DB
$query = "SELECT id, name, bulding_name, bulding_photo, bio , room_type FROM onwer_room_template ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Available Rooms</title>
  <link rel="stylesheet" href="./user_css/available_rooms.css">
</head>
<body>
<div class="content-wrapper">
    <div class="header">
        <h1 class="page-title">Available Rooms</h1>
    </div>
    <p class="page-subtitle">Browse available owner templates and view their rooms</p>

    <div class="templates-grid">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="template-card">
                   <img src="../assets/<?php echo htmlspecialchars($row['bulding_photo']); ?>" 
     alt="Building Image" class="template-image">


                    <div class="template-info">
                        <div class="template-name">
                            Onwer :-  <?php echo htmlspecialchars($row['name']); ?>
                        </div>
                        <div class="template-name">
                            Bulding :-  <?php echo htmlspecialchars($row['bulding_name']); ?>
                        </div>
                        
                        <p class="template-name">
                            Avaliable For :-  <?php echo htmlspecialchars($row['room_type']); ?> 
                        </p>
                        
                        <p class="template-name">
                           Address :-  <?php echo htmlspecialchars($row['bio']); ?>
                        </p>
                        <!-- View Rooms Button -->
                        <button class="btn btn-primary" onclick="viewRooms(<?php echo $row['id']; ?>)">
                            View Rooms
                        </button>
                    </div>
                </div>
            <?php } ?>
        <?php else: ?>
            <p>No templates available right now.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    
    function viewRooms(id) {
        // Redirect to View_room.php with the id as a query parameter
        window.location.href = 'View_room.php?id=' + id;
    }
  
</script>
</body>
</html>
