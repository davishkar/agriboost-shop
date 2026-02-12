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
        max-width: 800px;
        margin: 3rem auto;
        background: white;
        padding: 3rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .export-icon {
        font-size: 5rem;
        margin-bottom: 1rem;
    }
    
    .btn-export {
        display: inline-block;
        padding: 1rem 2rem;
        background-color: #55C173;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1.1rem;
        transition: background-color 0.3s;
    }
    
    .btn-export:hover {
        background-color: #419759;
    }
    
    .info-box {
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        border-radius: 5px;
        padding: 1rem;
        margin: 2rem 0;
        text-align: left;
    }
    
    .info-box ul {
        margin: 0.5rem 0 0 1.5rem;
    }
    
    .info-box li {
        margin: 0.25rem 0;
    }
</style>

<div class="container">
    <div class="export-container">
        <div class="export-icon">📄</div>
        <h2 style="color: #2E6F40; margin-bottom: 1rem;">Export Orders Report</h2>
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
