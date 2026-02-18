<?php
/**
 * User Registration Page
 * AGRIBOOST SHOP - User Registration
 */

// Include database connection
require_once '../config/db.php';

// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Name, email, and password are required!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user into users table
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $address);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
                // Optionally auto-login the user
                // $_SESSION['user_id'] = $conn->insert_id;
                // $_SESSION['user_name'] = $name;
                // $_SESSION['user_email'] = $email;
                // header('Location: ../index.php');
                // exit();
            } else {
                $error = 'Registration failed! Please try again.';
            }
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
    <title>User Registration - AgriBoost Shop</title>
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
<body style="background: linear-gradient(135deg, #0C2713 0%, #1C4A29 40%, #2E6F40 70%, #1C4A29 100%); min-height: 100vh;">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full p-8" style="background:rgba(255,255,255,0.72);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,0.45);box-shadow:0 20px 60px rgba(0,0,0,0.25),inset 0 1px 0 rgba(255,255,255,0.7);border-radius:20px;">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold" style="background:linear-gradient(135deg,#2E6F40,#55C173);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Create Account</h2>
                <p class="text-gray-500 mt-2">Join AgriBoost Shop today</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo $success; ?>
                    <a href="login.php" class="font-bold underline">Click here to login</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password * (min 6 characters)</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                    <input type="password" name="confirm_password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-agri-primary focus:border-transparent transition-all">
                </div>
                
                <button type="submit" 
                        class="w-full text-white font-bold py-3 px-4 rounded-lg transition-all duration-300" style="background:linear-gradient(135deg,#55C173,#2E6F40);box-shadow:0 4px 14px rgba(46,111,64,0.4);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(46,111,64,0.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(46,111,64,0.4)'">
                    Register
                </button>
            </form>
            
            <p class="text-center mt-6 text-gray-600">
                Already have an account? <a href="login.php" class="text-agri-dark font-bold hover:text-agri-primary">Login here</a>
            </p>
            
            <div class="mt-6 text-center">
                <a href="../index.php" class="text-agri-dark hover:text-agri-primary font-semibold">
                    ← Back to Store
                </a>
            </div>
        </div>
    </div>
</body>
</html>
