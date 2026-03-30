<?php

require_once '../config/config.php';


if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}


$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");


$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FunkiLens Rentals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>FunkiLens Rentals(Admin)</h1>
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
            <h2>Manage Users</h2>
            <button onclick="openModal('addModal')" class="btn btn-success">+ Add User</button>
        </div>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['role'] === 'admin' ? 'rented' : 'available'; ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="#" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-sm btn-primary">Edit</a>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="../actions/delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                         </tbody>
                </table>
            </div>
        </div>
    </div>

                <div id="addModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <h2>Add User</h2>
            <form action="../actions/add_user.php" method="POST">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
         <div id="editModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <h2>Edit User</h2>
            <form action="../actions/edit_user.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>New Password (leave blank to keep current):</label>
                    <input type="password" name="password" id="edit_password">
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" id="edit_role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals </p>
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