<?php
/**
 * Payment Page
 * AGRIBOOST SHOP - Payment Method Selection (Demo)
 */

require_once '../config/db.php';
session_start();

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Must have a pending order
if (empty($_SESSION['pending_order'])) {
    header('Location: cart.php');
    exit();
}

$user_id      = $_SESSION['user_id'];
$pending      = $_SESSION['pending_order'];
$total        = $pending['total'];
$cart         = $pending['cart_snapshot'];
$shipping_addr = $pending['shipping_address'];

$error = '';

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = clean_input($_POST['payment_method'] ?? '');
    $allowed = ['upi', 'card', 'netbanking', 'cod'];

    if (!in_array($payment_method, $allowed)) {
        $error = 'Please select a valid payment method.';
    } else {
        // Place the order in DB
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
            $stmt->bind_param("ids", $user_id, $total, $shipping_addr);
            $stmt->execute();
            $order_id = $conn->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $product_id => $item) {
                $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
                $stmt->execute();

                $upd = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $upd->bind_param("ii", $item['quantity'], $product_id);
                $upd->execute();
                $upd->close();
            }
            $stmt->close();

            $conn->commit();

            // Clear cart & pending order; store success info
            $_SESSION['cart'] = [];
            unset($_SESSION['pending_order']);
            $_SESSION['last_order'] = [
                'order_id'       => $order_id,
                'total'          => $total,
                'payment_method' => $payment_method,
                'shipping_addr'  => $shipping_addr,
                'items'          => $cart,
            ];

            header('Location: payment_success.php');
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Order could not be placed. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<!-- ===== Inline styles ===== -->
<style>
    .pay-wrap { max-width: 860px; margin: 2.5rem auto; padding: 0 1rem; }

    /* Progress bar */
    .progress-bar { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 2.5rem; }
    .step { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .step-circle {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem;
    }
    .step-circle.done  { background: #55C173; color: #fff; }
    .step-circle.active{ background: #2E6F40; color: #fff; box-shadow: 0 0 0 4px #cffcd8; }
    .step-circle.idle  { background: #e5e7eb; color: #9ca3af; }
    .step-label { font-size: .7rem; color: #6b7280; font-weight: 500; }
    .step-line { flex: 1; height: 3px; background: #e5e7eb; max-width: 80px; }
    .step-line.done { background: #55C173; }

    /* Cards */
    .card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.08); padding: 1.8rem; margin-bottom: 1.5rem; }
    .card h3 { color: #2E6F40; font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; }

    /* Payment method tiles */
    .method-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; }
    .method-tile { position: relative; }
    .method-tile input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .method-tile label {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: .6rem; padding: 1.4rem 1rem; border: 2px solid #e5e7eb;
        border-radius: 12px; cursor: pointer; transition: all .2s;
        font-size: .85rem; font-weight: 600; color: #374151;
        background: #fafafa;
    }
    .method-tile label .icon { font-size: 2rem; }
    .method-tile input:checked + label {
        border-color: #55C173; background: #f0fdf4; color: #2E6F40;
        box-shadow: 0 0 0 3px #cffcd8;
    }
    .method-tile label:hover { border-color: #6AEC8E; background: #f0fdf4; }

    /* UPI sub-form */
    .sub-form { margin-top: 1rem; padding: 1rem; background: #f9fafb; border-radius: 10px; border: 1px solid #e5e7eb; }
    .sub-form input { width: 100%; padding: .65rem .9rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: .9rem; margin-top: .4rem; }
    .sub-form label { font-size: .82rem; font-weight: 600; color: #374151; }

    /* Card sub-form */
    .card-row { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; margin-top: .8rem; }

    /* Order summary table */
    .summary-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .summary-table td { padding: .5rem .3rem; border-bottom: 1px solid #f3f4f6; }
    .summary-table tr:last-child td { border: none; font-weight: 700; font-size: 1rem; color: #2E6F40; }

    /* Pay button */
    .pay-btn {
        width: 100%; padding: 1rem; background: linear-gradient(135deg, #55C173, #2E6F40);
        color: #fff; border: none; border-radius: 10px; font-size: 1.05rem;
        font-weight: 700; cursor: pointer; transition: opacity .2s; margin-top: 1rem;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
    }
    .pay-btn:hover { opacity: .9; }

    /* Demo badge */
    .demo-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #fef3c7; color: #92400e; font-size: .75rem; font-weight: 600;
        padding: .3rem .8rem; border-radius: 20px; border: 1px solid #fde68a;
        margin-bottom: 1rem;
    }

    /* Alert */
    .alert-err { background: #fee2e2; color: #991b1b; padding: .9rem 1.2rem; border-radius: 10px; margin-bottom: 1rem; font-size: .9rem; }
</style>

<div class="pay-wrap">

    <!-- Progress Steps -->
    <div class="progress-bar">
        <div class="step">
            <div class="step-circle done">✓</div>
            <span class="step-label">Cart</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-circle done">✓</div>
            <span class="step-label">Details</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-circle active">3</div>
            <span class="step-label">Payment</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-circle idle">4</div>
            <span class="step-label">Confirm</span>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert-err">⚠️ <?php echo $error; ?></div>
    <?php endif; ?>

    <span class="demo-badge">🧪 Demo Mode — No real payment is processed</span>

    <form method="POST" action="" id="payForm">
        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">

            <!-- LEFT: Payment methods -->
            <div>
                <div class="card">
                    <h3>💳 Select Payment Method</h3>
                    <div class="method-grid">

                        <!-- UPI -->
                        <div class="method-tile">
                            <input type="radio" name="payment_method" id="pm_upi" value="upi">
                            <label for="pm_upi">
                                <span class="icon">📱</span>
                                UPI
                            </label>
                        </div>

                        <!-- Card -->
                        <div class="method-tile">
                            <input type="radio" name="payment_method" id="pm_card" value="card">
                            <label for="pm_card">
                                <span class="icon">💳</span>
                                Debit / Credit Card
                            </label>
                        </div>

                        <!-- Net Banking -->
                        <div class="method-tile">
                            <input type="radio" name="payment_method" id="pm_nb" value="netbanking">
                            <label for="pm_nb">
                                <span class="icon">🏦</span>
                                Net Banking
                            </label>
                        </div>

                        <!-- COD -->
                        <div class="method-tile">
                            <input type="radio" name="payment_method" id="pm_cod" value="cod" checked>
                            <label for="pm_cod">
                                <span class="icon">💵</span>
                                Cash on Delivery
                            </label>
                        </div>

                    </div>

                    <!-- UPI sub-form -->
                    <div class="sub-form" id="upi_form" style="display:none;">
                        <label>UPI ID</label>
                        <input type="text" placeholder="yourname@upi" id="upi_id_field">
                        <p style="font-size:.75rem;color:#6b7280;margin-top:.4rem;">e.g. 9876543210@paytm, name@gpay</p>
                    </div>

                    <!-- Card sub-form -->
                    <div class="sub-form" id="card_form" style="display:none;">
                        <label>Card Number</label>
                        <input type="text" placeholder="1234 5678 9012 3456" maxlength="19" id="card_num">
                        <div class="card-row">
                            <div>
                                <label>Expiry (MM/YY)</label>
                                <input type="text" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div>
                                <label>CVV</label>
                                <input type="text" placeholder="•••" maxlength="3">
                            </div>
                        </div>
                        <label style="margin-top:.8rem;display:block;">Name on Card</label>
                        <input type="text" placeholder="Full Name">
                    </div>

                    <!-- Net Banking sub-form -->
                    <div class="sub-form" id="nb_form" style="display:none;">
                        <label>Select Your Bank</label>
                        <select style="width:100%;padding:.65rem .9rem;border:1px solid #d1d5db;border-radius:8px;margin-top:.4rem;font-size:.9rem;">
                            <option value="">-- Choose Bank --</option>
                            <option>State Bank of India</option>
                            <option>HDFC Bank</option>
                            <option>ICICI Bank</option>
                            <option>Axis Bank</option>
                            <option>Kotak Mahindra Bank</option>
                            <option>Punjab National Bank</option>
                            <option>Bank of Baroda</option>
                            <option>Canara Bank</option>
                        </select>
                    </div>

                    <!-- COD info -->
                    <div class="sub-form" id="cod_form">
                        <p style="font-size:.85rem;color:#374151;">
                            💵 <strong>Cash on Delivery</strong> — Pay when your order arrives at your doorstep.<br>
                            <span style="color:#6b7280;font-size:.78rem;">Available for all pin codes. No extra charges.</span>
                        </p>
                    </div>
                </div>

                <!-- Shipping address display -->
                <div class="card">
                    <h3>📦 Shipping To</h3>
                    <p style="font-size:.9rem;color:#374151;line-height:1.6;"><?php echo nl2br(htmlspecialchars($shipping_addr)); ?></p>
                    <a href="checkout.php" style="font-size:.8rem;color:#55C173;text-decoration:underline;">✏️ Change address</a>
                </div>
            </div>

            <!-- RIGHT: Order summary -->
            <div>
                <div class="card">
                    <h3>🧾 Order Summary</h3>
                    <table class="summary-table">
                        <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?> <span style="color:#9ca3af;">×<?php echo $item['quantity']; ?></span></td>
                            <td style="text-align:right;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>Shipping</td>
                            <td style="text-align:right;color:#55C173;font-weight:600;">FREE</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td style="text-align:right;">₹<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </table>

                    <button type="submit" class="pay-btn" id="payBtn">
                        <span id="payBtnIcon">🔒</span>
                        <span id="payBtnText">Pay ₹<?php echo number_format($total, 2); ?></span>
                    </button>

                    <a href="checkout.php" style="display:block;text-align:center;margin-top:.8rem;font-size:.82rem;color:#6b7280;text-decoration:underline;">← Back to Checkout</a>

                    <p style="text-align:center;font-size:.72rem;color:#9ca3af;margin-top:.8rem;">
                        🔒 Secured by 256-bit SSL encryption
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    const methods = ['upi','card','netbanking','cod'];
    const subForms = { upi:'upi_form', card:'card_form', netbanking:'nb_form', cod:'cod_form' };
    const payBtnText = document.getElementById('payBtnText');
    const payBtnIcon = document.getElementById('payBtnIcon');
    const totalStr   = '₹<?php echo number_format($total, 2); ?>';

    function showSubForm(val) {
        methods.forEach(m => {
            const el = document.getElementById(subForms[m]);
            if (el) el.style.display = (m === val) ? 'block' : 'none';
        });
        if (val === 'cod') {
            payBtnText.textContent = 'Place Order (COD) ' + totalStr;
            payBtnIcon.textContent = '📦';
        } else {
            payBtnText.textContent = 'Pay ' + totalStr;
            payBtnIcon.textContent = '🔒';
        }
    }

    // Init
    showSubForm('cod');

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', () => showSubForm(radio.value));
    });

    // Card number formatting
    const cardNum = document.getElementById('card_num');
    if (cardNum) {
        cardNum.addEventListener('input', function() {
            let v = this.value.replace(/\D/g,'').substring(0,16);
            this.value = v.replace(/(.{4})/g,'$1 ').trim();
        });
    }

    // Loading state on submit
    document.getElementById('payForm').addEventListener('submit', function() {
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin" style="width:20px;height:20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Processing…';
    });
</script>

<?php include '../includes/footer.php'; ?>
