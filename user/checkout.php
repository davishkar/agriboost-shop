<?php
/**
 * Checkout Page
 * AGRIBOOST SHOP - Order Processing
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

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$error = '';
$success = '';

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = clean_input($_POST['shipping_address']);
    
    if (empty($shipping_address)) {
        $error = 'Shipping address is required!';
    } else {
        // Calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
            $stmt->bind_param("ids", $user_id, $total, $shipping_address);
            $stmt->execute();
            $order_id = $conn->insert_id;
            $stmt->close();
            
            // Insert order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
                $stmt->execute();
                
                // Update product stock
                $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stmt->bind_param("ii", $item['quantity'], $product_id);
                $update_stmt->execute();
                $update_stmt->close();
            }
            $stmt->close();
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = array();
            
            $success = 'Order placed successfully! Order ID: #' . $order_id;
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $error = 'Order failed! Please try again.';
        }
    }
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Include header
include '../includes/header.php';
?>

<style>
    .checkout-container {
        max-width: 800px;
        margin: 2rem auto;
    }
    
    .checkout-section {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .order-summary-table {
        width: 100%;
        margin-top: 1rem;
    }
    
    .order-summary-table td {
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .order-summary-table tr:last-child td {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
        color: #2E6F40;
    }
</style>

<div class="container checkout-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">🛍️ Checkout</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <br><br>
            <a href="orders.php" style="color: #2E6F40; font-weight: bold;">View My Orders</a>
        </div>
    <?php else: ?>
        <div class="checkout-section">
            <h3 style="color: #2E6F40; margin-bottom: 1rem;">Order Summary</h3>
            <table class="order-summary-table">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Total Amount</td>
                    <td style="text-align: right;">₹<?php echo number_format($total, 2); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="checkout-section">
            <h3 style="color: #2E6F40; margin-bottom: 1rem;">Shipping Information</h3>
            
            <form method="POST" action="">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; background-color: #f5f5f5;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; background-color: #f5f5f5;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Phone</label>
                    <input type="tel" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; background-color: #f5f5f5;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Shipping Address *</label>
                    <textarea name="shipping_address" rows="4" required
                              style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="cart.php" style="flex: 1; padding: 0.75rem; background-color: #6c757d; color: white; text-align: center; text-decoration: none; border-radius: 5px;">
                        ← Back to Cart
                    </a>
                    <button type="submit" style="flex: 1; padding: 0.75rem; background-color: #55C173; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
