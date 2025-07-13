<?php
session_start();

// Simple authentication (in real app, use proper password hashing)
$admin_username = 'admin';
$admin_password = 'admin123';

if ($_POST['username'] ?? '' === $admin_username && $_POST['password'] ?? '' === $admin_password) {
    $_SESSION['admin_logged_in'] = true;
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = 'වැරදි පරිශීලක නාමය හෝ මුර පදය';
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | සිංහල ප්‍රවෘත්ති</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-header">
                <h1>Admin Panel</h1>
                <h2>සිංහල ප්‍රවෘත්ති</h2>
                <p>කළමනාකරණ පැනලයට ප්‍රවේශ වන්න</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="admin-login-form">
                <div class="form-group">
                    <label for="username">පරිශීලක නාමය:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">මුර පදය:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="admin-login-btn">ප්‍රවේශ වන්න</button>
            </form>

            <div class="admin-info">
                <h3>ආදර්ශ ඇතුළත් කිරීම:</h3>
                <p><strong>පරිශීලක නාමය:</strong> admin</p>
                <p><strong>මුර පදය:</strong> admin123</p>
            </div>

            <div class="back-to-site">
                <a href="index.html">← වෙබ් අඩවියට ආපසු යන්න</a>
            </div>
        </div>
    </div>

    <style>
        .admin-login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a72 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .admin-login-box {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .admin-header h1 {
            color: #2c5aa0;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .admin-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .admin-header p {
            color: #666;
            font-size: 1rem;
        }

        .error-message {
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .admin-login-form {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2c5aa0;
        }

        .admin-login-btn {
            width: 100%;
            padding: 1rem;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .admin-login-btn:hover {
            background: #1e3a72;
        }

        .admin-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }

        .admin-info h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .admin-info p {
            margin: 0.5rem 0;
            color: #666;
        }

        .back-to-site {
            text-align: center;
        }

        .back-to-site a {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-to-site a:hover {
            color: #1e3a72;
        }

        @media (max-width: 480px) {
            .admin-login-container {
                padding: 1rem;
            }

            .admin-login-box {
                padding: 2rem;
            }

            .admin-header h1 {
                font-size: 1.7rem;
            }

            .admin-header h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</body>
</html>
