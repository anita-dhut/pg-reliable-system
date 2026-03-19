<?php
session_start();
include("../connection.php");

// Get logged-in owner email from session
$ownerEmail = $_SESSION['user_email'] ?? '';

// Fetch all pending requests for this owner
$requests = [];
if (!empty($ownerEmail)) {
    $query = "SELECT 
                id,
                name,
                email,
                mobile,
                bulding_name,
                status
              FROM room_request 
              WHERE onwer_email = '" . mysqli_real_escape_string($conn, $ownerEmail) . "'
              AND status = 'requesting'
              ORDER BY id DESC";
    
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Requests</title>
    <link rel="stylesheet" href="./onwer_css/current_request.css">

    <style>
        /* Loader overlay */
        #loaderOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div id="loaderOverlay">
    <div class="spinner"></div>
</div>

<div class="content-wrapper">
    <div class="header">
        <h1 class="page-title">Current Requests</h1>
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" placeholder="Search by name, email or contact number..." onkeyup="searchRequests(this.value)">
        </div>
    </div>
    <p class="page-subtitle">Review and manage pending room requests</p>

    <div class="requests-table">
        <table id="requestsTable">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>User Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Apartment Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0): ?>
                    <?php foreach ($requests as $index => $req): ?>
                        <tr>
                            <td class="serial-no"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($req['name']) ?></td>
                            <td><?= htmlspecialchars($req['mobile']) ?></td>
                            <td><?= htmlspecialchars($req['email']) ?></td>
                            <td><?= htmlspecialchars($req['bulding_name']) ?></td>
                            <td class="action-btns">
                                <form action="./onwer_query.php" method="POST" class="requestForm" style="display:inline;">
                                    <input type="hidden" name="requestId" value="<?= $req['id'] ?>">
                                    <button type="submit" name="acceptRequest" class="btn-accept">Accept</button>
                                </form>
                                <form action="./onwer_query.php" method="POST" class="requestForm" style="display:inline;">
                                    <input type="hidden" name="requestId" value="<?= $req['id'] ?>">
                                    <button type="submit" name="rejectRequest" class="btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:#7f8c8d;">
                            <div style="font-size:48px; margin-bottom:15px;">📭</div>
                            <h3 style="margin:0 0 10px; color:#2c3e50;">No Pending Requests</h3>
                            <p style="margin:0;">New room requests will appear here.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Search filter
    function searchRequests(query) {
        const table = document.getElementById('requestsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        query = query.toLowerCase();

        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let i = 1; i <= 3; i++) {
                if (cells[i] && cells[i].textContent.toLowerCase().includes(query)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    }

    // Loader on form submit
    document.querySelectorAll('.requestForm').forEach(form => {
        form.addEventListener('submit', () => {
            document.getElementById('loaderOverlay').style.display = 'flex';
        });
    });
</script>
</body>
</html>
