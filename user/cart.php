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
    .cart-container {
        max-width: 900px;
        margin: 2rem auto;
    }
    
    .cart-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .cart-table th {
        background-color: #2E6F40;
        color: white;
        padding: 1rem;
        text-align: left;
    }
    
    .cart-table td {
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }
    
    .cart-table tr:last-child td {
        border-bottom: none;
    }
    
    .quantity-input {
        width: 60px;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
    }
    
    .btn-update {
        padding: 0.5rem 1rem;
        background-color: #55C173;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
    }
    
    .btn-remove {
        padding: 0.5rem 1rem;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
    }
    
    .cart-summary {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .btn-checkout {
        display: inline-block;
        padding: 1rem 2rem;
        background-color: #55C173;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .btn-checkout:hover {
        background-color: #419759;
    }
</style>

<div class="container cart-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">🛒 Shopping Cart</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div style="background: white; padding: 3rem; text-align: center; border-radius: 10px;">
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 1rem;">Your cart is empty</p>
            <a href="../index.php" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #55C173; color: white; text-decoration: none; border-radius: 5px;">
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
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
        
        <div class="cart-summary">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #2E6F40;">Total Amount: ₹<?php echo number_format($total, 2); ?></h3>
                </div>
                <div>
                    <a href="../index.php" style="margin-right: 1rem; color: #2E6F40; text-decoration: none;">← Continue Shopping</a>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
