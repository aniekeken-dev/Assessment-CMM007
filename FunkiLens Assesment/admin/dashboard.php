<?php

require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users"))['count'] ?? 0;
$equipment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM equipment"))['count'] ?? 0;
$rentals_active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM rentals WHERE status = 'rented'"))['count'] ?? 0;
$rentals_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM rentals"))['count'] ?? 0;

$recent_rentals = mysqli_query($conn, "
    SELECT r.id, r.quantity, r.rent_date, r.status, u.name AS user_name, e.name AS equipment_name
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
    <link rel="icon" type="image/jpeg" href="../assets/logo.jpg">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="brand-lockup">
                <img src="../assets/logo.jpg" alt="FunkiLens Rentals logo">
                <div class="brand-text">
                    <h1>FunkiLens Rentals</h1>
                    <small>Admin</small>
                </div>
            </div>
            <nav class="nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="equipment.php">Equipment</a>
                <a href="users.php">Users</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Admin Dashboard</h2>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo (int) $equipment_count; ?></h3>
                <p>Total Equipment</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $users_count; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $rentals_active; ?></h3>
                <p>Active Rentals</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $rentals_total; ?></h3>
                <p>Total Rentals</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="margin-bottom: 1rem;">Recent Rentals</h2>
            <?php if ($recent_rentals && mysqli_num_rows($recent_rentals) > 0): ?>
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
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><?php echo (int) $row['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($row['rent_date']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status'] === 'rented' ? 'rented' : 'returned'; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No rental activity yet.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 0.85rem;">Quick Actions</h2>
            <p style="margin-bottom: 1rem;">Manage inventory, users, and rental activity from the admin area.</p>
            <a href="equipment.php" class="btn btn-primary">Manage Equipment</a>
            <a href="users.php" class="btn btn-secondary">Manage Users</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>
</body>
</html>
