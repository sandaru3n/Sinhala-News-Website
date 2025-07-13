<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $content = $_POST['content'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $status = $_POST['status'] ?? 'draft';

    if (empty($title) || empty($category) || empty($content)) {
        $error = 'කරුණාකර සියලු අවශ්‍ය ක්ෂේත්‍ර පුරවන්න';
    } else {
        // In real application, save to database
        $message = 'ප්‍රවෘත්තිය සාර්ථකව ' . ($status === 'published' ? 'ප්‍රකාශනය' : 'සුරකින') . ' ලදී';
    }
}
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
                    <span>සිංහල ප්‍රවෘත්ති</span>
                </div>
                <nav class="admin-nav">
                    <a href="admin-dashboard.php">Dashboard</a>
                    <a href="admin-add-article.php" class="active">ADD NEWS</a>
                    <a href="index.html" target="_blank">Visit Website</a>
                    <a href="admin-dashboard.php?logout=1" class="logout-btn">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="container">
            <div class="add-article-container">
                <div class="page-header">
                    <h1>Add News Article</h1>
                    <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>
                </div>

                <?php if ($message): ?>
                    <div class="success-message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="article-form">
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="title">News Headline *</label>
                            <input type="text" id="title" name="title" required placeholder="Add News Headline Here">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Choose Category</option>
                                <option value="දේශපාලන">Politics</option>
                                <option value="ක්‍රීඩා">Sports</option>
                                <option value="තාක්ෂණය">Technology</option>
                                <option value="ව්‍යාපාර">Business</option>
                                <option value="විනෝදාස්වාදය">Entertainment</option>
                                <option value="සෞඛ්‍ය">Health</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Publish</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="image_url">Image URL</label>
                            <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                            <small>Add Image for News (Alternative)</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="content">Article Description *</label>
                            <textarea id="content" name="content" required placeholder="Add Description Here..."></textarea>
                            <small>ප්‍රවෘත්තියේ සම්පූර්ණ අන්තර්ගතය ඇතුළත් කරන්න</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn primary">
                            <span class="icon">💾</span>
                            Save Article
                        </button>
                        <a href="admin-dashboard.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>

                <!-- Preview Section -->
                <div class="preview-section">
                    <h2>Preview</h2>
                    <div class="preview-card">
                        <div class="preview-image" id="previewImage">
                            <img src="https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Preview+Image" alt="Preview">
                        </div>
                        <div class="preview-content">
                            <div class="preview-meta">
                                <span class="preview-category" id="previewCategory">Category</span>
                                <span class="preview-date"><?= date('Y ජූලි d') ?></span>
                            </div>
                            <h3 id="previewTitle">ප්‍රවෘත්ති ශීර්ෂය මෙහි දිස්වේ</h3>
                            <p id="previewContent">ප්‍රවෘත්ති අන්තර්ගතයේ පළමු කොටස මෙහි දිස්වේ...</p>
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

        document.getElementById('category').addEventListener('change', function() {
            document.getElementById('previewCategory').textContent = this.value || 'ප්‍රවර්ගය';
        });

        document.getElementById('content').addEventListener('input', function() {
            const content = this.value.substring(0, 150) + (this.value.length > 150 ? '...' : '');
            document.getElementById('previewContent').textContent = content || 'ප්‍රවෘත්ති අන්තර්ගතයේ පළමු කොටස මෙහි දිස්වේ...';
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
            min-height: 200px;
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: flex-start;
            margin-top: 2rem;
        }

        .save-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .save-btn:hover {
            background: #1e3a72;
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

        .preview-date {
            color: #666;
        }

        .preview-card h3 {
            margin-bottom: 0.75rem;
            font-size: 1.3rem;
            line-height: 1.3;
            color: #333;
        }

        .preview-card p {
            color: #666;
            line-height: 1.5;
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

            .save-btn {
                justify-content: center;
            }
        }
    </style>
</body>
</html>
