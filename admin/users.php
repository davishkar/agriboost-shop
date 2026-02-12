<?php
/**
 * View Users Page
 * AGRIBOOST SHOP - Admin User Management
 */

// Include database connection
require_once '../config/db.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all regular users (not admins)
$users = $conn->query("SELECT id, name, email, phone, address, created_at FROM users ORDER BY created_at DESC");

// Include header
include '../includes/header.php';
?>

<style>
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
    }
    
    .users-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .users-table th {
        background-color: #2E6F40;
        color: white;
        padding: 1rem;
        text-align: left;
    }
    
    .users-table td {
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }
    
    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: bold;
    }
    
    .role-admin {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .role-user {
        background-color: #d4edda;
        color: #155724;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .stat-card h3 {
        color: #2E6F40;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .stat-card p {
        color: #666;
        font-size: 0.9rem;
    }
</style>

<div class="container admin-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">👥 View Users</h2>
    
    <!-- User Statistics -->
    <div class="stats-grid">
        <?php
        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $total_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
        $new_users_today = $conn->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
        ?>
        <div class="stat-card">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_admins; ?></h3>
            <p>Total Admins</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $new_users_today; ?></h3>
            <p>New Today</p>
        </div>
    </div>
    
    <!-- Users Table -->
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
