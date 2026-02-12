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
    .orders-container {
        max-width: 1000px;
        margin: 2rem auto;
    }
    
    .order-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eee;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-processing {
        background-color: #cfe2ff;
        color: #084298;
    }
    
    .status-shipped {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .status-delivered {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .order-items-table {
        width: 100%;
        margin-top: 1rem;
    }
    
    .order-items-table th {
        background-color: #f8f9fa;
        padding: 0.75rem;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }
    
    .order-items-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #eee;
    }
</style>

<div class="container orders-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">📦 My Orders</h2>
    
    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h3 style="color: #2E6F40; margin-bottom: 0.5rem;">Order #<?php echo $order['id']; ?></h3>
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
        <div style="background: white; padding: 3rem; text-align: center; border-radius: 10px;">
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 1rem;">No orders yet</p>
            <a href="../index.php" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #55C173; color: white; text-decoration: none; border-radius: 5px;">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
