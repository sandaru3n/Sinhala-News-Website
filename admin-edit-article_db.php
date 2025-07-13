<?php
require_once 'includes/config.php';

// Check if user is logged in and has permission
if (!is_editor_or_admin()) {
    redirect('admin_db.php');
}

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($article_id <= 0) {
    redirect('admin-dashboard_db.php');
}

$message = '';
$error = '';

try {
    $db = new Database();

    // Get categories for dropdown
    $categories = $db->getCategories();

    // Fetch the article to edit
    $article = $db->getArticle($article_id);
    if (!$article) {
        $error = 'ප්‍රවෘත්තිය සොයාගත නොහැක.';
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $article) {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid request. Please try again.';
        } else {
            $title = sanitize_input($_POST['title'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $summary = sanitize_input($_POST['summary'] ?? '');
            $content = $_POST['content'] ?? '';
            $image_url = sanitize_input($_POST['image_url'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $featured = isset($_POST['featured']) ? 1 : 0;

            // Validation
            if (empty($title)) {
                $error = 'කරුණාකර ප්‍රවෘත්ති ශීර්ෂය ඇතුළත් කරන්න';
            } elseif (empty($content)) {
                $error = 'කරුණාකර ප්‍රවෘත්ති අන්තර්ගතය ඇතුළත් කරන්න';
            } elseif ($category_id <= 0) {
                $error = 'කරුණාකර ප්‍රවර්ගයක් තෝරන්න';
            } else {
                // Generate summary if not provided
                if (empty($summary)) {
                    $summary = substr(strip_tags($content), 0, 200) . '...';
                }

                // Prepare article data
                $article_data = [
                    'title' => $title,
                    'summary' => $summary,
                    'content' => $content,
                    'category_id' => $category_id,
                    'image_url' => !empty($image_url) ? $image_url : null,
                    'status' => $status,
                    'featured' => $featured
                ];

                try {
                    if ($db->updateArticle($article_id, $article_data)) {
                        $db->updateCategoryCount($category_id);
                        $message = 'ප්‍රවෘත්තිය සාර්ථකව යාවත්කාලීන කරන ලදී';
                        log_security_event('Article updated', "Article ID: $article_id, Title: $title by user " . $_SESSION['username']);
                        // Refresh article data
                        $article = $db->getArticle($article_id);
                        // Optionally redirect to dashboard
                        redirect('admin-dashboard_db.php');
                    } else {
                        $error = 'ප්‍රවෘත්තිය යාවත්කාලීන කිරීමට නොහැකි විය';
                    }
                } catch (Exception $e) {
                    error_log("Article update error: " . $e->getMessage());
                    $error = 'ප්‍රවෘත්තිය යාවත්කාලීන කිරීමේදී දෝෂයක් ඇතිවිය';
                }
            }
        }
    }

    // Get current user
    $current_user = get_logged_in_user();

} catch (Exception $e) {
    error_log("Edit article page error: " . $e->getMessage());
    $error = 'Database connection error. Please try again later.';
    $categories = [];
    $article = null;
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ප්‍රවෘත්තිය සංස්කරණය | Admin Panel</title>
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
                    <a href="admin-dashboard_db.php">Dashboard</a>
                    <a href="admin-add-article_db.php">නව ප්‍රවෘත්තිය</a>
                    <a href="index_db.php" target="_blank">වෙබ් අඩවිය</a>
                    <span class="user-info">Welcome, <?= htmlspecialchars($current_user['full_name'] ?? 'User') ?></span>
                    <a href="admin-dashboard_db.php?logout=1" class="logout-btn">ඉවත් වන්න</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <div class="add-article-container">
                <div class="page-header">
                    <h1>ප්‍රවෘත්තිය සංස්කරණය</h1>
                    <a href="admin-dashboard_db.php" class="back-btn">← Dashboard වෙත ආපසු</a>
                </div>

                <?php if ($message): ?>
                    <div class="success-message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($article): ?>
                <form method="POST" class="article-form" id="articleForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="title">ප්‍රවෘත්ති ශීර්ෂය *</label>
                            <input type="text" id="title" name="title" required
                                   placeholder="ප්‍රවෘත්ති ශීර්ෂය ඇතුළත් කරන්න"
                                   value="<?= htmlspecialchars($_POST['title'] ?? $article['title']) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">ප්‍රවර්ගය *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">ප්‍රවර්ගය තෝරන්න</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"
                                            <?= ((($_POST['category_id'] ?? $article['category_id']) == $category['id']) ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($category['name_sinhala']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">තත්ත්වය</label>
                            <select id="status" name="status">
                                <option value="draft" <?= ((($_POST['status'] ?? $article['status']) === 'draft') ? 'selected' : '') ?>>කෙටුම්පත්</option>
                                <option value="published" <?= ((($_POST['status'] ?? $article['status']) === 'published') ? 'selected' : '') ?>>ප්‍රකාශනය කරන්න</option>
                                <option value="archived" <?= ((($_POST['status'] ?? $article['status']) === 'archived') ? 'selected' : '') ?>>සංරක්ෂිත</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="summary">සාරාංශය</label>
                            <textarea id="summary" name="summary" rows="3"
                                      placeholder="ප්‍රවෘත්ති සාරාංශය (හිස් තැබුවහොත් ස්වයංක්‍රීයව සෑදේ)"><?= htmlspecialchars($_POST['summary'] ?? $article['summary']) ?></textarea>
                            <small>ප්‍රවෘත්තියේ කෙටි සාරාංශයක් (විකල්ප)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="image_url">රූප URL</label>
                            <input type="url" id="image_url" name="image_url"
                                   placeholder="https://example.com/image.jpg"
                                   value="<?= htmlspecialchars($_POST['image_url'] ?? $article['image_url']) ?>">
                            <small>ප්‍රවෘත්තිය සඳහා රූප URL එකක් ඇතුළත් කරන්න (විකල්ප)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="content">ප්‍රවෘත්ති අන්තර්ගතය *</label>
                            <textarea id="content" name="content" required
                                      placeholder="ප්‍රවෘත්ති අන්තර්ගතය මෙහි ඇතුළත් කරන්න..."><?= htmlspecialchars($_POST['content'] ?? $article['content']) ?></textarea>
                            <small>ප්‍රවෘත්තියේ සම්පූර්ණ අන්තර්ගතය ඇතුළත් කරන්න</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="featured">
                                <input type="checkbox" id="featured" name="featured" value="1" <?= ((isset($_POST['featured']) ? $_POST['featured'] : $article['featured']) ? 'checked' : '') ?>>
                                ප්‍රධාන ප්‍රවෘත්තිය ලෙස සලකන්න
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">යාවත්කාලීන කරන්න</button>
                            <a href="admin-dashboard_db.php" class="btn btn-secondary" style="margin-left:10px;">අවලංගු කරන්න</a>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 