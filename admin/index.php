<?php
/**
 * Admin Dashboard
 * AGRIBOOST SHOP - Admin Panel
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

// Get statistics
$stats = array();

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $result->fetch_assoc()['count'];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'");
$stats['revenue'] = $result->fetch_assoc()['revenue'] ?? 0;

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.name as user_name FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               ORDER BY o.created_at DESC LIMIT 5");

// Include header
include '../includes/header.php';
?>

<style>
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #55C173;
    }
    
    .stat-card h3 {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }
    
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #2E6F40;
    }
    
    .admin-menu {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .menu-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        color: #2E6F40;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .menu-card .icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .recent-orders-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .recent-orders-table th {
        background-color: #2E6F40;
        color: white;
        padding: 1rem;
        text-align: left;
    }
    
    .recent-orders-table td {
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }
</style>

<div class="container admin-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">📊 Admin Dashboard</h2>
    <p style="margin-bottom: 2rem; color: #666;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $stats['users']; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="stat-value"><?php echo $stats['products']; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="stat-value"><?php echo $stats['orders']; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <div class="stat-value">₹<?php echo number_format($stats['revenue'], 2); ?></div>
        </div>
    </div>
    
    <!-- Admin Menu -->
    <h3 style="color: #2E6F40; margin-bottom: 1rem;">Quick Actions</h3>
    <div class="admin-menu">
        <a href="products.php" class="menu-card">
            <div class="icon">📦</div>
            <strong>Manage Products</strong>
        </a>
        <a href="orders.php" class="menu-card">
            <div class="icon">🛒</div>
            <strong>Manage Orders</strong>
        </a>
        <a href="users.php" class="menu-card">
            <div class="icon">👥</div>
            <strong>View Users</strong>
        </a>
        <a href="export_orders.php" class="menu-card">
            <div class="icon">📄</div>
            <strong>Export Orders</strong>
        </a>
    </div>
    
    <!-- Recent Orders -->
    <h3 style="color: #2E6F40; margin-bottom: 1rem;">Recent Orders</h3>
    <table class="recent-orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recent_orders->num_rows > 0): ?>
                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; background-color: #d1ecf1; color: #0c5460; border-radius: 15px; font-size: 0.85rem;">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #666;">No orders yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
