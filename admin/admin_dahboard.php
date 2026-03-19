<?php
session_start();
include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_email']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch total counts
$totalUsersQuery = "SELECT COUNT(*) as total FROM user_registration";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];

$totalOwnersQuery = "SELECT COUNT(*) as total FROM owner_registration";
$totalOwnersResult = mysqli_query($conn, $totalOwnersQuery);
$totalOwners = mysqli_fetch_assoc($totalOwnersResult)['total'];

// Fetch all owners
$ownersQuery = "SELECT id, name, email, mobile, address FROM owner_registration ORDER BY id DESC";
$ownersResult = mysqli_query($conn, $ownersQuery);

// Fetch all users
$usersQuery = "SELECT id, username, email, mobile, gender FROM user_registration ORDER BY id DESC";
$usersResult = mysqli_query($conn, $usersQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Regal PG</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .logo-section {
            padding: 0 25px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1abc9c;
            margin-bottom: 5px;
        }

        .admin-badge {
            font-size: 12px;
            background: rgba(26, 188, 156, 0.2);
            color: #1abc9c;
            padding: 4px 12px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: #1abc9c;
        }

        .nav-item.active {
            background: rgba(26,188,156,0.2);
            border-left-color: #1abc9c;
            color: #1abc9c;
        }

        .nav-icon {
            font-size: 18px;
        }

        .logout-btn {
            position: absolute;
            bottom: 30px;
            left: 25px;
            right: 25px;
            padding: 12px;
            background: rgba(231,76,60,0.2);
            border: 2px solid #e74c3c;
            color: #e74c3c;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .logout-btn:hover {
            background: #e74c3c;
            color: white;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }

        .header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 16px;
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Dashboard Statistics */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #1abc9c, #16a085);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 500;
        }

        /* Search Bar */
        .search-bar {
            margin-bottom: 25px;
            position: relative;
        }

        .search-input {
            width: 100%;
            max-width: 500px;
            padding: 14px 20px 14px 45px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #1abc9c;
            box-shadow: 0 0 0 3px rgba(26,188,156,0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 18px;
        }

        /* Table */
        .data-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
        }

        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        td {
            padding: 18px 20px;
            font-size: 14px;
            color: #2c3e50;
        }

        .serial-no {
            font-weight: 600;
            color: #7f8c8d;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state-text {
            font-size: 18px;
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 20px;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-section">
            <div class="logo">Regal PG</div>
            <span class="admin-badge">ADMIN PANEL</span>
        </div>

        <ul class="nav-menu">
            <li class="nav-item active" onclick="switchTab('dashboard')">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </li>
            <li class="nav-item" onclick="switchTab('owners')">
                <span class="nav-icon">🏢</span>
                <span>Owner Details</span>
            </li>
            <li class="nav-item" onclick="switchTab('users')">
                <span class="nav-icon">👥</span>
                <span>User Data</span>
            </li>
        </ul>

        <button class="logout-btn" onclick="logout()">
            🚪 Logout
        </button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Tab -->
        <div id="dashboard-tab" class="tab-content active">
            <div class="header">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Overview of your platform statistics</p>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?php echo $totalUsers; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🏢</div>
                    <div class="stat-number"><?php echo $totalOwners; ?></div>
                    <div class="stat-label">Total Owners</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-number"><?php echo $totalUsers + $totalOwners; ?></div>
                    <div class="stat-label">Total Registrations</div>
                </div>
            </div>
        </div>

        <!-- Owner Details Tab -->
        <div id="owners-tab" class="tab-content">
            <div class="header">
                <h1 class="page-title">Owner Details</h1>
                <p class="page-subtitle">Manage all property owners</p>
            </div>

            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search by name, email or mobile..." onkeyup="searchTable(this.value, 'ownersTable')">
            </div>

            <div class="data-table">
                <?php if (mysqli_num_rows($ownersResult) > 0) { ?>
                <table id="ownersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($owner = mysqli_fetch_assoc($ownersResult)) { ?>
                        <tr>
                            <td class="serial-no"><?php echo $owner['id']; ?></td>
                            <td><?php echo htmlspecialchars($owner['name']); ?></td>
                            <td><?php echo htmlspecialchars($owner['email']); ?></td>
                            <td><?php echo htmlspecialchars($owner['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($owner['address']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🏢</div>
                    <div class="empty-state-text">No owners found</div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- User Data Tab -->
        <div id="users-tab" class="tab-content">
            <div class="header">
                <h1 class="page-title">User Data</h1>
                <p class="page-subtitle">Manage all registered users</p>
            </div>

            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search by username, email or mobile..." onkeyup="searchTable(this.value, 'usersTable')">
            </div>

            <div class="data-table">
                <?php if (mysqli_num_rows($usersResult) > 0) { ?>
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($usersResult)) { ?>
                        <tr>
                            <td class="serial-no"><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user['gender'])); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-state-icon">👥</div>
                    <div class="empty-state-text">No users found</div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active to clicked nav item
            event.target.closest('.nav-item').classList.add('active');
        }

        // Search functionality
        function searchTable(query, tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            query = query.toLowerCase();
            
            for (let row of rows) {
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                // Search in all text columns (skip ID)
                for (let i = 1; i < cells.length; i++) {
                    if (cells[i] && cells[i].textContent.toLowerCase().includes(query)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        }

        // Logout
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        }
    </script>
</body>
</html>