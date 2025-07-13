<?php
/**
 * Database Configuration for Sinhala News Website
 * MySQL Database Settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'sinhala_news');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost:8000');
define('SITE_TITLE', 'සිංහල ප්‍රවෘත්ති');
define('SITE_TAGLINE', 'ශ්‍රී ලංකාවේ ප්‍රධාන ප්‍රවෘත්ති වෙබ් අඩවිය');

// Admin Configuration
define('ADMIN_EMAIL', 'admin@sinhalanews.lk');
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Upload Configuration
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ARTICLES_PER_PAGE', 10);
define('ADMIN_ARTICLES_PER_PAGE', 20);

// Security
define('HASH_ALGORITHM', 'sha256');
define('CSRF_TOKEN_LENGTH', 32);

// Timezone
date_default_timezone_set('Asia/Colombo');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-load database class
require_once __DIR__ . '/Database.php';

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize Input
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate Slug from Title
 */
function generate_slug($title) {
    // Convert to lowercase
    $slug = strtolower($title);

    // Replace spaces and special characters with hyphens
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);

    // Remove leading and trailing hyphens
    $slug = trim($slug, '-');

    return $slug;
}

/**
 * Format Date for Display
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Get Time Ago
 */
function time_ago($date) {
    $time_ago = strtotime($date);
    $current_time = time();
    $time_difference = $current_time - $time_ago;

    if ($time_difference < 60) {
        return 'මිනිත්තු කිහිපයකට පෙර';
    } elseif ($time_difference < 3600) {
        $minutes = floor($time_difference / 60);
        return $minutes . ' මිනිත්තුවකට පෙර';
    } elseif ($time_difference < 86400) {
        $hours = floor($time_difference / 3600);
        return $hours . ' පැයකට පෙර';
    } elseif ($time_difference < 2592000) {
        $days = floor($time_difference / 86400);
        return $days . ' දිනකට පෙර';
    } else {
        return format_date($date, 'Y-m-d');
    }
}

/**
 * Redirect Function
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if user is editor or admin
 */
function is_editor_or_admin() {
    return is_logged_in() && in_array($_SESSION['user_role'], ['admin', 'editor']);
}

/**
 * Get current user info
 */
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }

    $db = new Database();
    return $db->getUser($_SESSION['user_id']);
}

/**
 * Log security events
 */
function log_security_event($event, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - $event";
    if ($details) {
        $log_entry .= " - $details";
    }
    $log_entry .= " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

    file_put_contents(__DIR__ . '/../logs/security.log', $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Create logs directory if it doesn't exist
 */
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

/**
 * Check if database is connected
 */
function check_database_connection() {
    try {
        $db = new Database();
        return $db->isConnected();
    } catch (Exception $e) {
        return false;
    }
}
?>
