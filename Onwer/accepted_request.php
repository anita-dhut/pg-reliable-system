<?php
session_start();
include("../connection.php");

// Get logged-in owner email from session or will be set via JavaScript
$ownerEmail = $_SESSION['user_email'] ?? '';

// Fetch ACCEPTED requests for this owner
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
              AND status = 'accepted'
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
    <link rel="stylesheet" href="./onwer_css/accepted_request.css">
    <title>Accepted Requests</title>
</head>
<body>
<div class="content-wrapper">
    <div class="header">
        <h1 class="page-title">Accepted Requests</h1>
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" placeholder="Search by name, email or contact number..." onkeyup="searchRequests(this.value)">
        </div>
    </div>
    <p class="page-subtitle">View all accepted room requests</p>

    <div class="requests-table">
        <table id="acceptedTable">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>User Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Apartment Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0) { ?>
                    <?php foreach ($requests as $index => $req) { ?>
                        <tr>
                            <td class="serial-no"><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($req['name']); ?></td>
                            <td><?php echo htmlspecialchars($req['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($req['email']); ?></td>
                            <td><?php echo htmlspecialchars($req['bulding_name']); ?></td>
                            <td><span class="status-badge status-accepted">Accepted</span></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #7f8c8d;">
                            <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                            <h3 style="margin: 0 0 10px 0; color: #2c3e50;">No Accepted Requests Yet</h3>
                            <p style="margin: 0;">Accepted requests will appear here once you approve them.</p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>

    function searchRequests(query) {
        const table = document.getElementById('acceptedTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        query = query.toLowerCase();
        
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let i = 1; i <= 3; i++) { // Search name, mobile, email
                if (cells[i] && cells[i].textContent.toLowerCase().includes(query)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    }
</script>
</body>
</html>