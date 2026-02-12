<?php
/**
 * User Profile Page
 * AGRIBOOST SHOP - View and Update Profile
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
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate required fields
    if (empty($name)) {
        $error = 'Name is required!';
    } else {
        // If changing password
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error = 'Current password is required to change password!';
            } elseif (strlen($new_password) < 6) {
                $error = 'New password must be at least 6 characters!';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New passwords do not match!';
            } else {
                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
                
                if (!password_verify($current_password, $user['password'])) {
                    $error = 'Current password is incorrect!';
                } else {
                    // Update with new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $name, $phone, $address, $hashed_password, $user_id);
                    
                    if ($stmt->execute()) {
                        $success = 'Profile and password updated successfully!';
                        $_SESSION['user_name'] = $name;
                    } else {
                        $error = 'Update failed! Please try again.';
                    }
                    $stmt->close();
                }
            }
        } else {
            // Update without password change
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $phone, $address, $user_id);
            
            if ($stmt->execute()) {
                $success = 'Profile updated successfully!';
                $_SESSION['user_name'] = $name;
            } else {
                $error = 'Update failed! Please try again.';
            }
            $stmt->close();
        }
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, phone, address, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Include header
include '../includes/header.php';
?>

<style>
    .profile-container {
        max-width: 700px;
        margin: 2rem auto;
    }
    
    .profile-section {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="container profile-container">
    <h2 style="color: #2E6F40; margin-bottom: 1.5rem;">👤 My Profile</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="profile-section">
        <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <p style="margin: 0.25rem 0;"><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            <p style="margin: 0.25rem 0;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <h3 style="color: #2E6F40; margin-bottom: 1rem;">Update Profile</h3>
        
        <form method="POST" action="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Full Name *</label>
                <input type="text" name="name" required 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                       value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Phone</label>
                <input type="tel" name="phone" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                       value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Address</label>
                <textarea name="address" rows="3" 
                          style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #ddd;">
            
            <h4 style="color: #2E6F40; margin-bottom: 1rem;">Change Password (Optional)</h4>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Current Password</label>
                <input type="password" name="current_password" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">New Password</label>
                <input type="password" name="new_password" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666;">Minimum 6 characters</small>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Confirm New Password</label>
                <input type="password" name="confirm_password" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <button type="submit" 
                    style="width: 100%; padding: 0.75rem; background-color: #55C173; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                Update Profile
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
