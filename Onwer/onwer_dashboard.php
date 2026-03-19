<?php
session_start();
include("../connection.php");


// Handle logout
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$ownerEmail = $_SESSION['user_email'] ?? '';

// Fetch owner details
$ownerQuery = "SELECT * FROM owner_registration WHERE email = '" . mysqli_real_escape_string($conn, $ownerEmail) . "' LIMIT 1";
$ownerRes = mysqli_query($conn, $ownerQuery);
$ownerData = mysqli_fetch_assoc($ownerRes);

// Fetch all templates for this owner
$allTemplatesQuery = "SELECT * FROM onwer_room_template WHERE email = '" . mysqli_real_escape_string($conn, $ownerEmail) . "' ORDER BY id DESC";
$allTemplatesRes = mysqli_query($conn, $allTemplatesQuery);
$templates = [];
while ($template = mysqli_fetch_assoc($allTemplatesRes)) {
    $templates[] = $template;
}
$templateCount = count($templates);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./onwer_css/onwer_dashboard.css">
<title>Owner Dashboard - Regal PG</title>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
        <div class="logo">PG Reliable</div>
    </div>
    <ul class="nav-menu">
        <li class="nav-item active" onclick="loadTab('./your_rooms.php')">
            <span class="nav-icon">🏠</span>
            <span>Your Rooms</span>
        </li>
        <li class="nav-item" onclick="loadTab('./current_request.php')">
            <span class="nav-icon">📋</span>
            <span>Current Requests</span>
        </li>
        <li class="nav-item" onclick="loadTab('./accepted_request.php')">
            <span class="nav-icon">✅</span>
            <span>Accepted Requests</span>
        </li>
        <li class="nav-item" onclick="loadTab('./history.php')">
            <span class="nav-icon">📊</span>
            <span>History</span>
        </li>
    </ul>
    <div class="logout-section">
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
            <div class="user-name"><?php echo htmlspecialchars($ownerData['name'] ?? 'Owner'); ?></div>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" type="button" onclick="openTemplateModal()">➕ Add Template</button>
            <button class="btn btn-primary" type="button" onclick="openViewTemplateModal()" style="margin-left:10px;">👁️ View Template</button>
        </div>
    </div>

    <div class="content-wrapper">
        <iframe class="tab-iframe" id="tabIframe" src="your_rooms.php"></iframe>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal" id="templateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add Template</h2>
        </div>
        <form action="./onwer_query.php" autocomplete="off" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ownerEmail" value="<?php echo htmlspecialchars($ownerEmail); ?>">

            <label class="form-label">Enter your name</label>
            <input class="form-input" type="text" name="ownerName" value="<?php echo htmlspecialchars($ownerData['name'] ?? ''); ?>" required>

            <label class="form-label">Hostel/Building Name</label>
            <input class="form-input" type="text" name="buildingName" required>

            <label class="form-label">Rooms Available For <span class="required">*</span></label>
            <div class="custom-dropdown">
                <div class="dropdown-selected" id="addRoomType-selected">
                    <span class="selected-text placeholder">Select accommodation type</span>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <div class="dropdown-options" id="addRoomType-options">
                    <div class="dropdown-option" data-value="Male">Male Only</div>
                    <div class="dropdown-option" data-value="Female">Female Only</div>
                    <div class="dropdown-option" data-value="Both">Both Male & Female</div>
                </div>
                <input type="hidden" name="roomType" id="addRoomType-input" required>
            </div>

            <label class="form-label">Building Image</label>
            <input class="form-input" type="file" name="buildingImage" accept="image/*" required>

            <label class="form-label">Bio</label>
            <textarea class="form-textarea" name="bio" rows="4" required></textarea>

            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeTemplateModal()">Cancel</button>
                <button class="btn-upload" name="saveTemplate" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- View Templates Modal -->
