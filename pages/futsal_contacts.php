<?php
session_start();

$futsals = include __DIR__ . '/../handlers/futsals_data.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Contacts - Futsal Recommendation System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1541252260730-0412e8e2108e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #1e3c72;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
        }
        
        .futsals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .futsal-card {
            background: white;
            border-radius: 15px;
            padding: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border: 2px solid #f0f0f0;
            overflow: hidden;
        }
        
        .futsal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            border-color: #667eea;
        }
        
        .futsal-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .futsal-content {
            padding: 25px;
        }
        
        .futsal-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .futsal-details {
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .detail-icon {
            font-size: 18px;
            margin-right: 12px;
            width: 25px;
        }
        
        .detail-text {
            flex: 1;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .detail-value {
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }
        
        .phone-section {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: center;
        }
        
        .phone-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .contact-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .call-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .call-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .back-btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 30px;
        }
        
        .back-btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .futsals-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📞 Futsal Contact Directory</h1>
        <p>Find contact information for all futsal courts in Kathmandu Valley</p>
    </div>
    
    <div class="futsals-grid">
        <?php foreach ($futsals as $futsal): ?>
            <div class="futsal-card">
                <img src="https://picsum.photos/seed/<?= urlencode($futsal['name']) ?>/400/200.jpg" 
                     alt="<?= htmlspecialchars($futsal['name']) ?>" 
                     class="futsal-image">
                
                <div class="futsal-content">
                    <div class="futsal-name"><?= htmlspecialchars($futsal['name']) ?></div>
                    
                    <div class="futsal-details">
                    <div class="detail-item">
                        <div class="detail-icon">📍</div>
                        <div class="detail-text">
                            <div class="detail-label">Location</div>
                            <div class="detail-value"><?= htmlspecialchars($futsal['address']) ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">💰</div>
                        <div class="detail-text">
                            <div class="detail-label">Price per Hour</div>
                            <div class="detail-value">Rs. <?= number_format($futsal['price']) ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">⭐</div>
                        <div class="detail-text">
                            <div class="detail-label">Rating</div>
                            <div class="detail-value"><?= $futsal['rating'] ?> / 5.0 ★</div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="phone-section">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="phone-number">📞 <?= htmlspecialchars($futsal['phone']) ?></div>
                        <div class="contact-buttons">
                            <a href="tel:<?= htmlspecialchars($futsal['phone']) ?>" class="call-btn">
                                📞 Call Now
                            </a>
                            <a href="tel:<?= htmlspecialchars($futsal['phone']) ?>" class="call-btn">
                                💬 WhatsApp
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="phone-number">📞 Login to view phone number</div>
                        <div class="contact-buttons">
                            <a href="/login" class="call-btn">
                                📞 Call Now
                            </a>
                            <a href="/login" class="call-btn">
                                💬 WhatsApp
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="/index" class="back-btn">← Back to Home</a>
    </div>
</div>

</body>
</html>
