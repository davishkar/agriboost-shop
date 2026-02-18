<?php
/**
 * Payment Success Page
 * AGRIBOOST SHOP - Order Confirmation
 */

require_once '../config/db.php';
session_start();

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Must have a completed order in session
if (empty($_SESSION['last_order'])) {
    header('Location: orders.php');
    exit();
}

$order        = $_SESSION['last_order'];
$order_id     = $order['order_id'];
$total        = $order['total'];
$payment_method = $order['payment_method'];
$shipping_addr  = $order['shipping_addr'];
$items          = $order['items'];

// Payment method display info
$pm_info = [
    'upi'        => ['label' => 'UPI',              'icon' => '📱', 'color' => '#7c3aed'],
    'card'       => ['label' => 'Debit/Credit Card', 'icon' => '💳', 'color' => '#1d4ed8'],
    'netbanking' => ['label' => 'Net Banking',       'icon' => '🏦', 'color' => '#0369a1'],
    'cod'        => ['label' => 'Cash on Delivery',  'icon' => '💵', 'color' => '#15803d'],
];
$pm = $pm_info[$payment_method] ?? ['label' => ucfirst($payment_method), 'icon' => '💰', 'color' => '#374151'];

// Estimated delivery (5-7 days from now)
$est_delivery = date('d M Y', strtotime('+5 days')) . ' – ' . date('d M Y', strtotime('+7 days'));

// Clear last_order from session after reading (prevent refresh re-use)
// We keep it for this page render; clear on next visit
$_SESSION['last_order_viewed'] = true;

include '../includes/header.php';
?>

