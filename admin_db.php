<?php
require_once 'includes/config.php';

$error = '';
$message = '';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('admin-dashboard_db.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'කරුණාකර පරිශීලක නාමය සහ මුර පදය ඇතුළත් කරන්න';
    } else {
        try {
            $db = new Database();
            $user = $db->verifyLogin($username, $password);

            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['last_login'] = time();

                // Log successful login
                log_security_event('User login', "User: $username, Role: {$user['role']}");

                // Redirect to dashboard
                redirect('admin-dashboard_db.php');
            } else {
                $error = 'වැරදි පරිශීලක නාමය හෝ මුර පදය';
                log_security_event('Failed login attempt', "Username: $username");
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'පද්ධතිමය දෝෂයක්. කරුණාකර නැවත උත්සාහ කරන්න.';
        }
    }
}

// Check database connection
$db_connected = check_database_connection();
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= SITE_TITLE ?></title>
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
                <h2><?= SITE_TITLE ?></h2>
                <p>කළමනාකරණ පැනලයට ප්‍රවේශ වන්න</p>
            </div>

            <?php if (!$db_connected): ?>
                <div class="error-message">
                    <strong>Database Connection Error!</strong><br>
                    Please run the database migration first: <a href="database/migrate.php" target="_blank">Setup Database</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="success-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" class="admin-login-form">
                <div class="form-group">
                    <label for="username">පරිශීලක නාමය:</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">මුර පදය:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <button type="submit" class="admin-login-btn" <?= !$db_connected ? 'disabled' : '' ?>>
                    ප්‍රවේශ වන්න
                </button>
            </form>

            <?php if ($db_connected): ?>
                <div class="admin-info">
                    <h3>ආදර්ශ ඇතුළත් කිරීම:</h3>
                    <div class="login-credentials">
                        <div class="credential-item">
                            <strong>Admin Account:</strong><br>
                            Username: <code>admin</code><br>
                            Password: <code>admin123</code>
                        </div>
                        <div class="credential-item">
                            <strong>Editor Account:</strong><br>
                            Username: <code>editor</code><br>
                            Password: <code>admin123</code>
                        </div>
                    </div>
                    <p class="security-note">
                        <strong>⚠️ Security:</strong> Change default passwords after first login
                    </p>
                </div>
            <?php else: ?>
                <div class="setup-info">
                    <h3>Database Setup Required</h3>
                    <p>Before logging in, you need to set up the database:</p>
                    <ol>
                        <li>Make sure MySQL is running</li>
                        <li>Update database credentials in <code>includes/config.php</code></li>
                        <li><a href="database/migrate.php" target="_blank" class="setup-link">Run Database Migration</a></li>
                    </ol>
                </div>
            <?php endif; ?>

            <div class="back-to-site">
                <a href="index_db.php">← වෙබ් අඩවියට ආපසු යන්න</a>
            </div>

            <div class="system-info">
                <details>
                    <summary>System Information</summary>
                    <ul>
                        <li><strong>Database:</strong> <?= $db_connected ? '✅ Connected' : '❌ Not Connected' ?></li>
                        <li><strong>PHP Version:</strong> <?= PHP_VERSION ?></li>
                        <li><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></li>
                        <li><strong>Session:</strong> <?= session_status() === PHP_SESSION_ACTIVE ? '✅ Active' : '❌ Inactive' ?></li>
                    </ul>
                </details>
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
            max-width: 500px;
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

        .error-message a {
            color: #fff;
            text-decoration: underline;
        }

        .success-message {
            background: #28a745;
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

        .admin-login-btn:hover:not(:disabled) {
            background: #1e3a72;
        }

        .admin-login-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
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

        .login-credentials {
            display: grid;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .credential-item {
            background: white;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .credential-item code {
            background: #e9ecef;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: monospace;
        }

        .security-note {
            font-size: 0.9rem;
            color: #856404;
            background: #fff3cd;
            padding: 0.75rem;
            border-radius: 4px;
            margin: 0;
        }

        .setup-info {
            background: #fff3cd;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #ffc107;
        }

        .setup-info h3 {
            color: #856404;
            margin-bottom: 1rem;
        }

        .setup-info ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        .setup-info code {
            background: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: monospace;
        }

        .setup-link {
            color: #856404;
            font-weight: bold;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: #ffeaa7;
            border-radius: 4px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .setup-link:hover {
            background: #fdcb6e;
        }

        .back-to-site {
            text-align: center;
            margin-bottom: 1.5rem;
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

        .system-info {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .system-info details {
            cursor: pointer;
        }

        .system-info summary {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .system-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .system-info li {
            padding: 0.25rem 0;
            font-size: 0.8rem;
            color: #666;
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

            .login-credentials {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        // Auto-focus on first empty field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');

            if (!usernameField.value) {
                usernameField.focus();
            } else {
                passwordField.focus();
            }
        });

        // Show loading state on form submit
        document.querySelector('.admin-login-form').addEventListener('submit', function() {
            const button = this.querySelector('.admin-login-btn');
            button.textContent = 'ප්‍රවේශ වෙමින්...';
            button.disabled = true;
        });
    </script>
</body>
</html>
