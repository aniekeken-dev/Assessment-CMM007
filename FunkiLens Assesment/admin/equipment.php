<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Equipment - FunkiLens Rentals</title>
    <link rel="stylesheet" href="stlye.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1> FunkiLens Rentals(Admin)</h1>
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
            <h2>Manage Equipment</h2>
            <button onclick="openModal('addModal')" class="btn btn-success">+ Add Equipment</button>
        </div>
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
                
        <div id="addModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <h2>Add Equipment</h2>
            <form action="../actions/add_equipment.php" method="POST">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="category" required placeholder="e.g., Electronics, Furniture">
                </div>
                <div class="form-group">
                    <label>Serial Number:</label>
                    <input type="text" name="serial_number" required>
                </div>
                <div class="form-group">
                    <label>Condition:</label>
                    <select name="equipment_condition" required>
                        <option value="Excellent">Excellent</option>
                        <option value="Good" selected>Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" required>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Equipment</button>
                </div>
            </form>
        </div>
    </div>   
    
        <div id="editModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <h2>Edit Equipment</h2>
            <form action="../actions/edit_equipment.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="category" id="edit_category" required>
                </div>
                <div class="form-group">
                    <label>Serial Number:</label>
                    <input type="text" name="serial_number" id="edit_serial_number" required>
                </div>
                <div class="form-group">
                    <label>Condition:</label>
                    <select name="equipment_condition" id="edit_condition" required>
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
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
        
        function openEditModal(user) {
            document.getElementById('edit_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_role').value = user.role;
            openModal('editModal');
        }
    </script>

</body>
</html>