<?php
/**
 * Contact Page
 * AGRIBOOST SHOP - Contact Us
 */

// Include database connection
require_once 'config/db.php';

// Start session
session_start();

$success = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $subject = clean_input($_POST['subject']);
    $message = clean_input($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Name, email, and message are required!';
    } else {
        // In a real application, you would save this to a database or send an email
        $success = 'Thank you for contacting us! We will get back to you soon.';
    }
}

// Include header
include 'includes/header.php';
?>

<style>
    .contact-hero {
        background: linear-gradient(135deg, #2E6F40 0%, #55C173 100%);
        padding: 4rem 1rem;
        text-align: center;
        color: white;
    }
    
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1rem;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #2E6F40;
        margin-bottom: 0.5rem;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #55C173;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #55C173 0%, #3FA05B 100%);
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: transform 0.3s;
    }
    
    .btn-submit:hover {
        transform: scale(1.02);
    }
    
    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .info-item {
        display: flex;
        align-items: start;
        margin-bottom: 1.5rem;
    }
    
    .info-icon {
        font-size: 2rem;
        margin-right: 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .quick-links {
        background: linear-gradient(135deg, #55C173 0%, #3FA05B 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
    }
    
    .quick-links a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 0.5rem 0;
        transition: transform 0.3s;
    }
    
    .quick-links a:hover {
        transform: translateX(5px);
    }
    
    .faq-item {
        margin-bottom: 1rem;
    }
    
    .faq-question {
        font-weight: 600;
        color: #55C173;
        margin-bottom: 0.3rem;
    }
    
    .faq-answer {
        color: #666;
        line-height: 1.6;
    }
</style>

<!-- Hero Section -->
<div class="contact-hero">
    <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">Contact Us</h1>
    <p style="font-size: 1.5rem; opacity: 0.9;">We'd Love to Hear From You!</p>
</div>

<div class="contact-container">
    <div class="contact-grid">
        <!-- Contact Form -->
        <div class="info-card">
            <h2 style="color: #2E6F40; font-size: 2rem; margin-bottom: 1.5rem;">Send us a Message</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" required placeholder="John Doe"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required placeholder="john@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+91 98765 43210"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Product Inquiry"
                           value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" required rows="5" placeholder="Tell us how we can help you..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>

        <!-- Contact Information -->
        <div>
            <!-- Contact Details -->
            <div class="info-card" style="margin-bottom: 2rem;">
                <h2 style="color: #2E6F40; font-size: 2rem; margin-bottom: 1.5rem;">Get in Touch</h2>
                
                <div class="info-item">
                    <span class="info-icon">📍</span>
                    <div>
                        <h3 style="color: #2E6F40; font-weight: 600; margin-bottom: 0.3rem;">Address</h3>
                        <p style="color: #666; line-height: 1.6;">123 Agricultural Plaza<br>Green Valley District<br>Maharashtra, India - 411001</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <span class="info-icon">📞</span>
                    <div>
                        <h3 style="color: #2E6F40; font-weight: 600; margin-bottom: 0.3rem;">Phone</h3>
                        <p style="color: #666;">+91 98765 43210<br>+91 87654 32109</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <span class="info-icon">📧</span>
                    <div>
                        <h3 style="color: #2E6F40; font-weight: 600; margin-bottom: 0.3rem;">Email</h3>
                        <p style="color: #666;">info@agriboost.com<br>support@agriboost.com</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <span class="info-icon">🕒</span>
                    <div>
                        <h3 style="color: #2E6F40; font-weight: 600; margin-bottom: 0.3rem;">Business Hours</h3>
                        <p style="color: #666;">Monday - Saturday: 9:00 AM - 6:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="quick-links" style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Quick Links</h2>
                <a href="index.php">🌾 Browse Products</a>
                <a href="about.php">ℹ️ About Us</a>
                <a href="user/login.php">👤 My Account</a>
                <a href="user/orders.php">📦 Track Order</a>
            </div>

            <!-- FAQ -->
            <div class="info-card">
                <h2 style="color: #2E6F40; font-size: 1.5rem; margin-bottom: 1rem;">Frequently Asked Questions</h2>
                <div class="faq-item">
                    <p class="faq-question">Do you offer bulk discounts?</p>
                    <p class="faq-answer">Yes! Contact us for special pricing on bulk orders.</p>
                </div>
                <div class="faq-item">
                    <p class="faq-question">What are your delivery areas?</p>
                    <p class="faq-answer">We deliver pan-India with express shipping options.</p>
                </div>
                <div class="faq-item">
                    <p class="faq-question">Do you provide product guidance?</p>
                    <p class="faq-answer">Yes, our experts are available to help you choose the right products.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
