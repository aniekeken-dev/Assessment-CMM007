<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$category = isset($_GET['category']) ? clean($_GET['category']) : '';

$where = "WHERE quantity > 0";
if ($search !== '') {
    $where .= " AND (name LIKE '%$search%' OR category LIKE '%$search%')";
}
if ($category !== '') {
    $where .= " AND category = '$category'";
}

$equipment = mysqli_query($conn, "SELECT * FROM equipment $where ORDER BY name ASC");
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM equipment ORDER BY category ASC");

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Equipment - FunkiLens Rentals</title>
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
                <a href="equipment.php" class="active">Browse Equipment</a>
                <a href="my_rentals.php">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Browse Equipment</h2>
                <p>Search the available inventory and create a rental in a few clicks.</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 1.5rem;">
            <form method="GET" class="search-form">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Search equipment or category" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="equipment.php" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 1rem;">Available Equipment</h2>
            <?php if ($equipment && mysqli_num_rows($equipment) > 0): ?>
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
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($row['equipment_condition']); ?>">
                                            <?php echo htmlspecialchars($row['equipment_condition']); ?>
                                        </span>
                                    </td>
                                    <td><span class="badge badge-available"><?php echo (int) $row['quantity']; ?> available</span></td>
                                    <td>
                                        <a href="#" onclick='openRentModal(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>); return false;' class="btn btn-sm btn-success">Rent</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No equipment matched your search.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="rentModal" class="modal-overlay">
        <div class="modal">
            <h2>Rent Equipment</h2>
            <form action="../actions/rent_equipment.php" method="POST">
                <input type="hidden" name="equipment_id" id="rent_equipment_id">

                <div class="form-group">
                    <label for="rent_equipment_name">Equipment Name</label>
                    <input type="text" id="rent_equipment_name" readonly>
                </div>

                <div class="form-group">
                    <label for="rent_available_qty">Available Quantity</label>
                    <input type="text" id="rent_available_qty" readonly>
                </div>

                <div class="form-group">
                    <label for="rent_quantity">Quantity to Rent</label>
                    <input type="number" name="quantity" id="rent_quantity" min="1" value="1" required>
                </div>

                <div class="form-group">
                    <label for="rent_date">Rent Date</label>
                    <input type="date" name="rent_date" id="rent_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="rent_price">Unit Price Per Day</label>
                    <input type="text" id="rent_price" readonly>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="paypal_demo">PayPal Demo</option>
                        <option value="pay_on_pickup">Pay on Pickup</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rent_total">Estimated Total</label>
                    <input type="text" id="rent_total" readonly>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">Continue</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>

    <script>
    function openRentModal(equipment) {
        document.getElementById('rent_equipment_id').value = equipment.id;
        document.getElementById('rent_equipment_name').value = equipment.name;
        document.getElementById('rent_available_qty').value = equipment.quantity;
        document.getElementById('rent_price').value = '$' + Number(equipment.rental_price).toFixed(2);
        document.getElementById('rent_quantity').max = equipment.quantity;
        document.getElementById('rent_quantity').value = 1;
        document.getElementById('payment_method').value = 'paypal_demo';
        updateRentalTotal();
        document.getElementById('rentModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('rentModal').style.display = 'none';
    }

    function updateRentalTotal() {
        const quantity = Number(document.getElementById('rent_quantity').value || 0);
        const unitPriceText = document.getElementById('rent_price').value.replace('$', '');
        const unitPrice = Number(unitPriceText || 0);
        document.getElementById('rent_total').value = '$' + (quantity * unitPrice).toFixed(2);
    }

    document.getElementById('rent_quantity').addEventListener('input', updateRentalTotal);
    </script>
</body>
</html>
