<?php
require_once 'includes/config.php';

// Check if admin is logged in
if (!is_logged_in()) {
    redirect('admin.php');
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('admin.php');
}

try {
    $db = new Database();

    // Handle article actions
    $message = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid request. Please try again.';
        } else {
            $action = $_POST['action'] ?? '';
            $article_id = (int)($_POST['article_id'] ?? 0);

            if ($action === 'delete' && $article_id > 0) {
                if ($db->deleteArticle($article_id)) {
                    $message = 'ප්‍රවෘත්තිය සාර්ථකව ඉවත් කරන ලදී';
                    log_security_event('Article deleted', "Article ID: $article_id by user " . $_SESSION['username']);
                } else {
                    $error = 'ප්‍රවෘත්තිය ඉවත් කිරීමට නොහැකි විය';
                }
            } elseif ($action === 'publish' && $article_id > 0) {
                $data = ['status' => 'published'];
                if ($db->updateArticle($article_id, $data)) {
                    $message = 'ප්‍රවෘත්තිය සාර්ථකව ප්‍රකාශනය කරන ලදී';
                } else {
                    $error = 'ප්‍රවෘත්තිය ප්‍රකාශනය කිරීමට නොහැකි විය';
                }
            } elseif ($action === 'feature' && $article_id > 0) {
                // First, remove featured status from all articles
                $db->execute("UPDATE articles SET featured = 0");
                // Then set this article as featured
                $data = ['featured' => 1];
                if ($db->updateArticle($article_id, $data)) {
                    $message = 'ප්‍රවෘත්තිය ප්‍රධාන ප්‍රවෘත්තිය ලෙස සකසන ලදී';
                }
            }
        }
    }

    // Get statistics
    $stats = $db->getSiteStats();

    // Get recent articles for management
    $articles = $db->getAllArticlesForAdmin(1, 10);

    // Get recent activity
    $recent_activity = $db->getRecentActivity(5);

    // Get current user
    $current_user = get_logged_in_user();

} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $error = 'Database connection error. Please try again later.';
    $stats = [];
    $articles = [];
    $recent_activity = [];
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <div class="admin-header-content">
                <div class="admin-logo">
                    <h1>Admin Panel</h1>
                    <span><?= SITE_TITLE ?></span>
                </div>
                <nav class="admin-nav">
                    <a href="admin-dashboard.php" class="active">Dashboard</a>
                    <a href="admin-add-article.php">නව ප්‍රවෘත්තිය</a>
                    <a href="index.php" target="_blank">වෙබ් අඩවිය</a>
                    <span class="user-info">Welcome, <?= htmlspecialchars($current_user['full_name'] ?? 'Admin') ?></span>
                    <a href="?logout=1" class="logout-btn">ඉවත් වන්න</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <?php if ($message): ?>
                <div class="admin-message success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="admin-message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>මුළු ප්‍රවෘත්ති</h3>
                    <div class="stat-number"><?= $stats['total_articles'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>ප්‍රකාශිත</h3>
                    <div class="stat-number"><?= $stats['published_articles'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>කෙටුම්පත්</h3>
                    <div class="stat-number"><?= $stats['draft_articles'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <h3>ප්‍රවර්ග</h3>
                    <div class="stat-number"><?= $stats['total_categories'] ?? 0 ?></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>ක්ෂණික ක්‍රියා</h2>
                <div class="action-buttons">
                    <a href="admin-add-article.php" class="action-btn primary">
                        <span class="icon">➕</span>
                        නව ප්‍රවෘත්තිය එක් කරන්න
                    </a>
                    <a href="admin-dashboard.php" class="action-btn secondary">
                        <span class="icon">📊</span>
                        වාර්තා බලන්න
                    </a>
                    <a href="database/migrate.php" class="action-btn secondary" target="_blank">
                        <span class="icon">⚙️</span>
                        Database Setup
                    </a>
                </div>
            </div>

            <!-- Articles Management -->
            <div class="articles-management">
                <div class="section-header">
                    <h2>ප්‍රවෘත්ති කළමනාකරණය</h2>
                    <a href="admin-add-article.php" class="add-btn">නව ප්‍රවෘත්තිය</a>
                </div>

                <div class="articles-table">
                    <?php if (!empty($articles)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ශීර්ෂය</th>
                                    <th>ප්‍රවර්ගය</th>
                                    <th>කර්තෘ</th>
                                    <th>දිනය</th>
                                    <th>තත්ත්වය</th>
                                    <th>ක්‍රියාමාර්ග</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td><?= $article['id'] ?></td>
                                        <td class="article-title">
                                            <a href="article.php?id=<?= $article['id'] ?>" target="_blank">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </a>
                                            <?php if ($article['featured']): ?>
                                                <span class="featured-badge">ප්‍රධාන</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($article['category_name'] ?? 'නොමැත') ?></td>
                                        <td><?= htmlspecialchars($article['author_name'] ?? 'නොමැත') ?></td>
                                        <td><?= format_date($article['created_at'], 'Y-m-d') ?></td>
                                        <td>
                                            <span class="status <?= $article['status'] ?>">
                                                <?php
                                                switch($article['status']) {
                                                    case 'published': echo 'ප්‍රකාශිත'; break;
                                                    case 'draft': echo 'කෙටුම්පත්'; break;
                                                    case 'archived': echo 'සංරක්ෂිත'; break;
                                                    default: echo $article['status'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="admin-edit-article.php?id=<?= $article['id'] ?>" class="edit-btn">සංස්කරණය</a>

                                            <?php if ($article['status'] === 'draft'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="action" value="publish">
                                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                    <button type="submit" class="publish-btn">ප්‍රකාශනය</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if (!$article['featured'] && $article['status'] === 'published'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="action" value="feature">
                                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                    <button type="submit" class="feature-btn">ප්‍රධාන කරන්න</button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" style="display: inline;" onsubmit="return confirm('ඔබට මෙම ප්‍රවෘත්තිය ඉවත් කිරීමට අවශ්‍යද?')">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                <button type="submit" class="delete-btn">ඉවත් කරන්න</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-articles">
                            <p>ප්‍රවෘත්ති නොමැත. <a href="admin-add-article.php">පළමු ප්‍රවෘත්තිය එක් කරන්න</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>මෑත ක්‍රියාකාරකම්</h2>
                <div class="activity-list">
                    <?php if (!empty($recent_activity)): ?>
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <span class="activity-icon">
                                    <?= $activity['type'] === 'article' ? '📝' : '📧' ?>
                                </span>
                                <div class="activity-content">
                                    <p>
                                        <strong><?= $activity['type'] === 'article' ? 'නව ප්‍රවෘත්තිය' : 'නව පණිවිඩය' ?></strong>:
                                        <?= htmlspecialchars($activity['item']) ?>
                                    </p>
                                    <small><?= time_ago($activity['date']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <span class="activity-icon">ℹ️</span>
                            <div class="activity-content">
                                <p>මෑත ක්‍රියාකාරකම් නොමැත</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .admin-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo h1 {
            color: #2c5aa0;
            margin: 0;
            font-size: 1.5rem;
        }

        .admin-logo span {
            color: #666;
            font-size: 0.9rem;
        }

        .admin-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .admin-nav a {
            color: #333;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .admin-nav a.active,
        .admin-nav a:hover {
            background: #e9ecef;
        }

        .admin-nav .logout-btn {
            background: #dc3545;
            color: white;
        }

        .admin-nav .logout-btn:hover {
            background: #c82333;
        }

        .user-info {
            color: #666;
            font-size: 0.9rem;
        }

        .admin-main {
            padding: 0 0 3rem;
        }

        .admin-message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            border: 1px solid transparent;
        }

        .admin-message.success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .admin-message.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            margin: 0 0 1rem 0;
            font-size: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c5aa0;
        }

        .quick-actions {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .quick-actions h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn.primary {
            background: #2c5aa0;
            color: white;
        }

        .action-btn.primary:hover {
            background: #1e3a72;
        }

        .action-btn.secondary {
            background: #e9ecef;
            color: #333;
        }

        .action-btn.secondary:hover {
            background: #dee2e6;
        }

        .articles-management {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-header h2 {
            color: #333;
            margin: 0;
        }

        .add-btn {
            background: #28a745;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .add-btn:hover {
            background: #218838;
        }

        .articles-table {
            overflow-x: auto;
        }

        .articles-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .articles-table th,
        .articles-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .articles-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .article-title {
            max-width: 300px;
        }

        .article-title a {
            color: #2c5aa0;
            text-decoration: none;
        }

        .article-title a:hover {
            text-decoration: underline;
        }

        .featured-badge {
            background: #ffc107;
            color: #212529;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status.published {
            background: #d4edda;
            color: #155724;
        }

        .status.draft {
            background: #fff3cd;
            color: #856404;
        }

        .status.archived {
            background: #d1ecf1;
            color: #0c5460;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .edit-btn,
        .publish-btn,
        .feature-btn,
        .delete-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .publish-btn {
            background: #28a745;
            color: white;
        }

        .publish-btn:hover {
            background: #218838;
        }

        .feature-btn {
            background: #ffc107;
            color: #212529;
        }

        .feature-btn:hover {
            background: #e0a800;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .no-articles {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-articles a {
            color: #2c5aa0;
            text-decoration: none;
        }

        .recent-activity {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .recent-activity h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .activity-icon {
            font-size: 1.5rem;
        }

        .activity-content p {
            margin: 0 0 0.25rem 0;
        }

        .activity-content small {
            color: #666;
        }

        @media (max-width: 768px) {
            .admin-header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .admin-nav {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .dashboard-stats {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .action-buttons {
                flex-direction: column;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .articles-table {
                font-size: 0.9rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
