<?php
/**
 * Export Orders to CSV
 * AGRIBOOST SHOP - Admin Order Export
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

// If export is requested
if (isset($_GET['export'])) {
    // Fetch all orders with user details
    $query = "SELECT o.id, u.name as customer_name, u.email, u.phone, 
              o.total_amount, o.status, o.shipping_address, o.created_at
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              ORDER BY o.created_at DESC";
    
    $result = $conn->query($query);
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d_H-i-s') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, array('Order ID', 'Customer Name', 'Email', 'Phone', 'Total Amount', 'Status', 'Shipping Address', 'Order Date'));
    
    // Add data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['customer_name'],
            $row['email'],
            $row['phone'],
            $row['total_amount'],
            $row['status'],
            $row['shipping_address'],
            date('Y-m-d H:i:s', strtotime($row['created_at']))
        ));
    }
    
    fclose($output);
    exit();
}

// Include header
include '../includes/header.php';
?>

<style>
    .export-container {
        max-width: 700px; margin: 3rem auto;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .export-icon { font-size: 4.5rem; margin-bottom: 1rem; filter: drop-shadow(0 4px 12px rgba(85,193,115,0.4)); }
    .btn-export {
        display: inline-block; padding: 1rem 2.2rem;
        background: linear-gradient(135deg,#55C173,#2E6F40);
        color: white; text-decoration: none; border-radius: 12px;
        font-weight: 700; font-size: 1.05rem;
        box-shadow: 0 6px 20px rgba(46,111,64,0.4);
        transition: all .25s;
    }
    .btn-export:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(46,111,64,0.5); opacity: .92; }
    .info-box {
        background: rgba(209,236,241,0.55);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(190,229,235,0.6);
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        margin: 1.8rem 0;
        text-align: left;
    }
    .info-box ul { margin: .5rem 0 0 1.5rem; }
    .info-box li { margin: .25rem 0; color: #0c5460; font-size: .88rem; }
</style>

<div class="container" style="padding: 0 1rem;">
    <div class="glass-card export-container">
        <div class="export-icon">📄</div>
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" style="margin-bottom: 1.5rem; text-align:left;">
            <ol class="glass-breadcrumb" style="display:flex;align-items:center;gap:.4rem;list-style:none;padding:.6rem 1rem;border-radius:10px;font-size:.85rem;flex-wrap:wrap;">
                <li><a href="/agriboost-shop/index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">🏠 Home</a></li>
                <li style="color:rgba(255,255,255,0.5);">›</li>
                <li><a href="index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">📊 Dashboard</a></li>
                <li style="color:rgba(255,255,255,0.5);">›</li>
                <li><span style="color:#fff;font-weight:600;">📄 Export Orders</span></li>
            </ol>
        </nav>

        <h2 style="color:#2E6F40;font-weight:800;font-size:1.5rem;margin-bottom:.8rem;">Export Orders Report</h2>
        <p style="color: #666; margin-bottom: 2rem;">Download all orders data in CSV format for analysis and record keeping.</p>
        
        <div class="info-box">
            <strong style="color: #0c5460;">Export includes:</strong>
            <ul style="color: #0c5460;">
                <li>Order ID</li>
                <li>Customer Name, Email, and Phone</li>
                <li>Total Amount</li>
                <li>Order Status</li>
                <li>Shipping Address</li>
                <li>Order Date and Time</li>
            </ul>
        </div>
        
        <?php
        // Get total orders count
        $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
        ?>
        
        <p style="margin-bottom: 2rem;">
            <strong>Total Orders to Export:</strong> <?php echo $total_orders; ?>
        </p>
        
        <a href="?export=1" class="btn-export">📥 Download CSV Report</a>
        
        <p style="margin-top: 2rem;">
            <a href="index.php" style="color: #2E6F40;">← Back to Dashboard</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
