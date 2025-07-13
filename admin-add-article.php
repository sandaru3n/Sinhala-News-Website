<?php
require_once 'includes/config.php';

// Check if user is logged in and has permission
if (!is_editor_or_admin()) {
    redirect('admin.php');
}

$message = '';
$error = '';

try {
    $db = new Database();

    // Get categories for dropdown
    $categories = $db->getCategories();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error = 'Invalid request. Please try again.';
        } else {
            $title = sanitize_input($_POST['title'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $summary = sanitize_input($_POST['summary'] ?? '');
            $content = $_POST['content'] ?? ''; // Don't sanitize content as it may contain HTML
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
                    'author_id' => $_SESSION['user_id'],
                    'image_url' => !empty($image_url) ? $image_url : null,
                    'status' => $status,
                    'featured' => $featured
                ];

                try {
                    $article_id = $db->createArticle($article_data);

                    if ($article_id) {
                        // Update category count
                        $db->updateCategoryCount($category_id);

                        $message = 'ප්‍රවෘත්තිය සාර්ථකව ' . ($status === 'published' ? 'ප්‍රකාශනය' : 'සුරකින') . ' ලදී';

                        // Log the action
                        log_security_event('Article created', "Article ID: $article_id, Title: $title by user " . $_SESSION['username']);

                        // Clear form data on success
                        $_POST = [];
                    } else {
                        $error = 'ප්‍රවෘත්තිය සුරැකීමට නොහැකි විය';
                    }
                } catch (Exception $e) {
                    error_log("Article creation error: " . $e->getMessage());
                    $error = 'ප්‍රවෘත්තිය සුරැකීමේදී දෝෂයක් ඇතිවිය';
                }
            }
        }
    }

    // Get current user
    $current_user = get_logged_in_user();

} catch (Exception $e) {
    error_log("Add article page error: " . $e->getMessage());
    $error = 'Database connection error. Please try again later.';
    $categories = [];
}

