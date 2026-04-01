<?php

require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$equipment = mysqli_query($conn, "SELECT * FROM equipment ORDER BY created_at DESC, name ASC");
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Equipment - FunkiLens Rentals</title>
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
                <a href="dashboard.php">Dashboard</a>
                <a href="equipment.php" class="active">Equipment</a>
                <a href="users.php">Users</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Manage Equipment</h2>
                <p>Update inventory, quantities, and equipment condition from one place.</p>
            </div>
            <button type="button" onclick="openModal('addModal')" class="btn btn-primary">Add Equipment</button>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Serial Number</th>
                            <th>Condition</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($equipment && mysqli_num_rows($equipment) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($equipment)): ?>
                                <tr>
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($row['equipment_condition']); ?>">
                                            <?php echo htmlspecialchars($row['equipment_condition']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo (int) $row['quantity']; ?></td>
                                    <td>
                                        <a href="#" onclick='openEditModal(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>); return false;' class="btn btn-sm btn-primary">Edit</a>
                                        <a href="../actions/delete_equipment.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this equipment?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No equipment found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal">
            <h2>Add Equipment</h2>
            <form action="../actions/add_equipment.php" method="POST">
                <div class="form-group">
                    <label for="add_name">Name</label>
                    <input type="text" name="name" id="add_name" required>
                </div>
                <div class="form-group">
                    <label for="add_category">Category</label>
                    <input type="text" name="category" id="add_category" required>
                </div>
                <div class="form-group">
                    <label for="add_serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="add_serial_number" required>
                </div>
                <div class="form-group">
                    <label for="add_condition">Condition</label>
                    <select name="equipment_condition" id="add_condition" required>
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="add_quantity">Quantity</label>
                    <input type="number" name="quantity" id="add_quantity" min="1" value="1" required>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal">
            <h2>Edit Equipment</h2>
            <form action="../actions/edit_equipment.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <input type="text" name="category" id="edit_category" required>
                </div>
                <div class="form-group">
                    <label for="edit_serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="edit_serial_number" required>
                </div>
                <div class="form-group">
                    <label for="edit_condition">Condition</label>
                    <select name="equipment_condition" id="edit_condition" required>
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_quantity">Quantity</label>
                    <input type="number" name="quantity" id="edit_quantity" min="1" required>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>

    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function openEditModal(item) {
        document.getElementById('edit_id').value = item.id;
        document.getElementById('edit_name').value = item.name;
        document.getElementById('edit_category').value = item.category;
        document.getElementById('edit_serial_number').value = item.serial_number;
        document.getElementById('edit_condition').value = item.equipment_condition;
        document.getElementById('edit_quantity').value = item.quantity;
        openModal('editModal');
    }
    </script>
</body>
</html>
