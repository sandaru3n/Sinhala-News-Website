<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Sample articles data (in real app, this would be from database)
$articles = [
    [
        'id' => 1,
        'title' => 'ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය',
        'category' => 'දේශපාලන',
        'date' => '2025-07-13',
        'status' => 'Published'
    ],
    [
        'id' => 2,
        'title' => 'ක්‍රිකට් ලෝක කුසලානයේ ශ්‍රී ලංකා කණ්ඩායම',
        'category' => 'ක්‍රීඩා',
        'date' => '2025-07-13',
        'status' => 'Published'
    ],
    [
        'id' => 3,
        'title' => 'නව තාක්ෂණික නවෝත්පාදන ආයතනයක් පිහිටුවීම',
        'category' => 'තාක්ෂණය',
        'date' => '2025-07-12',
        'status' => 'Draft'
    ]
];

// Handle article actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $article_id = $_POST['article_id'] ?? '';

    if ($action === 'delete') {
        // In real app, delete from database
        $message = 'ප්‍රවෘත්තිය ඉවත් කරන ලදී';
    } elseif ($action === 'publish') {
        // In real app, update status in database
        $message = 'ප්‍රවෘත්තිය ප්‍රකාශනය කරන ලදී';
    }
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | සිංහල ප්‍රවෘත්ති</title>
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
                    <span>සිංහල ප්‍රවෘත්ති</span>
                </div>
                <nav class="admin-nav">
                    <a href="admin-dashboard.php" class="active">Dashboard</a>
                    <a href="admin-add-article.php">නව ප්‍රවෘත්තිය</a>
                    <a href="index.html" target="_blank">වෙබ් අඩවිය</a>
                    <a href="?logout=1" class="logout-btn">ඉවත් වන්න</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <?php if (isset($message)): ?>
                <div class="admin-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total News</h3>
                    <div class="stat-number"><?= count($articles) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Published</h3>
                    <div class="stat-number"><?= count(array_filter($articles, fn($a) => $a['status'] === 'Published')) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Draft</h3>
                    <div class="stat-number"><?= count(array_filter($articles, fn($a) => $a['status'] === 'Draft')) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Categories</h3>
                    <div class="stat-number">6</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Action</h2>
                <div class="action-buttons">
                    <a href="admin-add-article.php" class="action-btn primary">
                        <span class="icon">➕</span>
                        Add News
                    </a>
                    <a href="#" class="action-btn secondary">
                        <span class="icon">📊</span>
                        Records
                    </a>
                    <a href="#" class="action-btn secondary">
                        <span class="icon">⚙️</span>
                        Settings
                    </a>
                </div>
            </div>

            <!-- Articles Management -->
            <div class="articles-management">
                <div class="section-header">
                    <h2>News Management</h2>
                    <a href="admin-add-article.php" class="add-btn">Add News</a>
                </div>

                <div class="articles-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Heqadline</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td><?= $article['id'] ?></td>
                                    <td class="article-title"><?= htmlspecialchars($article['title']) ?></td>
                                    <td><?= $article['category'] ?></td>
                                    <td><?= $article['date'] ?></td>
                                    <td>
                                        <span class="status <?= $article['status'] === 'Published' ? 'published' : 'draft' ?>">
                                            <?= $article['status'] === 'Published' ? 'Published' : 'Draft' ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="admin-edit-article.php?id=<?= $article['id'] ?>" class="edit-btn">සංස්කරණය</a>
                                        <?php if ($article['status'] === 'Draft'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="publish">
                                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                <button type="submit" class="publish-btn">ප්‍රකාශනය</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('ඔබට මෙම ප්‍රවෘත්තිය ඉවත් කිරීමට අවශ්‍යද?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                            <button type="submit" class="delete-btn">ඉවත් කරන්න</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-list">
                    <div class="activity-item">
                        <span class="activity-icon">📝</span>
                        <div class="activity-content">
                            <p><strong>News</strong> එක් කරන ලදී</p>
                            <small>2 පැය කට පෙර</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-icon">✏️</span>
                        <div class="activity-content">
                            <p><strong>ප්‍රවෘත්තිය සංස්කරණය</strong> කරන ලදී</p>
                            <small>5 පැය කට පෙර</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-icon">🗑️</span>
                        <div class="activity-content">
                            <p><strong>ප්‍රවෘත්තිය ඉවත්</strong> කරන ලදී</p>
                            <small>1 දින කට පෙර</small>
                        </div>
                    </div>
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

        .admin-main {
            padding: 0 0 3rem;
        }

        .admin-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
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
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .edit-btn,
        .publish-btn,
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

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
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
