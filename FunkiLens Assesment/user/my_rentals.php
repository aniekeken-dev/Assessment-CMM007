<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>FunkiLens Rental</h1>
            <nav class="nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="equipment.php">Browse Equipment</a>
                <a href="my_rentals.php">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>My Rentals</h2>
            <a href="equipment.php" class="btn btn-success">+ Rent New Equipment</a>
        </div>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <?php if (mysqli_num_rows($rentals) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Rent Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($rentals)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['rent_date']; ?></td>
                                    <td><?php echo $row['return_date'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'rented'): ?>
                                            <span class="badge badge-rented">Rented</span>
                                        <?php else: ?>
                                            <span class="badge badge-returned">Returned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'rented'): ?>
                                            <a href="../actions/return_equipment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Return this equipment?')">Return</a>
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
                <p>You haven't rented any equipment yet.</p>
                <p><a href="equipment.php" class="btn btn-primary">Browse Equipment</a></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals @2026</p>
    </div>
</body>
</html>