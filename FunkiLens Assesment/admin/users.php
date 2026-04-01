<?php

require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FunkiLens Rentals</title>
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
                <a href="equipment.php">Equipment</a>
                <a href="users.php" class="active">Users</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Manage Users</h2>
                <p>Create, update, and remove accounts from one screen.</p>
            </div>
            <button type="button" onclick="openModal('addModal')" class="btn btn-primary">Add User</button>
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
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && mysqli_num_rows($users) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo htmlspecialchars($row['role']); ?>">
                                            <?php echo ucfirst($row['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                    <td>
                                        <a href="#" onclick='openEditModal(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>); return false;' class="btn btn-sm btn-primary">Edit</a>
                                        <?php if ((int) $row['id'] !== (int) $_SESSION['user_id']): ?>
                                            <a href="../actions/delete_user.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal">
            <h2>Add User</h2>
            <form action="../actions/add_user.php" method="POST">
                <div class="form-group">
                    <label for="add_name">Name</label>
                    <input type="text" name="name" id="add_name" required>
                </div>
                <div class="form-group">
                    <label for="add_email">Email</label>
                    <input type="email" name="email" id="add_email" required>
                </div>
                <div class="form-group">
                    <label for="add_password">Password</label>
                    <input type="password" name="password" id="add_password" required>
                </div>
                <div class="form-group">
                    <label for="add_role">Role</label>
                    <select name="role" id="add_role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal">
            <h2>Edit User</h2>
            <form action="../actions/edit_user.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label for="edit_password">New Password</label>
                    <input type="password" name="password" id="edit_password" placeholder="Leave blank to keep current password">
                </div>
                <div class="form-group">
                    <label for="edit_role">Role</label>
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
