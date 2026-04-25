<?php
session_start();

require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

$error = '';
$success = '';
$resetToken = '';

// Add password_reset_token column if it doesn't exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires DATETIME DEFAULT NULL");
} catch (PDOException $e) {
    // Column might already exist
}

// Handle forgot password request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email exists
        try {
            $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate 5-digit reset code
                $resetToken = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $stmt = $pdo->prepare("UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?");
                $stmt->execute([$resetToken, $resetExpires, $user['id']]);
                
                // Display code on screen (for development/testing)
                $success = 'Password reset code generated. Your reset code is: <strong>' . $resetToken . '</strong><br><br>This code will expire in 1 hour.<br><br><a href="/reset_password" style="color: #667eea; font-weight: 600;">Click here to reset your password</a>';
            } else {
                $error = 'Your email is not registered.';
            }
        } catch(PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Futsal Recommendation System</title>
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
        
        .container {
            max-width: 450px;
            width: 100%;
        }
        
        .forgot-password-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .card-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .card-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
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
            margin-bottom: 20px;
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
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="forgot-password-card">
        <div class="card-header">
            <h1>🔐 Forgot Password</h1>
            <p>Enter your email address to reset your password</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <button type="submit" name="forgot_password" value="1" class="submit-btn">
                📧 Send Reset Code
            </button>
        </form>
        
        <div class="back-link">
            <a href="/login">← Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>
