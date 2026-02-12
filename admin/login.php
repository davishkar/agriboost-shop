<?php
/**
 * Admin Login Page
 * AGRIBOOST SHOP - Admin Authentication
 */

// Include database connection
require_once '../config/db.php';

// Start session
session_start();

// Redirect if already logged in as admin
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required!';
    } else {
        // Check admin credentials from admins table
        $stmt = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Password is correct, create admin session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                
                // Redirect to admin dashboard
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid email or password!';
            }
        } else {
            $error = 'Invalid email or password!';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AgriBoost Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'agri-primary': '#55C173',
                        'agri-medium': '#3FA05B',
                        'agri-dark': '#2E6F40',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-xl shadow-2xl p-8">
            <!-- Admin Badge -->
            <div class="text-center mb-6">
                <div class="inline-block bg-agri-dark text-white px-4 py-2 rounded-full text-sm font-bold mb-4">
                    🔐 ADMIN ACCESS
                </div>
                <h2 class="text-3xl font-bold text-agri-dark">Admin Login</h2>
                <p class="text-gray-600 mt-2">AgriBoost Shop Management</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"
                           placeholder="admin@agriboost.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" 
                        class="w-full bg-agri-dark hover:bg-agri-medium text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105">
                    Login to Admin Panel
                </button>
            </form>
            
            <hr class="my-6 border-gray-300">
            
            <div class="bg-gray-50 p-4 rounded-lg text-sm">
                <p class="font-bold mb-2 text-gray-700">Demo Admin Credentials:</p>
                <p class="text-gray-600"><strong>Email:</strong> admin@agriboost.com</p>
                <p class="text-gray-600"><strong>Password:</strong> admin123</p>
            </div>
            
            <div class="mt-6 text-center">
                <a href="../index.php" class="text-agri-dark hover:text-agri-primary font-semibold">
                    ← Back to Store
                </a>
            </div>
        </div>
    </div>
</body>
</html>
