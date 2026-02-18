<?php
/**
 * Shopping Cart Page
 * AGRIBOOST SHOP - Session-based Cart
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

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$message = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $product_id = (int)$_POST['product_id'];
        
        // Check if product exists and has stock
        $stmt = $conn->prepare("SELECT name, price, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            if ($product['stock'] > 0) {
                // Add to cart or increase quantity
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                } else {
                    $_SESSION['cart'][$product_id] = array(
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => 1
                    );
                }
                $message = 'Product added to cart!';
            } else {
                $message = 'Product out of stock!';
            }
        }
        $stmt->close();
    } elseif ($action == 'update') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            $message = 'Cart updated!';
        } else {
            unset($_SESSION['cart'][$product_id]);
            $message = 'Product removed from cart!';
        }
    } elseif ($action == 'remove') {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        $message = 'Product removed from cart!';
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
    .cart-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }

    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th {
        background: linear-gradient(90deg,#2E6F40,#419759);
        color: white; padding: 1rem 1.2rem; text-align: left; font-size: .84rem; letter-spacing: .04em;
    }
    .cart-table td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: .9rem; vertical-align: middle; }
    .cart-table tr:last-child td { border-bottom: none; }
    .cart-table tbody tr:hover { background: rgba(85,193,115,0.05); }

    .quantity-input {
        width: 64px; padding: .45rem .5rem;
        border: 1px solid rgba(46,111,64,0.25); border-radius: 8px;
        text-align: center; background: rgba(255,255,255,0.85); font-size: .9rem;
    }
    .btn-update { padding: .45rem .85rem; background: linear-gradient(135deg,#55C173,#2E6F40); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; box-shadow:0 3px 10px rgba(46,111,64,0.3); transition: all .2s; }
    .btn-update:hover { transform: translateY(-1px); }
    .btn-remove { padding: .45rem .85rem; background: linear-gradient(135deg,#ef4444,#b91c1c); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; box-shadow:0 3px 10px rgba(185,28,28,0.25); transition: all .2s; }
    .btn-remove:hover { transform: translateY(-1px); }

    .cart-summary { padding: 1.5rem 1.8rem; margin-top: 1rem; }
    .btn-checkout {
        display: inline-block; padding: .9rem 2rem;
        background: linear-gradient(135deg,#55C173,#2E6F40);
        color: white; text-decoration: none; border-radius: 12px;
        font-weight: 700; font-size: .95rem;
        box-shadow: 0 5px 16px rgba(46,111,64,0.4);
        transition: all .25s;
    }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(46,111,64,0.5); }

    .section-heading { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
    .section-heading span { display: inline-block; width: 4px; height: 18px; background: linear-gradient(180deg,#55C173,#2E6F40); border-radius: 2px; }
</style>

<div class="container cart-container">
    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">🛒 Shopping Cart</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="glass-card" style="padding:3rem;text-align:center;">
            <p style="font-size:1.2rem;color:#2E6F40;margin-bottom:1rem;">Your cart is empty</p>
            <a href="../index.php" class="btn-checkout">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="glass-table">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                        <td><strong>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <button type="submit" class="btn-remove">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
        <div class="glass-card cart-summary">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <h3 style="color:#2E6F40;font-weight:800;font-size:1.2rem;">Total Amount: ₹<?php echo number_format($total, 2); ?></h3>
                </div>
                <div>
                    <a href="../index.php" style="margin-right:1rem;color:#6AEC8E;text-decoration:none;font-weight:600;">← Continue Shopping</a>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
