<?php
$users = $data['users'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="topbar">
        <h1>Admin - Manage Users</h1>
        <div>
            <a class="btn" href="<?php echo URLROOT; ?>/admin/dashboard">Back to Dashboard</a>
            <a class="btn logout" href="<?php echo URLROOT; ?>/auth/logout">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>User List</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user->id); ?></td>
                            <td><?php echo htmlspecialchars($user->fullname); ?></td>
                            <td><?php echo htmlspecialchars($user->email); ?></td>
                            <td>
                                <form action="<?php echo URLROOT; ?>/admin/updateRole" method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo $user->id; ?>">

                                    <select name="role">
                                        <option value="admin" <?php echo ($user->role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="teacher" <?php echo ($user->role == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                                        <option value="parent" <?php echo ($user->role == 'parent') ? 'selected' : ''; ?>>Parent</option>
                                    </select>

                                    <button type="submit" class="btn small">Update</button>
                                </form>
                            </td>
                            <td>
                                <?php echo $user->is_verified ? 'Yes' : 'No'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user->created_at); ?></td>
                            <td>
                                <a class="btn danger small"
                                   href="<?php echo URLROOT; ?>/admin/deleteUser/<?php echo $user->id; ?>"
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>