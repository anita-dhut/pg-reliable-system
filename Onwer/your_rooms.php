<?php
session_start();
include("../connection.php");

// ✅ Redirect to login if not logged in or not owner
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'owner' || !isset($_SESSION['user_email'])) {
    header("Location: ../index.php");
    exit();
}
$ownerEmail = $_SESSION['user_email'] ?? '';

// Fetch rooms photos for this owner
$roomsPhotos = [];
if (!empty($ownerEmail)) {
    $query = "SELECT rooms_photo FROM onwer_room_template WHERE email = '" . mysqli_real_escape_string($conn, $ownerEmail) . "' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        if (!empty($data['rooms_photo'])) {
            $roomsPhotos = explode(',', $data['rooms_photo']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./onwer_css/your_rooms.css">
    <title>Your Rooms</title>

</head>
<body>
    
<div class="content-wrapper">
    <div class="header">
        <div class="title-container">
            <h1 class="page-title">Your Rooms</h1>
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="openUploadModal()">
                    <span>➕</span>
                    Add Images
                </button>
                <button class="btn btn-danger" onclick="toggleRemoveMode()" id="removeBtn">
                    <span>🗑️</span>
                    Remove Images
                </button>
            </div>
        </div>
        <p class="page-subtitle">Manage your property listings and room images</p>
    </div>

    <div class="rooms-grid" id="roomsGrid">
        <?php if (count($roomsPhotos) > 0) { ?>
            <?php foreach ($roomsPhotos as $index => $photo) { 
                $photo = trim($photo);
                if (!empty($photo)) { ?>
                    <div class="room-card" data-photo="<?php echo htmlspecialchars($photo); ?>">
                        <div class="select-checkbox">✓</div>
                        <img src="../assets/<?php echo htmlspecialchars($photo); ?>" alt="Room" class="room-image">
                        <div class="room-info">
                            <div class="room-name">Room <?php echo $index + 1; ?></div>
                            <div class="room-details">Property Image</div>
                        </div>
                    </div>
                <?php } 
            } ?>
        <?php } else { ?>
            <div class="empty-state">
                <h3>📸 No Room Images Yet</h3>
                <p>Click "Add Images" to upload your first room photos</p>
            </div>
        <?php } ?>
    </div>

    <!-- Delete Confirmation Button -->
    <div id="deleteConfirmBar" style="display: none; position: fixed; right: 20px; background: #e74c3c; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <button onclick="confirmDelete()" style="background: white; color: #e74c3c; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px;">Delete Selected</button>
        <button onclick="cancelDelete()" style="background: transparent; color: white; border: 2px solid white; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal" id="uploadModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Upload Room Images</h2>
        </div>

        <form action="./onwer_query.php" method="POST" enctype="multipart/form-data" id="uploadForm">
            <input type="hidden" name="ownerEmail" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">


            <label class="form-label" for="roomImages">Select Images (Multiple)</label>
            <input class="form-input" name="roomImages[]" type="file" id="roomImages" accept="image/*" multiple required>
            <small style="color: #7f8c8d; display: block; margin-top: -15px; margin-bottom: 20px;">You can select multiple images at once</small>
            
            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeUploadModal()">Cancel</button>
                <button class="btn-upload" name="addRoomImages" type="submit">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    let removeMode = false;


    function openUploadModal() {
        document.getElementById('uploadModal').classList.add('active');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('active');
    }

    function toggleRemoveMode() {
        removeMode = !removeMode;
        const roomsGrid = document.getElementById('roomsGrid');
        const removeBtn = document.getElementById('removeBtn');
        
        if (removeMode) {
            roomsGrid.classList.add('remove-mode');
            removeBtn.style.backgroundColor = '#c0392b';
            removeBtn.innerHTML = '<span>✖</span> Cancel Remove';
        } else {
            roomsGrid.classList.remove('remove-mode');
            removeBtn.style.backgroundColor = '';
            removeBtn.innerHTML = '<span>🗑️</span> Remove Images';
            document.querySelectorAll('.room-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('deleteConfirmBar').style.display = 'none';
        }
    }

    document.addEventListener('click', function(e) {
        if (removeMode && e.target.closest('.room-card')) {
            const card = e.target.closest('.room-card');
            card.classList.toggle('selected');
            
            const selectedCards = document.querySelectorAll('.room-card.selected');
            const deleteBar = document.getElementById('deleteConfirmBar');
            
            if (selectedCards.length > 0) {
                deleteBar.style.display = 'block';
            } else {
                deleteBar.style.display = 'none';
            }
        }
    });

    function confirmDelete() {
        const selectedCards = document.querySelectorAll('.room-card.selected');
        if (selectedCards.length === 0) {
            alert('Please select images to delete');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedCards.length} image(s)?`)) {
            const photoNames = [];
            selectedCards.forEach(card => {
                photoNames.push(card.getAttribute('data-photo'));
            });

            // ✅ Get owner email from PHP session
            const ownerEmail = "<?php echo $_SESSION['user_email']; ?>";

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = './onwer_query.php';

            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'ownerEmail';
            emailInput.value = ownerEmail;
            form.appendChild(emailInput);

            const photosInput = document.createElement('input');
            photosInput.type = 'hidden';
            photosInput.name = 'photosToDelete';
            photosInput.value = photoNames.join(',');
            form.appendChild(photosInput);

            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = 'removeRoomImages';
            submitInput.value = '1';
            form.appendChild(submitInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function cancelDelete() {
        toggleRemoveMode();
    }

    // Close modal when clicking outside
    document.getElementById('uploadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUploadModal();
        }
    });
</script>

</body>
</html>