$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>නව ප්‍රවෘත්තිය | Admin Panel</title>
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
                    <a href="admin-dashboard.php">Dashboard</a>
                    <a href="admin-add-article.php" class="active">නව ප්‍රවෘත්තිය</a>
                    <a href="index.php" target="_blank">වෙබ් අඩවිය</a>
                    <span class="user-info">Welcome, <?= htmlspecialchars($current_user['full_name'] ?? 'User') ?></span>
                    <a href="admin-dashboard.php?logout=1" class="logout-btn">ඉවත් වන්න</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <div class="add-article-container">
                <div class="page-header">
                    <h1>නව ප්‍රවෘත්තිය එක් කරන්න</h1>
                    <a href="admin-dashboard.php" class="back-btn">← Dashboard වෙත ආපසු</a>
                </div>

                <?php if ($message): ?>
                    <div class="success-message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="article-form" id="articleForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="title">ප්‍රවෘත්ති ශීර්ෂය *</label>
                            <input type="text" id="title" name="title" required
                                   placeholder="ප්‍රවෘත්ති ශීර්ෂය ඇතුළත් කරන්න"
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">ප්‍රවර්ගය *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">ප්‍රවර්ගය තෝරන්න</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"
                                            <?= (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name_sinhala']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">තත්ත්වය</label>
                            <select id="status" name="status">
                                <option value="draft" <?= (($_POST['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>කෙටුම්පත්</option>
                                <option value="published" <?= (($_POST['status'] ?? '') === 'published') ? 'selected' : '' ?>>ප්‍රකාශනය කරන්න</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="summary">සාරාංශය</label>
                            <textarea id="summary" name="summary" rows="3"
                                      placeholder="ප්‍රවෘත්ති සාරාංශය (හිස් තැබුවහොත් ස්වයංක්‍රීයව සෑදේ)"><?= htmlspecialchars($_POST['summary'] ?? '') ?></textarea>
                            <small>ප්‍රවෘත්තියේ කෙටි සාරාංශයක් (විකල්ප)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="image_url">රූප URL</label>
                            <input type="url" id="image_url" name="image_url"
                                   placeholder="https://example.com/image.jpg"
                                   value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">
                            <small>ප්‍රවෘත්තිය සඳහා රූප URL එකක් ඇතුළත් කරන්න (විකල්ප)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="content">ප්‍රවෘත්ති අන්තර්ගතය *</label>
                            <textarea id="content" name="content" required
                                      placeholder="ප්‍රවෘත්ති අන්තර්ගතය මෙහි ඇතුළත් කරන්න..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                            <small>ප්‍රවෘත්තියේ සම්පූර්ණ අන්තර්ගතය ඇතුළත් කරන්න</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="checkbox-label">
                                <input type="checkbox" id="featured" name="featured"
                                       <?= isset($_POST['featured']) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                ප්‍රධාන ප්‍රවෘත්තිය ලෙස සකසන්න
                            </label>
                            <small>මෙය ප්‍රධාන ප්‍රවෘත්තිය ලෙස හෝම් පේජයේ ප්‍රදර්ශනය කරයි</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn primary">
                            <span class="icon">💾</span>
                            ප්‍රවෘත්තිය සුරකින්න
                        </button>
                        <a href="admin-dashboard.php" class="cancel-btn">අවලංගු කරන්න</a>
                        <button type="button" class="preview-btn secondary" onclick="togglePreview()">
                            <span class="icon">👁️</span>
                            පෙරදසුන
                        </button>
                    </div>
                </form>

                <!-- Preview Section -->
                <div class="preview-section" id="previewSection" style="display: none;">
                    <h2>පෙරදසුන</h2>
                    <div class="preview-card">
                        <div class="preview-image" id="previewImage">
                            <img src="https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Preview+Image" alt="Preview">
                        </div>
                        <div class="preview-content">
                            <div class="preview-meta">
                                <span class="preview-category" id="previewCategory">ප්‍රවර්ගය</span>
                                <span class="preview-date"><?= date('Y ජූලි d') ?></span>
                                <span class="preview-author">කතුර: <?= htmlspecialchars($current_user['full_name'] ?? 'Admin') ?></span>
                            </div>
                            <h3 id="previewTitle">ප්‍රවෘත්ති ශීර්ෂය මෙහි දිස්වේ</h3>
                            <div class="preview-summary" id="previewSummary">
                                <p>ප්‍රවෘත්ති සාරාංශය මෙහි දිස්වේ...</p>
                            </div>
                            <div class="preview-content-full" id="previewContentFull">
                                <p>ප්‍රවෘත්ති අන්තර්ගතය මෙහි දිස්වේ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Live preview functionality
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('previewTitle').textContent = this.value || 'ප්‍රවෘත්ති ශීර්ෂය මෙහි දිස්වේ';
        });

        document.getElementById('category_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('previewCategory').textContent = selectedOption.text || 'ප්‍රවර්ගය';
        });

        document.getElementById('summary').addEventListener('input', function() {
            const summaryDiv = document.getElementById('previewSummary');
            if (this.value.trim()) {
                summaryDiv.innerHTML = '<p>' + this.value + '</p>';
            } else {
                summaryDiv.innerHTML = '<p>ප්‍රවෘත්ති සාරාංශය මෙහි දිස්වේ...</p>';
            }
        });

        document.getElementById('content').addEventListener('input', function() {
            const contentDiv = document.getElementById('previewContentFull');
            if (this.value.trim()) {
                // Convert line breaks to paragraphs
                const content = this.value.split('\n\n').map(p => p.trim()).filter(p => p).map(p => '<p>' + p.replace(/\n/g, '<br>') + '</p>').join('');
                contentDiv.innerHTML = content || '<p>ප්‍රවෘත්ති අන්තර්ගතය මෙහි දිස්වේ...</p>';
            } else {
                contentDiv.innerHTML = '<p>ප්‍රවෘත්ති අන්තර්ගතය මෙහි දිස්වේ...</p>';
            }
        });

        document.getElementById('image_url').addEventListener('input', function() {
            const img = document.querySelector('#previewImage img');
            if (this.value) {
                img.src = this.value;
                img.onerror = function() {
                    this.src = 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Invalid+Image+URL';
                };
            } else {
                img.src = 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Preview+Image';
            }
        });

        // Toggle preview
        function togglePreview() {
            const preview = document.getElementById('previewSection');
            const btn = document.querySelector('.preview-btn');

            if (preview.style.display === 'none') {
                preview.style.display = 'block';
                btn.innerHTML = '<span class="icon">🙈</span> පෙරදසුන සඟවන්න';
                preview.scrollIntoView({ behavior: 'smooth' });
            } else {
                preview.style.display = 'none';
                btn.innerHTML = '<span class="icon">👁️</span> පෙරදසුන';
            }
        }

        // Auto-generate summary from content if summary is empty
        document.getElementById('content').addEventListener('blur', function() {
            const summaryField = document.getElementById('summary');
            if (!summaryField.value.trim() && this.value.trim()) {
                const plainText = this.value.replace(/<[^>]*>/g, '').replace(/\n+/g, ' ').trim();
                summaryField.value = plainText.substring(0, 200) + (plainText.length > 200 ? '...' : '');
                // Trigger summary preview update
                summaryField.dispatchEvent(new Event('input'));
            }
        });

        // Form submission with validation
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            const category = document.getElementById('category_id').value;

            if (!title) {
                alert('කරුණාකර ප්‍රවෘත්ති ශීර්ෂය ඇතුළත් කරන්න');
                e.preventDefault();
                return;
            }

            if (!content) {
                alert('කරුණාකර ප්‍රවෘත්ති අන්තර්ගතය ඇතුළත් කරන්න');
                e.preventDefault();
                return;
            }

            if (!category) {
                alert('කරුණාකර ප්‍රවර්ගයක් තෝරන්න');
                e.preventDefault();
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('.save-btn');
            submitBtn.innerHTML = '<span class="icon">⏳</span> සුරකිමින්...';
            submitBtn.disabled = true;
        });

        // Character count for content
        document.getElementById('content').addEventListener('input', function() {
            const count = this.value.length;
            const label = this.previousElementSibling;
            const counter = label.querySelector('.char-count') || document.createElement('span');
            counter.className = 'char-count';
            counter.textContent = ` (${count} characters)`;
            if (!label.querySelector('.char-count')) {
                label.appendChild(counter);
            }
        });
    </script>

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

        .user-info {
            color: #666;
            font-size: 0.9rem;
        }

        .add-article-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #333;
            margin: 0;
        }

        .back-btn {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #1e3a72;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            border: 1px solid #f5c6cb;
        }

        .article-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .form-row {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            flex: 1;
        }

        .form-group.full-width {
            flex: 1 1 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2c5aa0;
        }

        .form-group textarea {
            resize: vertical;
        }

        #content {
            min-height: 200px;
        }

        #summary {
            min-height: 80px;
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            font-weight: normal !important;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: flex-start;
            margin-top: 2rem;
        }

        .save-btn,
        .preview-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .save-btn.primary {
            background: #2c5aa0;
            color: white;
        }

        .save-btn.primary:hover:not(:disabled) {
            background: #1e3a72;
        }

        .save-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .preview-btn.secondary {
            background: #6c757d;
            color: white;
        }

        .preview-btn.secondary:hover {
            background: #545b62;
        }

        .cancel-btn {
            color: #666;
            text-decoration: none;
            padding: 1rem 2rem;
            transition: color 0.3s ease;
        }

        .cancel-btn:hover {
            color: #333;
        }

        .preview-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .preview-section h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .preview-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .preview-content {
            padding: 1.5rem;
        }

        .preview-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .preview-category {
            background: #2c5aa0;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .preview-date,
        .preview-author {
            color: #666;
        }

        .preview-card h3 {
            margin-bottom: 0.75rem;
            font-size: 1.3rem;
            line-height: 1.3;
            color: #333;
        }

        .preview-summary {
            color: #666;
            margin-bottom: 1rem;
            font-style: italic;
            border-left: 3px solid #2c5aa0;
            padding-left: 1rem;
        }

        .preview-content-full {
            color: #333;
            line-height: 1.6;
        }

        .preview-content-full p {
            margin-bottom: 1rem;
        }

        .char-count {
            color: #666;
            font-size: 0.8rem;
            font-weight: normal;
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

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .form-row {
                flex-direction: column;
                gap: 1rem;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .save-btn,
            .preview-btn {
                justify-content: center;
            }
        }
    </style>
</body>
</html>
