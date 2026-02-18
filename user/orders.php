<?php
/**
 * Order History Page
 * AGRIBOOST SHOP - View User Orders
 */

// Include database connection
require_once '../config/db.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

// Include header
include '../includes/header.php';
?>

<style>
    .orders-container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }

    .order-card {
        padding: 1.6rem;
        margin-bottom: 1.5rem;
        transition: transform .25s, box-shadow .25s;
    }
    .order-card:hover { transform: translateY(-3px); box-shadow: 0 14px 40px rgba(0,0,0,0.16), inset 0 1px 0 rgba(255,255,255,0.7); }

    .order-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    .status-badge { padding: .4rem 1rem; border-radius: 20px; font-size: .82rem; font-weight: 700; }
    .status-pending        { background: rgba(255,243,205,0.9); color: #856404; }
    .status-confirmed      { background: rgba(209,250,229,0.9); color: #065f46; }
    .status-processing     { background: rgba(207,226,255,0.9); color: #084298; }
    .status-packed         { background: rgba(237,233,254,0.9); color: #5b21b6; }
    .status-out_for_delivery { background: rgba(255,237,213,0.9); color: #9a3412; }
    .status-shipped        { background: rgba(209,236,241,0.9); color: #0c5460; }
    .status-delivered      { background: rgba(209,250,229,0.9); color: #155724; }
    .status-cancelled      { background: rgba(254,226,226,0.9); color: #721c24; }

    .order-items-table { width: 100%; margin-top: 1rem; border-collapse: collapse; }
    .order-items-table th {
        background: linear-gradient(90deg,rgba(46,111,64,0.1),rgba(85,193,115,0.08));
        padding: .7rem 1rem; text-align: left; font-size: .83rem;
        border-bottom: 2px solid rgba(46,111,64,0.15); color: #2E6F40; font-weight: 700;
    }
    .order-items-table td { padding: .7rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: .88rem; }
    .order-items-table tr:last-child td { border-bottom: none; }
</style>

<div class="container orders-container">
    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">📦 My Orders</h2>
    
    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="glass-card order-card">
                <div class="order-header">
                    <div>
                        <h3 style="color:#2E6F40;margin-bottom:.4rem;font-weight:700;">Order #<?php echo $order['id']; ?></h3>
                        <p style="color: #666; font-size: 0.9rem;">
                            Placed on: <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo strtoupper($order['status']); ?>
                        </span>
                    </div>
                </div>
                
                <?php
                // Fetch order items
                $order_id = $order['id'];
                $items_sql = "SELECT oi.*, p.name FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?";
                $items_stmt = $conn->prepare($items_sql);
                $items_stmt->bind_param("i", $order_id);
                $items_stmt->execute();
                $items = $items_stmt->get_result();
                ?>
                
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php $items_stmt->close(); ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #eee;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Shipping Address:</strong><br>
                            <span style="color: #666;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                        </div>
                        <div style="text-align: right;">
                            <h3 style="color: #2E6F40;">Total: ₹<?php echo number_format($order['total_amount'], 2); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="glass-card" style="padding:3rem;text-align:center;">
            <p style="font-size:1.2rem;color:#2E6F40;margin-bottom:1rem;">No orders yet</p>
            <a href="../index.php" class="btn-checkout" style="display:inline-block;padding:.9rem 2rem;background:linear-gradient(135deg,#55C173,#2E6F40);color:white;text-decoration:none;border-radius:12px;font-weight:700;box-shadow:0 5px 16px rgba(46,111,64,0.4);">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
