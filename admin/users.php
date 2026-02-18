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
    .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }

    .users-table { width: 100%; border-collapse: collapse; }
    .users-table th {
        background: linear-gradient(90deg,#2E6F40,#419759);
        color: white; padding: 1rem 1.2rem; text-align: left; font-size: .84rem; letter-spacing: .04em;
    }
    .users-table td { padding: .85rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: .88rem; }
    .users-table tr:last-child td { border-bottom: none; }
    .users-table tbody tr:hover { background: rgba(85,193,115,0.06); }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .stat-card {
        padding: 1.4rem 1.6rem; text-align: center;
        transition: transform .25s, box-shadow .25s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.7); }
    .stat-card h3 { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg,#2E6F40,#55C173); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: .3rem; }
    .stat-card p  { color: #419759; font-size: .82rem; font-weight: 600; }

    .section-heading { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
    .section-heading span { display: inline-block; width: 4px; height: 18px; background: linear-gradient(180deg,#55C173,#2E6F40); border-radius: 2px; }
</style>

<div class="container admin-container">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" style="margin-bottom: 1.2rem;">
        <ol class="glass-breadcrumb" style="display:flex;align-items:center;gap:.4rem;list-style:none;padding:.6rem 1rem;border-radius:10px;font-size:.85rem;flex-wrap:wrap;">
            <li><a href="/agriboost-shop/index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">🏠 Home</a></li>
            <li style="color:rgba(255,255,255,0.5);">›</li>
            <li><a href="index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">📊 Dashboard</a></li>
            <li style="color:rgba(255,255,255,0.5);">›</li>
            <li><span style="color:#fff;font-weight:600;">👥 Users</span></li>
        </ol>
    </nav>

    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">👥 View Users</h2>
    
    <!-- User Statistics -->
    <div class="stats-grid">
        <?php
        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $total_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
        $new_users_today = $conn->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
        ?>
        <div class="glass-card stat-card">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="glass-card stat-card">
            <h3><?php echo $total_admins; ?></h3>
            <p>Total Admins</p>
        </div>
        <div class="glass-card stat-card">
            <h3><?php echo $new_users_today; ?></h3>
            <p>New Today</p>
        </div>
    </div>
    
    <!-- Users Table -->
    <p class="section-heading"><span></span> All Users</p>
    <div class="glass-table">
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
</div>

<?php include '../includes/footer.php'; ?>
