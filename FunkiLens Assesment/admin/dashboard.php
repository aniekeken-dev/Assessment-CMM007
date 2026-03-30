
<?php

require_once '../config/config.php';


if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}


$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$equipment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM equipment"))['count'];
$rentals_active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rentals WHERE status = 'rented'"))['count'];
$rentals_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rentals"))['count'];


$recent_rentals = mysqli_query($conn, "
    SELECT r.*, u.name as user_name, e.name as equipment_name 
    FROM rentals r 
    JOIN users u ON r.user_id = u.id 
    JOIN equipment e ON r.equipment_id = e.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FunkiLens Rentals</title>
    <link real="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1> FunkiLens Rentals (Admin)</h1>
            <nav class="nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="equipment.php">Equipment</a>
                <a href="users.php">Users</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $equipment_count; ?></h3>
                <p>Total Equipment</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $users_count; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $rentals_active; ?></h3>
                <p>Active Rentals</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $rentals_total; ?></h3>
                <p>Total Rentals</p>
            </div>
        </div>

         <div class="card">
            <h2>Recent Rentals</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Equipment</th>
                            <th>Quantity</th>
                            <th>Rent Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($recent_rentals)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['rent_date']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
         <div class="card">
            <h2>Quick Actions</h2>
            <p>
                <a href="equipment.php" class="btn btn-primary">Manage Equipment</a>
                <a href="users.php" class="btn btn-success">Manage Users</a>
            </p>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>
</body>
</html>