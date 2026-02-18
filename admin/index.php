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
    .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }

    /* Stat cards */
    .stat-card {
        padding: 1.5rem 1.8rem;
        border-left: 4px solid transparent;
        border-image: linear-gradient(180deg,#55C173,#2E6F40) 1;
        transition: transform .25s, box-shadow .25s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.7); }
    .stat-card h3 { color: #419759; font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .4rem; font-weight: 600; }
    .stat-card .stat-value { font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg,#2E6F40,#55C173); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

    /* Quick action cards */
    .menu-card {
        padding: 1.6rem 1rem;
        text-align: center;
        text-decoration: none;
        color: #2E6F40;
        transition: transform .25s, box-shadow .25s;
        position: relative;
        overflow: hidden;
    }
    .menu-card::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(85,193,115,0.08), rgba(46,111,64,0.04));
        opacity: 0; transition: opacity .25s;
        border-radius: 16px;
    }
    .menu-card:hover::before { opacity: 1; }
    .menu-card:hover { transform: translateY(-6px); box-shadow: 0 14px 40px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.7); }
    .menu-card .icon { font-size: 2.5rem; margin-bottom: .5rem; display: block; }

    /* Table */
    .recent-orders-table { width: 100%; border-collapse: collapse; }
    .recent-orders-table th {
        background: linear-gradient(90deg,#2E6F40,#419759);
        color: white; padding: 1rem 1.2rem; text-align: left; font-size: .85rem; letter-spacing: .04em;
    }
    .recent-orders-table th:first-child { border-radius: 0; }
    .recent-orders-table td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: .88rem; }
    .recent-orders-table tr:last-child td { border-bottom: none; }
    .recent-orders-table tbody tr:hover { background: rgba(85,193,115,0.06); }

    /* Grids */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(230px,1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .admin-menu  { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 1rem; margin-bottom: 2rem; }

    /* Section headings */
    .section-heading { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
    .section-heading span { display: inline-block; width: 4px; height: 18px; background: linear-gradient(180deg,#55C173,#2E6F40); border-radius: 2px; }
</style>

<div class="container admin-container">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" style="margin-bottom: 1.2rem;">
        <ol class="glass-breadcrumb" style="display:flex;align-items:center;gap:.4rem;list-style:none;padding:.6rem 1rem;border-radius:10px;font-size:.85rem;flex-wrap:wrap;">
            <li><a href="/agriboost-shop/index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">🏠 Home</a></li>
            <li style="color:rgba(255,255,255,0.5);">›</li>
            <li><span style="color:#fff;font-weight:600;">📊 Dashboard</span></li>
        </ol>
    </nav>

    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">📊 Admin Dashboard</h2>
    <p style="margin-bottom:2rem;color:rgba(255,255,255,0.75);font-size:.9rem;">Welcome back, <strong style="color:#6AEC8E;"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>!</p>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="glass-card stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $stats['users']; ?></div>
        </div>
        <div class="glass-card stat-card">
            <h3>Total Products</h3>
            <div class="stat-value"><?php echo $stats['products']; ?></div>
        </div>
        <div class="glass-card stat-card">
            <h3>Total Orders</h3>
            <div class="stat-value"><?php echo $stats['orders']; ?></div>
        </div>
        <div class="glass-card stat-card">
            <h3>Total Revenue</h3>
            <div class="stat-value">₹<?php echo number_format($stats['revenue'], 2); ?></div>
        </div>
    </div>
    
    <!-- Admin Menu -->
    <p class="section-heading"><span></span> Quick Actions</p>
    <div class="admin-menu">
        <a href="products.php" class="glass-card menu-card">
            <span class="icon">📦</span>
            <strong>Manage Products</strong>
        </a>
        <a href="orders.php" class="glass-card menu-card">
            <span class="icon">🛒</span>
            <strong>Manage Orders</strong>
        </a>
        <a href="users.php" class="glass-card menu-card">
            <span class="icon">👥</span>
            <strong>View Users</strong>
        </a>
        <a href="export_orders.php" class="glass-card menu-card">
            <span class="icon">📄</span>
            <strong>Export Orders</strong>
        </a>
    </div>
    
    <!-- Recent Orders -->
    <p class="section-heading"><span></span> Recent Orders</p>
    <div class="glass-table">
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
</div>

<?php include '../includes/footer.php'; ?>
