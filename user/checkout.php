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
        
        // Store pending order info in session and redirect to payment page
        $_SESSION['pending_order'] = [
            'shipping_address' => $shipping_address,
            'total'            => $total,
            'cart_snapshot'    => $_SESSION['cart'],
        ];
        
        header('Location: payment.php');
        exit();
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
    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">🛍️ Checkout</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
        <div class="glass-card" style="padding:1.6rem;margin-bottom:1.5rem;">
            <h3 style="color:#2E6F40;margin-bottom:1rem;font-weight:700;">Order Summary</h3>
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
        
        <div class="glass-card" style="padding:1.6rem;margin-bottom:1.5rem;">
            <h3 style="color:#2E6F40;margin-bottom:1rem;font-weight:700;">Shipping Information</h3>
            
            <form method="POST" action="">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly
                           style="width:100%;padding:.75rem .9rem;border:1px solid rgba(46,111,64,0.2);border-radius:8px;background:rgba(245,245,245,0.7);font-size:.9rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly
                           style="width:100%;padding:.75rem .9rem;border:1px solid rgba(46,111,64,0.2);border-radius:8px;background:rgba(245,245,245,0.7);font-size:.9rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Phone</label>
                    <input type="tel" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly
                           style="width:100%;padding:.75rem .9rem;border:1px solid rgba(46,111,64,0.2);border-radius:8px;background:rgba(245,245,245,0.7);font-size:.9rem;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Shipping Address *</label>
                    <textarea name="shipping_address" rows="4" required
                              style="width:100%;padding:.75rem .9rem;border:1px solid rgba(46,111,64,0.25);border-radius:8px;background:rgba(255,255,255,0.8);font-size:.9rem;"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="cart.php" style="flex:1;padding:.85rem;background:rgba(108,117,125,0.85);color:white;text-align:center;text-decoration:none;border-radius:10px;font-weight:600;backdrop-filter:blur(4px);">← Back to Cart</a>
                    <button type="submit" style="flex:1;padding:.85rem;background:linear-gradient(135deg,#55C173,#2E6F40);color:white;border:none;border-radius:10px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(46,111,64,0.4);transition:all .25s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">Continue to Payment →</button>
                </div>
            </form>
        </div>
</div>

<?php include '../includes/footer.php'; ?>
