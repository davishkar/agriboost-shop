<?php
/**
 * About Page
 * AGRIBOOST SHOP - About Us
 */

// Include database connection
require_once 'config/db.php';

// Start session
session_start();

// Include header
include 'includes/header.php';
?>

<style>
    .about-hero {
        background: linear-gradient(135deg, #2E6F40 0%, #55C173 100%);
        padding: 4rem 1rem;
        text-align: center;
        color: white;
    }
    
    .about-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1rem;
    }
    
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(85, 193, 115, 0.3);
    }
    
    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .value-item {
        border-left: 4px solid #55C173;
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
        text-align: center;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #55C173 0%, #3FA05B 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #2E6F40 0%, #55C173 100%);
        color: white;
        padding: 3rem 1rem;
        text-align: center;
        border-radius: 12px;
        margin: 3rem 0;
    }
    
    .btn-primary {
        background: white;
        color: #2E6F40;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin: 0.5rem;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: #f0f0f0;
        transform: scale(1.05);
    }
    
    .btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin: 0.5rem;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: white;
        color: #2E6F40;
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">About AgriBoost Shop</h1>
    <p style="font-size: 1.5rem; opacity: 0.9;">Empowering Farmers with Quality Agricultural Solutions</p>
</div>

<div class="about-section">
    <!-- Mission & Vision -->
    <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 3rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <span style="font-size: 3rem;">🎯</span>
            <h2 style="color: #2E6F40; font-size: 2.5rem; margin: 1rem 0;">Our Mission</h2>
        </div>
        <p style="font-size: 1.2rem; line-height: 1.8; color: #555; text-align: center; max-width: 800px; margin: 0 auto;">
            At AgriBoost Shop, we are committed to empowering farmers and agricultural enthusiasts with high-quality fertilizers, 
            organic products, and essential agricultural supplies. Our mission is to boost agricultural productivity while 
            promoting sustainable farming practices that protect our environment for future generations.
        </p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">500+</div>
            <div style="font-size: 1.2rem;">Happy Farmers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">50+</div>
            <div style="font-size: 1.2rem;">Quality Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">100%</div>
            <div style="font-size: 1.2rem;">Satisfaction Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div style="font-size: 1.2rem;">Customer Support</div>
        </div>
    </div>

    <!-- What We Offer -->
    <div style="margin: 4rem 0;">
        <h2 style="color: #2E6F40; font-size: 2.5rem; text-align: center; margin-bottom: 3rem;">
            <span style="font-size: 2.5rem;">🌱</span> What We Offer
        </h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">🌾</div>
                <h3 style="color: #55C173; font-size: 1.5rem; margin-bottom: 1rem;">Premium Fertilizers</h3>
                <p style="color: #666; line-height: 1.6;">NPK, Urea, DAP, and specialized fertilizers for all crop types to maximize yield</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">♻️</div>
                <h3 style="color: #55C173; font-size: 1.5rem; margin-bottom: 1rem;">Organic Products</h3>
                <p style="color: #666; line-height: 1.6;">Compost, vermicompost, and natural soil conditioners for sustainable farming</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3 style="color: #55C173; font-size: 1.5rem; margin-bottom: 1rem;">Bio Pesticides</h3>
                <p style="color: #666; line-height: 1.6;">Eco-friendly pest control solutions safe for crops and environment</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💊</div>
                <h3 style="color: #55C173; font-size: 1.5rem; margin-bottom: 1rem;">Micronutrients</h3>
                <p style="color: #666; line-height: 1.6;">Essential minerals and nutrients for optimal plant health and growth</p>
            </div>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div style="background: linear-gradient(135deg, #55C173 0%, #3FA05B 100%); color: white; padding: 3rem; border-radius: 12px; margin: 4rem 0;">
        <h2 style="font-size: 2.5rem; text-align: center; margin-bottom: 3rem;">
            ⭐ Why Choose Us
        </h2>
        <div class="feature-grid">
            <div style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Quality Assured</h3>
                <p style="opacity: 0.9;">All products tested and certified for agricultural use</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🚚</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Fast Delivery</h3>
                <p style="opacity: 0.9;">Quick and reliable shipping to your doorstep</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Best Prices</h3>
                <p style="opacity: 0.9;">Competitive pricing with bulk discounts available</p>
            </div>
        </div>
    </div>

    <!-- Our Values -->
    <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #2E6F40; font-size: 2.5rem; margin-bottom: 2rem;">
            <span style="font-size: 2.5rem;">💚</span> Our Values
        </h2>
        <div class="value-item">
            <h3 style="color: #2E6F40; font-size: 1.5rem; margin-bottom: 0.5rem;">Sustainability</h3>
            <p style="color: #666; line-height: 1.6;">We promote eco-friendly farming practices and sustainable agriculture</p>
        </div>
        <div class="value-item">
            <h3 style="color: #2E6F40; font-size: 1.5rem; margin-bottom: 0.5rem;">Quality</h3>
            <p style="color: #666; line-height: 1.6;">We never compromise on product quality and authenticity</p>
        </div>
        <div class="value-item">
            <h3 style="color: #2E6F40; font-size: 1.5rem; margin-bottom: 0.5rem;">Customer First</h3>
            <p style="color: #666; line-height: 1.6;">Your satisfaction and success are our top priorities</p>
        </div>
        <div class="value-item">
            <h3 style="color: #2E6F40; font-size: 1.5rem; margin-bottom: 0.5rem;">Innovation</h3>
            <p style="color: #666; line-height: 1.6;">We continuously seek better solutions for modern agriculture</p>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <h3 style="font-size: 2rem; margin-bottom: 1.5rem;">Ready to Boost Your Harvest?</h3>
        <div>
            <a href="index.php" class="btn-primary">Shop Now</a>
            <a href="contact.php" class="btn-secondary">Contact Us</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
