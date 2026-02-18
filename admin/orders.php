<?php
/**
 * Manage Orders Page
 * AGRIBOOST SHOP - Admin Order Management
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

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = clean_input($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $message = 'Order status updated successfully!';
    }
    $stmt->close();
}

// Fetch all orders
$orders = $conn->query("SELECT o.*, u.name as user_name, u.email as user_email 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC");

// Include header
include '../includes/header.php';
?>

<style>
    .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }

    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th {
        background: linear-gradient(90deg,#2E6F40,#419759);
        color: white; padding: 1rem 1.2rem; text-align: left; font-size: .84rem; letter-spacing: .04em;
    }
    .orders-table td { padding: .85rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: .88rem; vertical-align: middle; }
    .orders-table tr:last-child td { border-bottom: none; }
    .orders-table tbody tr:hover { background: rgba(85,193,115,0.06); }

    .status-select {
        padding: .45rem .7rem; border: 1px solid rgba(46,111,64,0.3);
        border-radius: 8px; background: rgba(255,255,255,0.85); font-size: .85rem;
        cursor: pointer;
    }
    .btn-update { padding: .45rem .9rem; background: linear-gradient(135deg,#55C173,#2E6F40); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; box-shadow:0 3px 10px rgba(46,111,64,0.3); }
    .btn-view   { padding: .45rem .9rem; background: linear-gradient(135deg,#0c5460,#0a3d47); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-block; box-shadow:0 3px 10px rgba(0,0,0,0.2); }

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
            <li><span style="color:#fff;font-weight:600;">🛒 Orders</span></li>
        </ol>
    </nav>

    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">🛒 Manage Orders</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="glass-table">
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($order['user_name']); ?><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($order['user_email']); ?></small>
                        </td>
                        <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date('M j, Y, g:i a', strtotime($order['created_at'])); ?></td>
                        <td>
                            <button onclick="viewOrder(<?php echo $order['id']; ?>)" class="btn-view">View Details</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">No orders available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);z-index:1000;overflow-y:auto;">
    <div class="glass-card" style="max-width:700px;margin:3rem auto;padding:2rem;border-radius:16px;">
        <h3 style="color: #2E6F40; margin-bottom: 1rem;">Order Details</h3>
        <div id="orderDetails"></div>
        <button onclick="closeOrderModal()" style="margin-top: 1rem; padding: 0.75rem 1.5rem; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
    </div>
</div>

<script>
function viewOrder(orderId) {
    // Fetch order details via AJAX (simplified version)
    fetch('?ajax=1&order_id=' + orderId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('orderDetails').innerHTML = data;
            document.getElementById('orderModal').style.display = 'block';
        });
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}
</script>

<?php
// Handle AJAX request for order details
if (isset($_GET['ajax']) && isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    
    // Fetch order
    $stmt = $conn->prepare("SELECT o.*, u.name as user_name, u.email, u.phone 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Fetch order items
    $stmt = $conn->prepare("SELECT oi.*, p.name as product_name 
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();
    $stmt->close();
    
    echo '<div style="background-color: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">';
    echo '<p><strong>Customer:</strong> ' . htmlspecialchars($order['user_name']) . '</p>';
    echo '<p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>';
    echo '<p><strong>Phone:</strong> ' . htmlspecialchars($order['phone']) . '</p>';
    echo '<p><strong>Shipping Address:</strong><br>' . nl2br(htmlspecialchars($order['shipping_address'])) . '</p>';
    echo '<p><strong>Status:</strong> ' . ucfirst($order['status']) . '</p>';
    echo '<p><strong>Order Date:</strong> ' . date('F j, Y, g:i a', strtotime($order['created_at'])) . '</p>';
    echo '</div>';
    
    echo '<h4 style="margin-bottom: 0.5rem;">Order Items:</h4>';
    echo '<table style="width: 100%; border-collapse: collapse;">';
    echo '<thead><tr style="background-color: #f8f9fa;"><th style="padding: 0.5rem; text-align: left;">Product</th><th style="padding: 0.5rem;">Price</th><th style="padding: 0.5rem;">Qty</th><th style="padding: 0.5rem;">Subtotal</th></tr></thead>';
    echo '<tbody>';
    
    while ($item = $items->fetch_assoc()) {
        echo '<tr>';
        echo '<td style="padding: 0.5rem;">' . htmlspecialchars($item['product_name']) . '</td>';
        echo '<td style="padding: 0.5rem; text-align: center;">₹' . number_format($item['price'], 2) . '</td>';
        echo '<td style="padding: 0.5rem; text-align: center;">' . $item['quantity'] . '</td>';
        echo '<td style="padding: 0.5rem; text-align: center;">₹' . number_format($item['price'] * $item['quantity'], 2) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '<tfoot><tr style="font-weight: bold; background-color: #f8f9fa;"><td colspan="3" style="padding: 0.5rem; text-align: right;">Total:</td><td style="padding: 0.5rem; text-align: center;">₹' . number_format($order['total_amount'], 2) . '</td></tr></tfoot>';
    echo '</table>';
    
    exit();
}
?>

<?php include '../includes/footer.php'; ?>
