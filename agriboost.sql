-- ====================================================
-- AGRIBOOST SHOP - Database Schema
-- Fertilizers & Agricultural Essentials Platform
-- ====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS agriboost_shop;
USE agriboost_shop;

-- ====================================================
-- Table: admins
-- Stores admin user information (separate from regular users)
-- ====================================================
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Table: users
-- Stores regular user/customer information
-- ====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Table: products
-- Stores product information
-- ====================================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Table: orders
-- Stores order information
-- ====================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Table: order_items
-- Stores individual items in each order
-- ====================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================
-- Insert Sample Admin Users
-- Password: admin123 (hashed using password_hash)
-- ====================================================
INSERT INTO admins (name, email, password, phone) VALUES
('Admin', 'admin@agriboost.com', '$2y$10$e0MYzXyjpJS7Pd0i5qC.r.uyG7.LWJB5L3qF5qJ5Z5z5Z5z5Z5z5O', '9876543210'),
('Super Admin', 'superadmin@agriboost.com', '$2y$10$e0MYzXyjpJS7Pd0i5qC.r.uyG7.LWJB5L3qF5qJ5Z5z5Z5z5Z5z5O', '9876543211');



-- ====================================================
-- Insert Sample Products
-- ====================================================
INSERT INTO products (name, description, price, stock, image, category) VALUES
('NPK Fertilizer 20-20-20', 'Balanced NPK fertilizer suitable for all crops. Promotes healthy growth and high yield.', 850.00, 150, 'image3.png', 'Fertilizers'),
('Urea Fertilizer', 'High nitrogen content fertilizer. Ideal for leafy vegetables and crops.', 650.00, 200, 'image4.png', 'Fertilizers'),
('Organic Compost 50kg', 'Premium quality organic compost enriched with nutrients. Perfect for organic farming.', 450.00, 100, 'image5.png', 'Organic'),
('Potash Fertilizer', 'Rich in potassium. Enhances fruit quality and disease resistance.', 720.00, 120, 'image6.png', 'Fertilizers'),
('Bio Pesticide Spray', 'Eco-friendly pesticide made from natural ingredients. Safe for crops and environment.', 380.00, 80, 'image7.png', 'Pesticides'),
('Micronutrient Mix', 'Essential micronutrients for healthy plant growth. Contains Zinc, Boron, Iron.', 520.00, 90, 'image8.png', 'Nutrients'),
('Vermicompost 25kg', 'Premium vermicompost rich in organic matter. Improves soil structure.', 350.00, 110, 'image9.png', 'Organic'),
('DAP Fertilizer', 'Di-ammonium Phosphate fertilizer. High phosphorus content for root development.', 1200.00, 140, 'image10.png', 'Fertilizers'),
('Neem Oil Spray', 'Natural neem oil based pesticide. Effective against pests and diseases.', 280.00, 70, 'image11.png', 'Pesticides'),
('Seaweed Extract', 'Organic seaweed extract. Boosts plant immunity and growth.', 680.00, 60, 'image12.png', 'Organic'),
('Calcium Nitrate', 'Water soluble calcium fertilizer. Prevents blossom end rot.', 590.00, 95, 'image13.png', 'Nutrients'),
('Humic Acid Granules', 'Improves soil fertility and nutrient absorption. Organic soil conditioner.', 420.00, 85, 'image14.png', 'Soil Conditioners');

-- ====================================================
-- Insert Sample Orders
-- ====================================================
INSERT INTO orders (user_id, total_amount, status, shipping_address, created_at) VALUES
(2, 1500.00, 'delivered', 'Village Road, Farm Area', '2026-01-15 10:30:00'),
(3, 830.00, 'shipped', 'Green Valley, Agricultural Zone', '2026-02-01 14:20:00'),
(2, 2150.00, 'processing', 'Village Road, Farm Area', '2026-02-10 09:15:00');

-- ====================================================
-- Insert Sample Order Items
-- ====================================================
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
-- Order 1 items
(1, 1, 1, 850.00),
(1, 2, 1, 650.00),
-- Order 2 items
(2, 5, 2, 380.00),
(2, 7, 1, 350.00),
-- Order 3 items
(3, 8, 1, 1200.00),
(3, 4, 1, 720.00),
(3, 9, 1, 280.00);

-- ====================================================
-- Login Credentials for Testing
-- ====================================================
-- ADMIN LOGIN (use admin/login.php):
--   Email: admin@agriboost.com
--   Password: admin123
--
-- USER LOGIN (use user/login.php):
--   Email: john@example.com
--   Password: admin123
-- ====================================================

