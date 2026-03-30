<?php

require_once '../config/config.php';


if (!isLoggedIn()) {
    redirect('../login.php');
}


$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';


$where = "WHERE quantity > 0";
if (!empty($search)) {
    $where .= " AND (name LIKE '%$search%' OR category LIKE '%$search%')";
}
if (!empty($category)) {
    $where .= " AND category = '$category'";
}


$equipment = mysqli_query($conn, "SELECT * FROM equipment $where ORDER BY name ASC");


$categories = mysqli_query($conn, "SELECT DISTINCT category FROM equipment ORDER BY category");


$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Equipments - FunkiLens Rentals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <div class="header">
        <div class="header-content">
            <h1>FunkiLens Rentals</h1>
            <nav class="nav">
                <a href="userdashboard.php">Dashboard</a>
                <a href="equipment.php">Browse Equipment</a>
                <a href="my_rentals.php">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div> 
    <div class="container">
        <div class="page-header">
            <h2>User Dashboard</h2>
           </div>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        
        <div class="card">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search equipment..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="equipment.php" class="btn btn-secondary">Clear</a>
            </form>
        </div>
        
        
        <div class="card">
            <h2>Available Equipment</h2>
            <?php if (mysqli_num_rows($equipment) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Serial Number</th>
                                <th>Condition</th>
                                <th>Available</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($equipment)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['equipment_condition']); ?></td>
                                    <td>
                                        <?php if ($row['quantity'] > 0): ?>
                                            <span class="badge badge-available"><?php echo $row['quantity']; ?> available</span>
                                        <?php else: ?>
                                            <span class="badge badge-unavailable">Unavailable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['quantity'] > 0): ?>
                                        <a href="#" onclick="openRentModal(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-sm btn-success">Rent</a>
                                        <?php else: ?>
                                            <span class="badge badge-unavailable">Not Available</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No equipment found.</p>
            <?php endif; ?>
        </div>
    </div>
    
    
    <div id="rentModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <h2>Rent Equipment</h2>
            <form action="../actions/rent_equipment.php" method="POST">
                <input type="hidden" name="equipment_id" id="rent_equipment_id">
                <div class="form-group">
                    <label>Equipment Name:</label>
                    <input type="text" id="rent_equipment_name" readonly>
                </div>
                <div class="form-group">
                    <label>Available Quantity:</label>
                    <input type="text" id="rent_available_qty" readonly>
                </div>
                <div class="form-group">
                    <label>Quantity to Rent:</label>
                    <input type="number" name="quantity" id="rent_quantity" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Rent Date:</label>
                    <input type="date" name="rent_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Rental</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals@2026</p>
    </div>
    
    <script>
        function openRentModal(equipment) {
            document.getElementById('rent_equipment_id').value = equipment.id;
            document.getElementById('rent_equipment_name').value = equipment.name;
            document.getElementById('rent_available_qty').value = equipment.quantity;
            document.getElementById('rent_quantity').max = equipment.quantity;
            document.getElementById('rent_quantity').value = 1;
            document.getElementById('rentModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('rentModal').style.display = 'none';
        }
    </script>
</body>
</html>