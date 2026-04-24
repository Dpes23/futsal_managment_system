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

$error = '';
$success = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match.';
    } else {
        // Get current user's password
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($currentPassword, $user['password'])) {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect.';
            }
        } catch(PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

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
    $error = 'Failed to load user data.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Futsal Recommendation System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .profile-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
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
        
        .profile-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }
        
        .user-info h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .user-info p {
            color: #666;
            font-size: 14px;
        }
        
        .header-right {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .profile-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .section-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 13px;
        }
        
        .password-requirements h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            color: #666;
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .password-requirements li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper input {
            padding-right: 50px;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #666;
            padding: 0;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: #667eea;
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
                    <a href="/admin_bookings" class="nav-btn">⬅️ Back to Admin Panel</a>
                <?php else: ?>
                    <div class="nav-buttons-left">
                        <a href="/futsals" class="nav-btn">🔍 Search Futsals</a>
                        <a href="/my_bookings" class="nav-btn">📅 My Bookings</a>
                    </div>
                    <a href="?logout=1" class="nav-btn logout-btn">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-section">
        <h2 class="section-title">🔒 Change Password</h2>
        
        <div class="password-requirements">
            <h4>Password Requirements:</h4>
            <ul>
                <li>Minimum 6 characters long</li>
                <li>Must match current password</li>
                <li>New password and confirmation must match</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password *</label>
                <div class="password-wrapper">
                    <input type="password" id="current_password" name="current_password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('current_password')">👁️</button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password *</label>
                <div class="password-wrapper">
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password')">👁️</button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password *</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">👁️</button>
                </div>
            </div>
            
            <button type="submit" name="change_password" value="1" class="submit-btn">
                ✅ Change Password
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        button.textContent = '🙈';
    } else {
        field.type = 'password';
        button.textContent = '👁️';
    }
}
</script>

</body>
</html>
