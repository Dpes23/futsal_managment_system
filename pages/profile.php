<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /login');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

// Get user details
try {
    $stmt = $pdo->prepare("SELECT id, username, email, full_name, mobile, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: /login');
        exit();
    }
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Futsal Recommendation System</title>
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
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-header {
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
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-info p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nav-buttons-left {
            display: flex;
            gap: 10px;
        }
        
        .profile-avatar {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
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
        
        .settings-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .settings-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            font-weight: 500;
            cursor: pointer;
        }
        
        .settings-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 10px;
            z-index: 1;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background 0.3s;
            font-size: 14px;
        }
        
        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-content a.logout {
            color: #dc3545;
            border-top: 1px solid #f0f0f0;
        }
        
        .dropdown-content a.logout:hover {
            background-color: #fee;
        }
        
        .show {
            display: block;
        }
        
        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .section-title {
            color: #1e3c72;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .info-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-header">
        <div class="header-content">
            <div class="header-left">
                <div class="profile-avatar">👤</div>
                <div class="user-info">
                    <h1><?= htmlspecialchars($user['full_name']) ?></h1>
                    <p>@<?= htmlspecialchars($user['username']) ?></p>
                </div>
            </div>
            <div class="header-right">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="/admin_bookings" class="nav-btn">← Back to Admin Panel</a>
                <?php else: ?>
                    <a href="/futsals" class="nav-btn">← Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-section">
        <h2 class="section-title">Personal Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= htmlspecialchars($user['full_name']) ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= htmlspecialchars($user['username']) ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">Mobile Number</div>
                    <div class="info-value"><?= !empty($user['mobile']) ? htmlspecialchars($user['mobile']) : 'Not provided' ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">Member Since</div>
                    <div class="info-value"><?= date('F d, Y', strtotime($user['created_at'])) ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-content">
                    <div class="info-label">User ID</div>
                    <div class="info-value">#<?= str_pad($user['id'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profile-section">
        <h2 class="section-title">Account Statistics</h2>
        <div class="stats-grid">
            <?php
            // Get user booking statistics
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $totalBookings = $stmt->fetch()['total'];
                
                $stmt = $pdo->prepare("SELECT COUNT(*) as confirmed FROM bookings WHERE user_id = ? AND status = 'confirmed'");
                $stmt->execute([$_SESSION['user_id']]);
                $confirmedBookings = $stmt->fetch()['confirmed'];
                
                $stmt = $pdo->prepare("SELECT COUNT(*) as cancelled FROM bookings WHERE user_id = ? AND status = 'cancelled'");
                $stmt->execute([$_SESSION['user_id']]);
                $cancelledBookings = $stmt->fetch()['cancelled'];
                
                $stmt = $pdo->prepare("SELECT SUM(total_price) as total_spent FROM bookings WHERE user_id = ? AND status = 'confirmed'");
                $stmt->execute([$_SESSION['user_id']]);
                $totalSpent = $stmt->fetch()['total_spent'] ?? 0;
            } catch(PDOException $e) {
                $totalBookings = $confirmedBookings = $cancelledBookings = 0;
                $totalSpent = 0;
            }
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?= $totalBookings ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= $confirmedBookings ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= $cancelledBookings ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">Rs. <?= number_format($totalSpent) ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById("settingsDropdown").classList.toggle("show");
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.settings-btn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>

</body>
</html>
