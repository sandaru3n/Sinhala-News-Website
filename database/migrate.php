<?php
/**
 * Database Migration Script for Sinhala News Website
 * Run this script to set up the database structure
 */

// Include configuration
require_once __DIR__ . '/../includes/config.php';

/**
 * Create database if it doesn't exist
 */
function createDatabase() {
    $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($connection->query($sql) === TRUE) {
        echo "✅ Database '" . DB_NAME . "' created successfully or already exists.\n";
    } else {
        die("❌ Error creating database: " . $connection->error);
    }

    $connection->close();
}

/**
 * Run SQL file
 */
function runSqlFile($filename) {
    if (!file_exists($filename)) {
        die("❌ SQL file not found: $filename\n");
    }

    $sql = file_get_contents($filename);

    // Connect to database
    $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($connection->connect_error) {
        die("❌ Connection failed: " . $connection->connect_error);
    }

    // Set charset
    $connection->set_charset('utf8mb4');

    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $success_count = 0;
    $error_count = 0;

    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }

        if ($connection->multi_query($statement . ';')) {
            do {
                if ($result = $connection->store_result()) {
                    $result->free();
                }
            } while ($connection->next_result());
            $success_count++;
        } else {
            echo "⚠️  Error executing statement: " . $connection->error . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n";
            $error_count++;
        }
    }

    echo "✅ Executed $success_count statements successfully.\n";
    if ($error_count > 0) {
        echo "⚠️  $error_count statements had errors.\n";
    }

    $connection->close();
}

/**
 * Test database connection
 */
function testConnection() {
    try {
        $db = new Database();
        if ($db->isConnected()) {
            echo "✅ Database connection test successful.\n";

            // Test some queries
            $categories = $db->getCategories();
            echo "✅ Found " . count($categories) . " categories.\n";

            $articles = $db->getPublishedArticles(1, 5);
            echo "✅ Found " . count($articles) . " published articles.\n";

            return true;
        } else {
            echo "❌ Database connection test failed.\n";
            return false;
        }
    } catch (Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Create necessary directories
 */
function createDirectories() {
    $directories = [
        __DIR__ . '/../uploads',
        __DIR__ . '/../uploads/articles',
        __DIR__ . '/../logs',
        __DIR__ . '/../cache'
    ];

    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            echo "✅ Created directory: $dir\n";
        }
    }
}

/**
 * Create .htaccess for uploads security
 */
function createHtaccess() {
    $htaccess_content = "# Prevent direct access to PHP files\n";
    $htaccess_content .= "<Files *.php>\n";
    $htaccess_content .= "    Order allow,deny\n";
    $htaccess_content .= "    Deny from all\n";
    $htaccess_content .= "</Files>\n\n";
    $htaccess_content .= "# Allow image files\n";
    $htaccess_content .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp)$\">\n";
    $htaccess_content .= "    Order allow,deny\n";
    $htaccess_content .= "    Allow from all\n";
    $htaccess_content .= "</FilesMatch>\n";

    $htaccess_file = __DIR__ . '/../uploads/.htaccess';
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, $htaccess_content);
        echo "✅ Created .htaccess for uploads directory.\n";
    }
}

/**
 * Main migration function
 */
function runMigration() {
    echo "🚀 Starting database migration for Sinhala News Website...\n\n";

    // Step 1: Create database
    echo "Step 1: Creating database...\n";
    createDatabase();
    echo "\n";

    // Step 2: Run schema
    echo "Step 2: Creating tables and inserting sample data...\n";
    runSqlFile(__DIR__ . '/schema.sql');
    echo "\n";

    // Step 3: Create directories
    echo "Step 3: Creating necessary directories...\n";
    createDirectories();
    echo "\n";

    // Step 4: Create .htaccess
    echo "Step 4: Setting up security files...\n";
    createHtaccess();
    echo "\n";

    // Step 5: Test connection
    echo "Step 5: Testing database connection...\n";
    if (testConnection()) {
        echo "\n✅ Migration completed successfully!\n";
        echo "\n📝 Default Login Credentials:\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
        echo "\n🌐 You can now access:\n";
        echo "   - Website: http://localhost:8000/\n";
        echo "   - Admin Panel: http://localhost:8000/admin.php\n";
        echo "\n⚠️  Remember to:\n";
        echo "   1. Change default passwords\n";
        echo "   2. Update database credentials in includes/config.php\n";
        echo "   3. Set proper file permissions in production\n";
    } else {
        echo "\n❌ Migration completed with errors. Please check the logs.\n";
    }
}

// Run migration if executed directly
if (php_sapi_name() === 'cli') {
    runMigration();
} else {
    // Web interface for migration
    ?>
    <!DOCTYPE html>
    <html lang="si">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Migration - සිංහල ප්‍රවෘත්ති</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .button { background: #007cba; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
            .button:hover { background: #005a87; }
            .output { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-top: 20px; white-space: pre-wrap; font-family: monospace; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>සිංහල ප්‍රවෘත්ති Database Migration</h1>
            <p>This will set up the MySQL database for your news website.</p>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong> Make sure you have:
            <ul>
                <li>MySQL server running</li>
                <li>Correct database credentials in <code>includes/config.php</code></li>
                <li>Proper permissions to create databases</li>
            </ul>
        </div>

        <?php if (isset($_POST['run_migration'])): ?>
            <div class="output">
                <?php
                ob_start();
                runMigration();
                $output = ob_get_clean();
                echo htmlspecialchars($output);
                ?>
            </div>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="run_migration" class="button">
                    🚀 Run Database Migration
                </button>
            </form>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <h3>Current Configuration:</h3>
            <ul>
                <li><strong>Host:</strong> <?= DB_HOST ?></li>
                <li><strong>Database:</strong> <?= DB_NAME ?></li>
                <li><strong>Username:</strong> <?= DB_USERNAME ?></li>
                <li><strong>Charset:</strong> <?= DB_CHARSET ?></li>
            </ul>
        </div>
    </body>
    </html>
    <?php
}
?>
