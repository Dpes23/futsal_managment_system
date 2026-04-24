<?php
session_start();

require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

$error = '';
$success = '';

// Add password_reset_token column if it doesn't exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires DATETIME DEFAULT NULL");
} catch (PDOException $e) {
    // Column might already exist
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $resetToken = $_POST['reset_token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($resetToken) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match.';
    } else {
        // Check if reset token is valid
        try {
            $stmt = $pdo->prepare("SELECT id, password_reset_expires FROM users WHERE password_reset_token = ?");
            $stmt->execute([$resetToken]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Check if token has expired
                if (strtotime($user['password_reset_expires']) < time()) {
                    $error = 'Reset token has expired. Please request a new one.';
                } else {
                    // Update password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
                    $stmt->execute([$hashedPassword, $user['id']]);
                    $success = 'Password reset successfully! You can now login with your new password.';
                }
            } else {
                $error = 'Invalid reset token. Please check and try again.';
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
    <title>Reset Password - Futsal Recommendation System</title>
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
        
        .reset-password-card {
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
        
        .password-requirements {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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
    <div class="reset-password-card">
        <div class="card-header">
            <h1>🔑 Reset Password</h1>
            <p>Enter your reset code and new password</p>
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
        
        <div class="password-requirements">
            <h4>Password Requirements:</h4>
            <ul>
                <li>Minimum 6 characters long</li>
                <li>New password and confirmation must match</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="reset_token">Reset Code *</label>
                <input type="text" id="reset_token" name="reset_token" required placeholder="Enter the reset code from your email">
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
            
            <button type="submit" name="reset_password" value="1" class="submit-btn">
                ✅ Reset Password
            </button>
        </form>
        
        <div class="back-link">
            <a href="/login">← Back to Login</a>
        </div>
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