<div class="modal" id="viewTemplateModal">
    <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
        <div class="modal-header">
            <h2 class="modal-title">Your Templates</h2>
            <p style="color:#7f8c8d; font-size:14px;">Manage all your property templates</p>
        </div>

        <?php if($templateCount > 0): ?>
            <div style="margin-bottom:20px; padding:15px; background:#e8f5e9; border-radius:8px; border-left:4px solid #27ae60;">
                <strong style="color:#27ae60;">Total Templates: <?php echo $templateCount; ?></strong>
            </div>

            <div class="templates-list">
                <?php foreach($templates as $template): ?>
                    <div class="template-card" style="background:#f8f9fa; padding:20px; margin-bottom:20px; border-radius:12px; border:2px solid #e0e6ed;">
                        <div style="display:flex; gap:20px; align-items:flex-start;">
                            <img src="../assets/<?php echo htmlspecialchars($template['bulding_photo']); ?>" alt="Building" style="width:120px; height:120px; object-fit:cover; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            <div style="flex:1;">
                                <h3 style="color:#2c3e50; margin-bottom:10px; font-size:18px;"><?php echo htmlspecialchars($template['bulding_name']); ?></h3>
                                <p style="color:#7f8c8d; margin-bottom:8px;"><strong>Owner:</strong> <?php echo htmlspecialchars($template['name']); ?></p>
                                <p style="color:#7f8c8d; margin-bottom:8px; line-height:1.6;"><strong>Bio:</strong> <?php echo htmlspecialchars(substr($template['bio'],0,100)) . (strlen($template['bio'])>100?'...':''); ?></p>
                                <p style="color:#95a5a6; font-size:13px;"><strong>ID:</strong> #<?php echo $template['id']; ?></p>
                            </div>
                            <button class="btn-edit-template" onclick="openEditModal(<?php echo $template['id']; ?>)" style="padding:10px 20px; background:linear-gradient(135deg,#1abc9c,#16a085); color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">✏️ Edit</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="modal-actions" style="margin-top:20px;">
                <button class="btn-cancel" type="button" onclick="closeViewTemplateModal()">Close</button>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:40px;">
                <p style="color:#7f8c8d; font-size:16px; margin-bottom:20px;">📋 No templates found. Please add one first.</p>
                <div class="modal-actions" style="justify-content:center;">
                    <button class="btn-cancel" type="button" onclick="closeViewTemplateModal()">Close</button>
                    <button class="btn-upload" type="button" onclick="closeViewTemplateModal();openTemplateModal()">Add Template</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal" id="editTemplateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Template</h2>
        </div>
        <form id="editTemplateForm" action="./onwer_query.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="templateId" id="editTemplateId">
            <input type="hidden" name="ownerEmail" value="<?php echo htmlspecialchars($ownerEmail); ?>">

            <label class="form-label">Owner Name</label>
            <input class="form-input" type="text" name="ownerName" id="editOwnerName" required>

            <label class="form-label">Hostel/Building Name</label>
            <input class="form-input" type="text" name="buildingName" id="editBuildingName" required>

            <label class="form-label">Rooms Available For <span class="required">*</span></label>
            <div class="custom-dropdown">
                <div class="dropdown-selected" id="editRoomType-selected">
                    <span class="selected-text placeholder">Select accommodation type</span>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <div class="dropdown-options" id="editRoomType-options">
                    <div class="dropdown-option" data-value="Male">Male Only</div>
                    <div class="dropdown-option" data-value="Female">Female Only</div>
                    <div class="dropdown-option" data-value="Both">Both Male & Female</div>
                </div>
                <input type="hidden" name="roomType" id="editRoomType-input" required>
            </div>

            <label class="form-label">Current Building Image</label><br>
            <img id="editCurrentImage" src="" alt="Building" width="100" style="border-radius:8px; margin-bottom:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

            <label class="form-label">Upload New Image (Optional)</label>
            <input class="form-input" type="file" name="newbuildingImage" accept="image/*">

            <label class="form-label">Bio</label>
            <textarea class="form-textarea" name="bio" id="editBio" rows="4" required></textarea>

            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeEditModal()">Cancel</button>
                <button class="btn-upload" name="updateTemplate" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

    <script>
        // ---------- Dropdowns ----------
        document.addEventListener('DOMContentLoaded', function() {
            setupDropdown('addRoomType');
            setupDropdown('editRoomType');

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if(e.target === this) this.classList.remove('active');
                });
            });
        });

        function setupDropdown(idPrefix){
            const selected = document.getElementById(idPrefix + '-selected');
            const options = document.getElementById(idPrefix + '-options');
            const hiddenInput = document.getElementById(idPrefix + '-input');
            const optionItems = options.querySelectorAll('.dropdown-option');

            selected.addEventListener('click', function(e){ e.stopPropagation(); options.classList.toggle('open'); });

            optionItems.forEach(option=>{
                option.addEventListener('click', function(e){
                    e.stopPropagation();
                    selected.querySelector('.selected-text').textContent = this.textContent;
                    selected.querySelector('.selected-text').classList.remove('placeholder');
                    hiddenInput.value = this.dataset.value;
                    optionItems.forEach(opt=>opt.classList.remove('selected'));
                    this.classList.add('selected');
                    options.classList.remove('open');
                });
            });

            document.addEventListener('click', function(e){
                if(!selected.contains(e.target)) options.classList.remove('open');
            });
        }

        function resetDropdown(idPrefix){
            const selected = document.getElementById(idPrefix + '-selected');
            const options = document.getElementById(idPrefix + '-options');
            const hiddenInput = document.getElementById(idPrefix + '-input');
            const optionItems = options.querySelectorAll('.dropdown-option');
            selected.querySelector('.selected-text').textContent = 'Select accommodation type';
            selected.querySelector('.selected-text').classList.add('placeholder');
            hiddenInput.value = '';
            optionItems.forEach(opt=>opt.classList.remove('selected'));
            options.classList.remove('open');
        }

        // ---------- Tabs ----------
        function loadTab(url){
            document.querySelectorAll('.nav-item').forEach(item=>item.classList.remove('active'));
            event.target.closest('.nav-item').classList.add('active');
            document.getElementById('tabIframe').src = url;
        }

        // ---------- Modals ----------
        function openTemplateModal(){ document.getElementById('templateModal').classList.add('active'); }
        function closeTemplateModal(){
            if(confirm('Are you sure? All entered data will be lost.')){
                document.getElementById('templateModal').classList.remove('active');
                const addForm = document.querySelector('#templateModal form');
                addForm.reset();
                resetDropdown('addRoomType');
            }
        }
        function openViewTemplateModal(){ document.getElementById('viewTemplateModal').classList.add('active'); }
        function closeViewTemplateModal(){ document.getElementById('viewTemplateModal').classList.remove('active'); }

        // Templates JS for edit
        window.templates = <?php echo json_encode($templates); ?>;

        function openEditModal(templateId){
            closeViewTemplateModal();
            const template = window.templates.find(t=>parseInt(t.id)===parseInt(templateId));
            if(template){
                document.getElementById('editTemplateId').value = template.id;
                document.getElementById('editOwnerName').value = template.name;
                document.getElementById('editBuildingName').value = template.bulding_name;
                document.getElementById('editBio').value = template.bio;
                document.getElementById('editCurrentImage').src = '../assets/' + template.bulding_photo;

                const editSelectedText = document.getElementById('editRoomType-selected').querySelector('.selected-text');
                const editHiddenInput = document.getElementById('editRoomType-input');
                const editOptions = document.getElementById('editRoomType-options').querySelectorAll('.dropdown-option');
                let roomTypeText = 'Select accommodation type';
                let roomTypeValue = '';
                switch(template.room_type.toLowerCase()){
                    case 'male': roomTypeText='Male Only'; roomTypeValue='Male'; break;
                    case 'female': roomTypeText='Female Only'; roomTypeValue='Female'; break;
                    case 'both': roomTypeText='Both Male & Female'; roomTypeValue='Both'; break;
                }
                editSelectedText.textContent = roomTypeText;
                editSelectedText.classList.toggle('placeholder', !roomTypeValue);
                editHiddenInput.value = roomTypeValue;
                editOptions.forEach(opt=>opt.classList.remove('selected'));
                const selectedOpt = Array.from(editOptions).find(opt=>opt.dataset.value===roomTypeValue);
                if(selectedOpt) selectedOpt.classList.add('selected');

                document.getElementById('editTemplateModal').classList.add('active');
            } else { alert('Template not found'); }
        }

        function closeEditModal(){
            if(confirm('Are you sure? All changes will be lost.')){
                document.getElementById('editTemplateModal').classList.remove('active');
                document.getElementById('editTemplateForm').reset();
                resetDropdown('editRoomType');
            }
        }

        // Logout clears storage
        document.getElementById('logoutForm').addEventListener('submit', function(){
            localStorage.clear();
            sessionStorage.clear();
        });
    </script>
</body>
</html>