<style>
    .success-wrap { max-width: 760px; margin: 2.5rem auto; padding: 0 1rem; }

    /* Animated checkmark */
    .check-circle {
        width: 90px; height: 90px; border-radius: 50%;
        background: linear-gradient(135deg, #55C173, #2E6F40);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.2rem;
        animation: pop .5s cubic-bezier(.36,.07,.19,.97) both;
        box-shadow: 0 8px 30px rgba(85,193,115,.4);
    }
    .check-circle svg { width: 48px; height: 48px; stroke: #fff; stroke-width: 3; fill: none; }
    @keyframes pop {
        0%   { transform: scale(0); opacity: 0; }
        70%  { transform: scale(1.15); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Hero card */
    .hero-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 6px 30px rgba(0,0,0,.1);
        padding: 2.5rem 2rem; text-align: center; margin-bottom: 1.5rem;
    }
    .hero-card h1 { color: #2E6F40; font-size: 1.7rem; font-weight: 800; margin-bottom: .4rem; }
    .hero-card p  { color: #6b7280; font-size: .95rem; }

    .order-id-badge {
        display: inline-block; background: #f0fdf4; color: #2E6F40;
        border: 2px dashed #55C173; border-radius: 10px;
        padding: .5rem 1.5rem; font-size: 1.1rem; font-weight: 700;
        margin: 1rem 0; letter-spacing: .5px;
    }

    /* Info card */
    .info-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.07); padding: 1.6rem; margin-bottom: 1.2rem; }
    .info-card h3 { color: #2E6F40; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: .7rem; }

    /* Payment badge */
    .pm-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .45rem 1rem; border-radius: 20px; font-weight: 600; font-size: .88rem;
        color: #fff;
    }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .items-table th { text-align: left; padding: .5rem .4rem; color: #9ca3af; font-weight: 600; font-size: .78rem; border-bottom: 2px solid #f3f4f6; }
    .items-table td { padding: .65rem .4rem; border-bottom: 1px solid #f9fafb; color: #374151; }
    .items-table tr:last-child td { border: none; }
    .items-table .total-row td { font-weight: 700; color: #2E6F40; font-size: 1rem; padding-top: .9rem; border-top: 2px solid #f3f4f6; }

    /* Timeline */
    .timeline { list-style: none; padding: 0; margin: 0; }
    .timeline li { display: flex; align-items: flex-start; gap: .9rem; padding: .6rem 0; }
    .tl-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .8rem; flex-shrink: 0; margin-top: 2px; }
    .tl-dot.done   { background: #d1fae5; color: #065f46; }
    .tl-dot.active { background: #fef3c7; color: #92400e; }
    .tl-dot.idle   { background: #f3f4f6; color: #9ca3af; }
    .tl-info strong { display: block; font-size: .88rem; font-weight: 600; color: #374151; }
    .tl-info span   { font-size: .78rem; color: #9ca3af; }

    /* Action buttons */
    .action-row { display: flex; gap: 1rem; margin-top: 1.5rem; }
    .btn-primary {
        flex: 1; padding: .85rem; background: linear-gradient(135deg, #55C173, #2E6F40);
        color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: .95rem;
        text-align: center; text-decoration: none; cursor: pointer; transition: opacity .2s;
    }
    .btn-primary:hover { opacity: .9; }
    .btn-outline {
        flex: 1; padding: .85rem; background: #fff; color: #2E6F40;
        border: 2px solid #55C173; border-radius: 10px; font-weight: 700; font-size: .95rem;
        text-align: center; text-decoration: none; cursor: pointer; transition: all .2s;
    }
    .btn-outline:hover { background: #f0fdf4; }

    @media (max-width: 600px) {
        .action-row { flex-direction: column; }
    }
</style>

<div class="success-wrap">

    <!-- Hero -->
    <div class="hero-card">
        <div class="check-circle">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <h1>Order Placed Successfully! 🎉</h1>
        <p>Thank you for shopping with AgriBoost Shop. Your order has been confirmed.</p>
        <div class="order-id-badge">Order #<?php echo $order_id; ?></div>
        <br>
        <span class="pm-badge" style="background:<?php echo $pm['color']; ?>;">
            <?php echo $pm['icon']; ?> Paid via <?php echo $pm['label']; ?>
        </span>
        <p style="margin-top:1rem;font-size:.82rem;color:#9ca3af;">
            📧 A confirmation has been noted. Estimated delivery: <strong style="color:#2E6F40;"><?php echo $est_delivery; ?></strong>
        </p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;">

        <!-- Order Items -->
        <div class="info-card" style="grid-column:1/-1;">
            <h3>🛒 Items Ordered</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td style="text-align:center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align:right;">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align:right;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3">Total Amount</td>
                        <td style="text-align:right;">₹<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Shipping Address -->
        <div class="info-card">
            <h3>📦 Shipping Address</h3>
            <p style="font-size:.88rem;color:#374151;line-height:1.7;"><?php echo nl2br(htmlspecialchars($shipping_addr)); ?></p>
        </div>

        <!-- Order Status Timeline -->
        <div class="info-card">
            <h3>📋 Order Status</h3>
            <ul class="timeline">
                <li>
                    <div class="tl-dot done">✓</div>
                    <div class="tl-info">
                        <strong>Order Confirmed</strong>
                        <span><?php echo date('d M Y, h:i A'); ?></span>
                    </div>
                </li>
                <li>
                    <div class="tl-dot active">⏳</div>
                    <div class="tl-info">
                        <strong>Processing</strong>
                        <span>Your order is being prepared</span>
                    </div>
                </li>
                <li>
                    <div class="tl-dot idle">📦</div>
                    <div class="tl-info">
                        <strong>Packed & Shipped</strong>
                        <span>Awaiting dispatch</span>
                    </div>
                </li>
                <li>
                    <div class="tl-dot idle">🚚</div>
                    <div class="tl-info">
                        <strong>Out for Delivery</strong>
                        <span>Est. <?php echo $est_delivery; ?></span>
                    </div>
                </li>
                <li>
                    <div class="tl-dot idle">✅</div>
                    <div class="tl-info">
                        <strong>Delivered</strong>
                        <span>—</span>
                    </div>
                </li>
            </ul>
        </div>

    </div>

    <!-- Action Buttons -->
    <div class="action-row">
        <a href="orders.php" class="btn-primary">📋 View My Orders</a>
        <a href="/agriboost-shop/index.php" class="btn-outline">🛍️ Continue Shopping</a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
