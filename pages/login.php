<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    require_once __DIR__ . '/../config/database.php';
    $pdo = getDatabaseConnection();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            
            if ($user['is_admin']) {
                header('Location: /admin_bookings');
            } else {
                header('Location: /futsals');
            }
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } catch(PDOException $e) {
        $error = 'Database error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Futsal Recommendation System</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            padding: 40px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
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
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #fcc;
        }
        
        .login-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .tab-container {
            display: flex;
            margin-bottom: 25px;
            background: #f0f0f0;
            border-radius: 10px;
            padding: 5px;
        }
        
        .tab-btn {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .tab-btn.active {
            background: white;
            color: #667eea;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .tab-btn:hover:not(.active) {
            background: #e0e0e0;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .login-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .login-links a:hover {
            color: #764ba2;
            text-decoration: none;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
        }
        
        .divider span {
            color: #999;
            font-size: 12px;
            white-space: nowrap;
            text-decoration: none;
        }
        
        .login-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        
        .login-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        
        .login-links a:hover {
            text-decoration: none;
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">⚽</div>
            <h1>Welcome Back</h1>
            <p>Login to access your futsal booking account</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="tab-container">
            <button type="button" class="tab-btn active" onclick="switchTab('user')">User Account</button>
            <button type="button" class="tab-btn" onclick="switchTab('admin')">Admin Account</button>
        </div>
        
        <form method="post" id="user-login-form">
            <div class="tab-content active" id="user-tab">
                <div class="form-group">
                    <label for="user-username">Username</label>
                    <input type="text" id="user-username" name="username" required placeholder="Enter your username" autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="user-password">Password</label>
                    <div class="password-container">
                        <input type="password" id="user-password" name="password" required placeholder="Enter your password" autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('user-password', this)">👁️</button>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Login as User</button>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="/forgot_password" style="color: #667eea; text-decoration: none; font-size: 14px;">Forgot Password?</a>
                </div>
            </div>
        </form>
        
        <form method="post" id="admin-login-form">
            <div class="tab-content" id="admin-tab">
                <div class="form-group">
                    <label for="admin-username">Admin Username</label>
                    <input type="text" id="admin-username" name="username" required placeholder="Enter admin username" autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="admin-password">Admin Password</label>
                    <div class="password-container">
                        <input type="password" id="admin-password" name="password" required placeholder="Enter admin password" autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('admin-password', this)">👁️</button>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Login as Admin</button>
            </div>
        </form>
        
        <script>
            function switchTab(tab) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Remove active class from all tab buttons
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Show selected tab content
                document.getElementById(tab + '-tab').classList.add('active');
                
                // Add active class to clicked button
                event.target.classList.add('active');
            }
            
            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                if (input.type === 'password') {
                    input.type = 'text';
                    button.textContent = '🙈';
                } else {
                    input.type = 'password';
                    button.textContent = '👁️';
                }
            }
        </script>
        
        <div class="login-links">
            <a href="/">← Back to Home</a>
            <a href="/register">Create Account →</a>
        </div>
    </div>
</body>
</html>
