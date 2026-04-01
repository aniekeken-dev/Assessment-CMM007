<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = (int) $_SESSION['user_id'];

$rentals = mysqli_query($conn, "
    SELECT r.id, r.quantity, r.rent_date, r.due_date, r.status, e.name AS equipment_name, e.category
    FROM rentals r
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.user_id = $user_id
    ORDER BY r.rent_date DESC, r.id DESC
");

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - FunkiLens Rentals</title>
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
                <a href="userdashboard.php">Dashboard</a>
                <a href="equipment.php">Browse Equipment</a>
                <a href="my_rentals.php" class="active">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>My Rentals</h2>
                <p>Track the items you currently have and your previous returns.</p>
            </div>
            <a href="equipment.php" class="btn btn-primary">Rent New Equipment</a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <?php if ($rentals && mysqli_num_rows($rentals) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Rent Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($rentals)): ?>
                                <tr>
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo (int) $row['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($row['rent_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status'] === 'rented' ? 'rented' : 'returned'; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'rented'): ?>
                                            <a href="../actions/return_equipment.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Return this equipment?')">Return</a>
                                        <?php else: ?>
                                            <span class="badge badge-returned">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="margin-bottom: 1rem;">You haven&apos;t rented any equipment yet.</p>
                <a href="equipment.php" class="btn btn-primary">Browse Equipment</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>
</body>
</html>
