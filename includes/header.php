<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriBoost Shop - Fertilizers & Agricultural Essentials</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'agri-lightest': '#CFFCD8',
                        'agri-light': '#6AEC8E',
                        'agri-primary': '#55C173',
                        'agri-medium': '#419759',
                        'agri-dark': '#2E6F40',
                        'agri-darker': '#1C4A29',
                        'agri-darkest': '#0C2713',
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-agri-darker text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <a href="/agriboost-shop/index.php" class="flex items-center space-x-2 text-xl font-bold hover:text-agri-light transition-colors duration-300">
                    <span class="text-2xl">🌾</span>
                    <span>AgriBoost Shop</span>
                </a>
                
                <!-- Desktop Menu -->
                <ul class="hidden md:flex space-x-8 items-center">
                    <li>
                        <a href="/agriboost-shop/index.php" 
                           class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'index.php') ? 'text-agri-light font-semibold' : ''; ?>">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="/agriboost-shop/about.php" 
                           class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'about.php') ? 'text-agri-light font-semibold' : ''; ?>">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="/agriboost-shop/contact.php" 
                           class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'contact.php') ? 'text-agri-light font-semibold' : ''; ?>">
                            Contact
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <!-- Admin Menu -->
                        <li>
                            <a href="/agriboost-shop/admin/index.php" 
                               class="hover:text-agri-light transition-colors duration-300 <?php echo (strpos($current_page, 'admin') !== false) ? 'text-agri-light font-semibold' : ''; ?>">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <span class="text-agri-light text-sm">Admin: <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                        </li>
                        <li>
                            <a href="/agriboost-shop/admin/logout.php" 
                               class="bg-agri-primary hover:bg-agri-medium px-4 py-2 rounded-lg transition-all duration-300">
                                Logout
                            </a>
                        </li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <!-- User Menu -->
                        <li>
                            <a href="/agriboost-shop/user/cart.php" 
                               class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'cart.php') ? 'text-agri-light font-semibold' : ''; ?>">
                                🛒 Cart
                            </a>
                        </li>
                        <li>
                            <a href="/agriboost-shop/user/orders.php" 
                               class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'orders.php') ? 'text-agri-light font-semibold' : ''; ?>">
                                My Orders
                            </a>
                        </li>
                        <li>
                            <a href="/agriboost-shop/user/profile.php" 
                               class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'profile.php') ? 'text-agri-light font-semibold' : ''; ?>">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="/agriboost-shop/user/logout.php" 
                               class="bg-agri-primary hover:bg-agri-medium px-4 py-2 rounded-lg transition-all duration-300">
                                Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <li>
                            <a href="/agriboost-shop/user/login.php" 
                               class="hover:text-agri-light transition-colors duration-300 <?php echo ($current_page == 'login.php') ? 'text-agri-light font-semibold' : ''; ?>">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/agriboost-shop/user/register.php" 
                               class="bg-agri-primary hover:bg-agri-medium px-4 py-2 rounded-lg transition-all duration-300 <?php echo ($current_page == 'register.php') ? 'bg-agri-medium' : ''; ?>">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-agri-darkest">
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="/agriboost-shop/index.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors <?php echo ($current_page == 'index.php') ? 'bg-agri-dark text-agri-light' : ''; ?>">Home</a>
                <a href="/agriboost-shop/about.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors <?php echo ($current_page == 'about.php') ? 'bg-agri-dark text-agri-light' : ''; ?>">About</a>
                <a href="/agriboost-shop/contact.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors <?php echo ($current_page == 'contact.php') ? 'bg-agri-dark text-agri-light' : ''; ?>">Contact</a>
                
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="/agriboost-shop/admin/index.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Dashboard</a>
                    <a href="/agriboost-shop/admin/logout.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Logout</a>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <a href="/agriboost-shop/user/cart.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">🛒 Cart</a>
                    <a href="/agriboost-shop/user/orders.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">My Orders</a>
                    <a href="/agriboost-shop/user/profile.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Profile</a>
                    <a href="/agriboost-shop/user/logout.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Logout</a>
                <?php else: ?>
                    <a href="/agriboost-shop/user/login.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Login</a>
                    <a href="/agriboost-shop/user/register.php" class="block px-3 py-2 rounded-lg hover:bg-agri-dark transition-colors">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
