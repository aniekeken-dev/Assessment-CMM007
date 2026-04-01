<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = (int) $_SESSION['user_id'];

$total_rentals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM rentals WHERE user_id = $user_id"))['count'] ?? 0;
$active_rentals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM rentals WHERE user_id = $user_id AND status = 'rented'"))['count'] ?? 0;
$returned_rentals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM rentals WHERE user_id = $user_id AND status = 'returned'"))['count'] ?? 0;

$active_rentals_list = mysqli_query($conn, "
    SELECT r.id, r.quantity, r.rent_date, e.name AS equipment_name, e.category
    FROM rentals r
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.user_id = $user_id AND r.status = 'rented'
    ORDER BY r.rent_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - FunkiLens Rentals</title>
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
                    <small>Rentals</small>
                </div>
            </div>
            <nav class="nav">
                <a href="userdashboard.php" class="active">Dashboard</a>
                <a href="equipment.php">Browse Equipment</a>
                <a href="my_rentals.php">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>User Dashboard</h2>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo (int) $active_rentals; ?></h3>
                <p>Active Rentals</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $returned_rentals; ?></h3>
                <p>Returned Items</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $total_rentals; ?></h3>
                <p>Total Rentals</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="margin-bottom: 1rem;">Active Rentals</h2>
            <?php if ($active_rentals_list && mysqli_num_rows($active_rentals_list) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Equipment</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Rent Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($active_rentals_list)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo (int) $row['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($row['rent_date']); ?></td>
                                    <td>
                                        <a href="../actions/return_equipment.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Return this equipment?')">Return</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>You have no active rentals right now.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 0.85rem;">Quick Actions</h2>
            <p style="margin-bottom: 1rem;">Browse available equipment or review all your rentals.</p>
            <a href="equipment.php" class="btn btn-primary">Browse Equipment</a>
            <a href="my_rentals.php" class="btn btn-secondary">View All Rentals</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>
</body>
</html>
