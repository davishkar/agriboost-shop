<?php
/**
 * User Login Page
 * AGRIBOOST SHOP - User Authentication
 */

// Include database connection
require_once '../config/db.php';

// Start session
session_start();

// Redirect if already logged in as user
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
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
        // Check user credentials from users table
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create user session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to home page
                header('Location: ../index.php');
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
    <title>User Login - AgriBoost Shop</title>
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
                <h2 class="text-3xl font-bold" style="background:linear-gradient(135deg,#2E6F40,#55C173);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Welcome Back!</h2>
                <p class="text-gray-500 mt-2">Login to your AgriBoost account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 rounded-lg transition-all" style="border:1px solid rgba(46,111,64,0.25);background:rgba(255,255,255,0.8);font-size:.95rem;" onfocus="this.style.boxShadow='0 0 0 3px rgba(85,193,115,0.25)';this.style.borderColor='#55C173'" onblur="this.style.boxShadow='';this.style.borderColor='rgba(46,111,64,0.25)'"
                           placeholder="your@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg transition-all" style="border:1px solid rgba(46,111,64,0.25);background:rgba(255,255,255,0.8);font-size:.95rem;" onfocus="this.style.boxShadow='0 0 0 3px rgba(85,193,115,0.25)';this.style.borderColor='#55C173'" onblur="this.style.boxShadow='';this.style.borderColor='rgba(46,111,64,0.25)'"
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" 
                        class="w-full text-white font-bold py-3 px-4 rounded-lg transition-all duration-300" style="background:linear-gradient(135deg,#55C173,#2E6F40);box-shadow:0 4px 14px rgba(46,111,64,0.4);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(46,111,64,0.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(46,111,64,0.4)'">
                    Login
                </button>
            </form>
            
            <p class="text-center mt-6 text-gray-600">
                Don't have an account? <a href="register.php" class="text-agri-dark font-bold hover:text-agri-primary">Register here</a>
            </p>
            
            <hr class="my-6 border-gray-300">
            <p class="text-center mt-6 text-gray-600">
                <a href="../admin/login.php" class="text-agri-dark font-bold hover:text-agri-primary">🪪 Admin Login</a>
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
