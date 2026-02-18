<?php
/**
 * Manage Products Page
 * AGRIBOOST SHOP - Admin Product Management
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

$message = '';
$error = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $name = clean_input($_POST['name']);
        $description = clean_input($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category = clean_input($_POST['category']);
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (in_array($file_type, $allowed_types) && $file_size <= 5000000) { // 5MB max
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = '../images/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                } else {
                    $error = 'Failed to upload image!';
                }
            } else {
                $error = 'Invalid image file! Only JPG, PNG, GIF, WEBP allowed (max 5MB)';
            }
        }
        
        if (empty($name) || empty($price) || empty($stock)) {
            $error = 'Name, price, and stock are required!';
        } elseif (!$error) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category, $image);
            
            if ($stmt->execute()) {
                $message = 'Product added successfully!';
            } else {
                $error = 'Failed to add product!';
            }
            $stmt->close();
        }
    } elseif ($action == 'edit') {
        $id = (int)$_POST['id'];
        $name = clean_input($_POST['name']);
        $description = clean_input($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category = clean_input($_POST['category']);
        
        // Get current image
        $current_image = $_POST['current_image'];
        $image = $current_image;
        
        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (in_array($file_type, $allowed_types) && $file_size <= 5000000) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = '../images/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($current_image && file_exists('../images/' . $current_image)) {
                        unlink('../images/' . $current_image);
                    }
                    $image = $new_filename;
                }
            }
        }
        
        if (empty($name) || empty($price) || $stock < 0) {
            $error = 'Name, price, and stock are required!';
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category, $image, $id);
            
            if ($stmt->execute()) {
                $message = 'Product updated successfully!';
            } else {
                $error = 'Failed to update product!';
            }
            $stmt->close();
        }
    } elseif ($action == 'delete') {
        $id = (int)$_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product!';
        }
        $stmt->close();
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Include header
include '../includes/header.php';
?>

<style>
    .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }

    .add-product-form { padding: 2rem; margin-bottom: 2rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: .4rem; font-weight: 600; color: #1C4A29; font-size: .88rem; }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%; padding: .7rem .9rem;
        border: 1px solid rgba(46,111,64,0.25);
        border-radius: 8px;
        background: rgba(255,255,255,0.8);
        font-size: .9rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #55C173;
        box-shadow: 0 0 0 3px rgba(85,193,115,0.2);
    }

    .products-table { width: 100%; border-collapse: collapse; }
    .products-table th {
        background: linear-gradient(90deg,#2E6F40,#419759);
        color: white; padding: 1rem 1.2rem; text-align: left; font-size: .84rem; letter-spacing: .04em;
    }
    .products-table td { padding: .85rem 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: .88rem; vertical-align: middle; }
    .products-table tr:last-child td { border-bottom: none; }
    .products-table tbody tr:hover { background: rgba(85,193,115,0.06); }

    .btn-edit   { padding: .45rem .9rem; background: linear-gradient(135deg,#55C173,#2E6F40); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; margin-right:.4rem; transition: all .2s; box-shadow: 0 3px 10px rgba(46,111,64,0.3); }
    .btn-edit:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(46,111,64,0.4); }
    .btn-delete { padding: .45rem .9rem; background: linear-gradient(135deg,#ef4444,#b91c1c); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:.82rem; font-weight:600; transition: all .2s; box-shadow: 0 3px 10px rgba(185,28,28,0.25); }
    .btn-delete:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(185,28,28,0.35); }
    .btn-add    { padding: .75rem 1.6rem; font-size:.92rem; }

    .section-heading { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
    .section-heading span { display: inline-block; width: 4px; height: 18px; background: linear-gradient(180deg,#55C173,#2E6F40); border-radius: 2px; }
</style>

<div class="container admin-container">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" style="margin-bottom: 1.2rem;">
        <ol class="glass-breadcrumb" style="display:flex;align-items:center;gap:.4rem;list-style:none;padding:.6rem 1rem;border-radius:10px;font-size:.85rem;flex-wrap:wrap;">
            <li><a href="/agriboost-shop/index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">🏠 Home</a></li>
            <li style="color:rgba(255,255,255,0.5);">›</li>
            <li><a href="index.php" style="color:#6AEC8E;text-decoration:none;font-weight:500;">📊 Dashboard</a></li>
            <li style="color:rgba(255,255,255,0.5);">›</li>
            <li><span style="color:#fff;font-weight:600;">📦 Products</span></li>
        </ol>
    </nav>

    <h2 style="color:#fff;font-weight:800;font-size:1.6rem;margin-bottom:1.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.3);">📦 Manage Products</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Add Product Form -->
    <p class="section-heading"><span></span> Add New Product</p>
    <div class="glass-card add-product-form">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" placeholder="e.g., Fertilizers, Organic">
                </div>
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Product description..."></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
                <small style="color: #666;">Allowed: JPG, PNG, GIF, WEBP (Max 5MB)</small>
            </div>
            <button type="submit" class="gradient-btn btn-add">Add Product</button>
        </form>
    </div>
    
    <!-- Products List -->
    <p class="section-heading"><span></span> All Products</p>
    <div class="glass-table">
    <table class="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-center;">🌱</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="btn-edit">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">No products available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);z-index:1000;">
    <div class="glass-card" style="max-width:600px;margin:3rem auto;padding:2rem;border-radius:16px;">
        <h3 style="color: #2E6F40; margin-bottom: 1rem;">Edit Product</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="current_image" id="edit_current_image">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="edit_category">
            </div>
            <div class="form-group">
                <label>Price (₹) *</label>
                <input type="number" name="price" id="edit_price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity *</label>
                <input type="number" name="stock" id="edit_stock" min="0" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <div id="current_image_preview" style="margin-bottom: 0.5rem;"></div>
                <input type="file" name="image" accept="image/*">
                <small style="color: #666;">Leave empty to keep current image. Allowed: JPG, PNG, GIF, WEBP (Max 5MB)</small>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeEditModal()" style="flex: 1; padding: 0.75rem; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 1; padding: 0.75rem; background-color: #55C173; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_category').value = product.category || '';
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock').value = product.stock;
    document.getElementById('edit_description').value = product.description || '';
    document.getElementById('edit_current_image').value = product.image || '';
    
    // Show current image preview
    const preview = document.getElementById('current_image_preview');
    if (product.image) {
        preview.innerHTML = '<img src="../images/' + product.image + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;"><br><small>Current image</small>';
    } else {
        preview.innerHTML = '<small style="color: #666;">No image uploaded</small>';
    }
    
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<?php include '../includes/footer.php'; ?>
