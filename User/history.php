<?php
session_start();
include("../connection.php");

// Get user email from URL parameter or session
$userEmail = isset($_GET['user_email']) ? $_GET['user_email'] : ($_SESSION['user_email'] ?? '');

// Store in session for future use
if (!empty($userEmail)) {
    $_SESSION['user_email'] = $userEmail;
}

// Fetch ALL requests for this user
$requests = [];
if (!empty($userEmail)) {
    $query = "SELECT 
                id,
                onwer_name,
                onwer_email,
                bulding_name,
                status
              FROM room_request 
              WHERE email = '" . mysqli_real_escape_string($conn, $userEmail) . "'
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
    <link rel="stylesheet" href="./user_css//history.css">
    <title>History</title>
</head>
<body>
<div class="content-wrapper">
    <div class="header">
        <h1 class="page-title">History</h1>
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" placeholder="Search by owner name or email..." onkeyup="searchRequests(this.value)">
        </div>
    </div>
    <p class="page-subtitle">View all your past room requests</p>

    <div class="requests-table">
        <table id="requestsTable">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Apartment Name</th>
                    <th>Owner Name</th>
                    <th>Owner Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0) { ?>
                    <?php foreach ($requests as $index => $req) { ?>
                        <tr>
                            <td class="serial-no"><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($req['bulding_name']); ?></td>
                            <td><?php echo htmlspecialchars($req['onwer_name']); ?></td>
                            <td><?php echo htmlspecialchars($req['onwer_email']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($req['status']); ?>">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #7f8c8d;">
                            <div style="font-size: 48px; margin-bottom: 15px;">📜</div>
                            <h3 style="margin: 0 0 10px 0; color: #2c3e50;">No Request History</h3>
                            <p style="margin: 0;">Your request history will appear here once you start sending room requests.</p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>

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
</script>

</body>
</html>