<?php
/**
 * Homepage - Product Listing
 * AGRIBOOST SHOP
 */

// Include database connection
require_once 'config/db.php';

// Start session
session_start();

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-agri-dark to-agri-primary text-white py-20 px-4 text-center overflow-hidden" style="background-image: url('images/bg-image1.jpeg'); background-size: cover; background-position: center; background-blend-mode: overlay;">
    <!-- Overlay for better text readability -->
    <div class="absolute inset-0 bg-gradient-to-r from-agri-dark/80 to-agri-primary/70"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        <h1 class="text-4xl md:text-6xl font-bold mb-6 drop-shadow-lg">🌾 Welcome to AgriBoost Shop</h1>
        <p class="text-xl md:text-3xl mb-4 opacity-95 drop-shadow-md">Your One-Stop Shop for Fertilizers & Agricultural Essentials</p>
        <p class="text-lg md:text-xl mt-6 drop-shadow-md">Quality Products for Better Harvest</p>
        
        <!-- CTA Buttons -->
        <div class="mt-8 flex gap-4 justify-center flex-wrap">
            <a href="#products" class="bg-white text-agri-dark px-8 py-3 rounded-lg font-bold hover:bg-agri-light hover:text-white transition-all duration-300 shadow-lg">
                Shop Now
            </a>
            <a href="about.php" class="bg-agri-primary/30 backdrop-blur-sm border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-agri-dark transition-all duration-300 shadow-lg">
                Learn More
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8" id="products">
    <h2 class="text-3xl font-bold text-agri-dark mb-8 text-center">Our Products</h2>
    
    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
            <div class="overflow-hidden transition-all duration-300" style="background:rgba(255,255,255,0.72);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,0.45);box-shadow:0 6px 24px rgba(0,0,0,0.12);border-radius:16px;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,0.18)'" onmouseout="this.style.transform='';this.style.boxShadow='0 6px 24px rgba(0,0,0,0.12)'">
                    <!-- Product Image -->
                    <div class="bg-agri-lightest h-48 flex items-center justify-center overflow-hidden">
                        <?php if ($product['image']): ?>
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="text-6xl">🌱</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-agri-dark mb-2 line-clamp-2">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                        </p>
                        
                        <div class="text-2xl font-bold text-agri-primary mb-2">
                            ₹<?php echo number_format($product['price'], 2); ?>
                        </div>
                        
                        <div class="text-sm mb-4">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="text-agri-primary font-medium">✓ In Stock (<?php echo $product['stock']; ?> available)</span>
                            <?php else: ?>
                                <span class="text-red-500 font-medium">✗ Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="user/cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" 
                                        class="w-full text-white font-bold py-3 px-4 rounded-lg transition-all duration-300" style="background:linear-gradient(135deg,#55C173,#2E6F40);box-shadow:0 4px 12px rgba(46,111,64,0.35);" <?php echo ($product['stock'] <= 0) ? 'disabled style="background:linear-gradient(135deg,#9ca3af,#6b7280);box-shadow:none;cursor:not-allowed;"' : ''; ?>>
                                    Add to Cart
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="user/login.php">
                                <button class="w-full text-white font-bold py-3 px-4 rounded-lg transition-all duration-300" style="background:linear-gradient(135deg,#55C173,#2E6F40);box-shadow:0 4px 12px rgba(46,111,64,0.35);">
                                    Login to Purchase
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12 text-gray-500">
                <p class="text-xl">No products available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
