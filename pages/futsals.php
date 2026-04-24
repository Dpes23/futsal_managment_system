<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/recommendation_engine.php';
$futsals = include __DIR__ . '/../handlers/futsals_data.php';

// Get user location if available (for distance calculation)
$userLat = $_SESSION['user_lat'] ?? 27.7172; // Default to Kathmandu
$userLon = $_SESSION['user_lon'] ?? 85.3240;

// Get recommended futsals using the recommendation engine
$recommendedFutsals = getFutsalRecommendations('general', [], 6);

// Get all futsal names that are recommended
$recommendedNames = array_column($recommendedFutsals, 'name');

// Separate futsals into recommended and non-recommended
$nonRecommendedFutsals = [];
foreach ($futsals as $futsal) {
    if (!in_array($futsal['name'], $recommendedNames)) {
        $nonRecommendedFutsals[] = $futsal;
    }
}

// Sort non-recommended futsals by rating (highest first)
usort($nonRecommendedFutsals, function($a, $b) {
    return $b['rating'] <=> $a['rating'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Courts - Book Now</title>
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
        
        .page-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px 30px;
            margin: -30px -30px 30px -30px;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header-left p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .header-right {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            color: white;
            transform: translateY(-2px);
        }
        
        .futsals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .futsal-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border: 1px solid #f0f0f0;
            border-left: 4px solid #1e3c72;
        }
        
        .futsal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .futsal-card h3 {
            margin: 0 0 15px 0;
            color: #1e3c72;
            font-size: 20px;
            font-weight: 600;
        }
        
        .recommendation-badge {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .futsal-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .detail-item.clickable {
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .detail-item.clickable:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }
        
        .book-btn {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .rating {
            color: #000;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1>⚽ Futsal Courts</h1>
                <p>Book your preferred futsal court</p>
            </div>
            <div class="header-right">
                <a href="/profile" class="nav-btn">👤 My Profile</a>
                <a href="/my_bookings" class="nav-btn">📅 My Bookings</a>
            </div>
        </div>
    </div>
    
    <div class="futsals-grid">
        <?php 
        // Display recommended futsals first
        foreach ($recommendedFutsals as $futsal): 
            $distance = round(calculateDistance($userLat, $userLon, $futsal['lat'], $futsal['lng']), 1);
        ?>
            <div class="futsal-card">
                <div class="recommendation-badge">🏆 Recommended</div>
                <h3><?= htmlspecialchars($futsal['name']) ?></h3>
                
                <div class="futsal-details">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($futsal['address']) ?>" target="_blank" class="detail-item clickable" style="text-decoration: none;">
                        <div class="detail-label">📍 Location</div>
                        <div class="detail-value" style="color: #1e3c72; font-weight: 500;">
                            <?= htmlspecialchars($futsal['address']) ?> 🗺️
                        </div>
                    </a>
                    
                    <div class="detail-item">
                        <div class="detail-label">📞 Mobile</div>
                        <div class="detail-value"><?= htmlspecialchars($futsal['phone']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">💰 Price</div>
                        <div class="detail-value">Rs. <?= number_format($futsal['price']) ?>/hr</div>
                    </div>
                    
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $futsal['lat'] ?>,<?= $futsal['lng'] ?>" target="_blank" class="detail-item clickable" style="text-decoration: none;">
                        <div class="detail-label">📏 Distance</div>
                        <div class="detail-value" style="color: #1e3c72; font-weight: 500;">
                            <?= $distance ?> km 🗺️
                        </div>
                    </a>
                    
                    <div class="detail-item">
                        <div class="detail-label">⭐ Rating</div>
                        <div class="detail-value"><span style="color: #ffc107;">⭐</span> <?= $futsal['rating'] ?></div>
                    </div>
                </div>
                
                <a href="/booking?futsal=<?= urlencode($futsal['name']) ?>" class="book-btn">
                    📖 Book Now
                </a>
            </div>
        <?php endforeach; ?>
        
        <?php 
        // Display non-recommended futsals sorted by rating
        foreach ($nonRecommendedFutsals as $futsal): 
            $distance = round(calculateDistance($userLat, $userLon, $futsal['lat'], $futsal['lng']), 1);
        ?>
            <div class="futsal-card">
                <h3><?= htmlspecialchars($futsal['name']) ?></h3>
                
                <div class="futsal-details">
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($futsal['address']) ?>" target="_blank" class="detail-item clickable" style="text-decoration: none;">
                        <div class="detail-label">📍 Location</div>
                        <div class="detail-value" style="color: #1e3c72; font-weight: 500;">
                            <?= htmlspecialchars($futsal['address']) ?> 🗺️
                        </div>
                    </a>
                    
                    <div class="detail-item">
                        <div class="detail-label">📞 Mobile</div>
                        <div class="detail-value"><?= htmlspecialchars($futsal['phone']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">💰 Price</div>
                        <div class="detail-value">Rs. <?= number_format($futsal['price']) ?>/hr</div>
                    </div>
                    
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $futsal['lat'] ?>,<?= $futsal['lng'] ?>" target="_blank" class="detail-item clickable" style="text-decoration: none;">
                        <div class="detail-label">📏 Distance</div>
                        <div class="detail-value" style="color: #1e3c72; font-weight: 500;">
                            <?= $distance ?> km 🗺️
                        </div>
                    </a>
                    
                    <div class="detail-item">
                        <div class="detail-label">⭐ Rating</div>
                        <div class="detail-value"><span style="color: #ffc107;">⭐</span> <?= $futsal['rating'] ?></div>
                    </div>
                </div>
                
                <a href="/booking?futsal=<?= urlencode($futsal['name']) ?>" class="book-btn">
                    📖 Book Now
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
