<?php
require_once 'includes/config.php';

// Get article ID from URL
$article_id = (int)($_GET['id'] ?? 0);

if ($article_id <= 0) {
    redirect('index_db.php');
}

try {
    $db = new Database();

    // Get article details
    $article = $db->getArticle($article_id);

    if (!$article || $article['status'] !== 'published') {
        // Article not found or not published
        redirect('index_db.php');
    }

    // Increment view count
    $db->incrementViews($article_id);

    // Get related articles from same category
    $related_articles = [];
    if ($article['category_id']) {
        $category_articles = $db->getArticlesByCategory($article['category_slug'], 1, 4);
        $related_articles = array_filter($category_articles, function($a) use ($article_id) {
            return $a['id'] != $article_id;
        });
        $related_articles = array_slice($related_articles, 0, 3);
    }

    // Get popular articles for sidebar
    $popular_articles = $db->getPopularArticles(3);

    // Get categories
    $categories = $db->getCategories();

} catch (Exception $e) {
    error_log("Article page error: " . $e->getMessage());
    redirect('index_db.php');
}

?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | <?= SITE_TITLE ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($article['summary']), 0, 160)) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['summary']) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($article['image_url'] ?? '') ?>">
    <meta property="og:url" content="<?= SITE_URL ?>/article_db.php?id=<?= $article_id ?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="index_db.php" style="text-decoration: none; color: inherit;"><?= SITE_TITLE ?></a></h1>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index_db.php">මුල් පිටුව</a></li>
                        <li><a href="category_db.php?cat=politics">දේශපාලන</a></li>
                        <li><a href="category_db.php?cat=sports">ක්‍රීඩා</a></li>
                        <li><a href="category_db.php?cat=technology">තාක්ෂණය</a></li>
                        <li><a href="category_db.php?cat=business">ව්‍යාපාර</a></li>
                        <li><a href="category_db.php?cat=entertainment">විනෝදාස්වාදය</a></li>
                    </ul>
                </nav>
                <div class="search-box">
                    <form action="search_db.php" method="GET">
                        <input type="text" name="q" id="searchInput" placeholder="ප්‍රවෘත්ති සොයන්න..." required>
                        <button type="submit" id="searchBtn">සොයන්න</button>
                    </form>
                </div>
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="index_db.php">මුල් පිටුව</a> &gt;
            <a href="category_db.php?cat=<?= htmlspecialchars($article['category_slug']) ?>"><?= htmlspecialchars($article['category_name']) ?></a> &gt;
            <span><?= htmlspecialchars($article['title']) ?></span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="article-layout">
                <article class="article-content">
                    <!-- Article Header -->
                    <header class="article-header">
                        <div class="article-meta">
                            <span class="category"><?= htmlspecialchars($article['category_name']) ?></span>
                            <span class="date"><?= format_date($article['published_at'], 'Y ජූලි d') ?></span>
                            <span class="author">කතුර: <?= htmlspecialchars($article['author_name']) ?></span>
                            <span class="views"><?= $article['views'] ?> බැලීම්</span>
                        </div>
                        <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>

                        <!-- Summary -->
                        <?php if (!empty($article['summary'])): ?>
                            <div class="article-summary">
                                <p><?= htmlspecialchars($article['summary']) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Social Share -->
                        <div class="article-share">
                            <span>බෙදා ගන්න:</span>
                            <div class="social-share-buttons">
                                <button onclick="shareToFacebook('<?= addslashes($article['title']) ?>', window.location.href)" class="social-share-btn facebook">
                                    <span class="share-icon">📘</span>
                                    Facebook
                                </button>
                                <button onclick="shareToTwitter('<?= addslashes($article['title']) ?>', window.location.href)" class="social-share-btn twitter">
                                    <span class="share-icon">🐦</span>
                                    Twitter
                                </button>
                                <button onclick="shareToWhatsApp('<?= addslashes($article['title']) ?>', window.location.href)" class="social-share-btn whatsapp">
                                    <span class="share-icon">💬</span>
                                    WhatsApp
                                </button>
                                <button onclick="copyToClipboard(window.location.href)" class="social-share-btn copy">
                                    <span class="share-icon">📋</span>
                                    ලිංකය පිටපත්
                                </button>
                                <button onclick="window.print()" class="social-share-btn print">
                                    <span class="share-icon">🖨️</span>
                                    මුද්‍රණය
                                </button>
                            </div>
                        </div>
                    </header>

                    <!-- Article Image -->
                    <?php if (!empty($article['image_url'])): ?>
                        <div class="article-image">
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                        </div>
                    <?php endif; ?>

                    <!-- Article Body -->
                    <div class="article-body">
                        <?php
                        // Convert line breaks to paragraphs
                        $content = htmlspecialchars($article['content']);
                        $paragraphs = explode("\n\n", $content);
                        foreach ($paragraphs as $paragraph) {
                            if (trim($paragraph)) {
                                echo '<p>' . nl2br(trim($paragraph)) . '</p>';
                            }
                        }
                        ?>
                    </div>

                    <!-- Article Footer -->
                    <footer class="article-footer">
                        <div class="article-info">
                            <p><strong>ප්‍රකාශන දිනය:</strong> <?= format_date($article['published_at'], 'Y ජූලි d, H:i') ?></p>
                            <p><strong>අවසන් වරට යාවත්කාලීන:</strong> <?= format_date($article['updated_at'], 'Y ජූලි d, H:i') ?></p>
                            <p><strong>ප්‍රවර්ගය:</strong> <a href="category_db.php?cat=<?= htmlspecialchars($article['category_slug']) ?>"><?= htmlspecialchars($article['category_name']) ?></a></p>
                        </div>
                    </footer>

                    <!-- Navigation -->
                    <div class="article-navigation">
                        <?php
                        // Get previous and next articles
                        try {
                            $prev_article = $db->getAdjacentArticle($article_id, 'prev');
                            $next_article = $db->getAdjacentArticle($article_id, 'next');
                        } catch (Exception $e) {
                            $prev_article = null;
                            $next_article = null;
                        }
                        ?>

                        <div class="nav-links">
                            <?php if ($prev_article): ?>
                                <a href="article_db.php?id=<?= $prev_article['id'] ?>" class="prev-article">
                                    <span class="nav-label">පෙර ප්‍රවෘත්තිය</span>
                                    <span class="nav-title"><?= htmlspecialchars($prev_article['title']) ?></span>
                                </a>
                            <?php endif; ?>

                            <?php if ($next_article): ?>
                                <a href="article_db.php?id=<?= $next_article['id'] ?>" class="next-article">
                                    <span class="nav-label">ඊළඟ ප්‍රවෘත්තිය</span>
                                    <span class="nav-title"><?= htmlspecialchars($next_article['title']) ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <!-- Related Articles -->
                    <?php if (!empty($related_articles)): ?>
                        <div class="widget">
                            <h3 class="widget-title">අදාළ ප්‍රවෘත්ති</h3>
                            <div class="related-articles">
                                <?php foreach ($related_articles as $related): ?>
                                    <article class="related-item">
                                        <img src="<?= htmlspecialchars($related['image_url'] ?? 'https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News') ?>"
                                             alt="<?= htmlspecialchars($related['title']) ?>">
                                        <div class="related-content">
                                            <h4><a href="article_db.php?id=<?= $related['id'] ?>"><?= htmlspecialchars($related['title']) ?></a></h4>
                                            <span class="date"><?= format_date($related['published_at'], 'ජූලි d') ?></span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Popular News -->
                    <div class="widget">
                        <h3 class="widget-title">ජනප්‍රිය ප්‍රවෘත්ති</h3>
                        <div class="popular-news">
                            <?php foreach ($popular_articles as $popular): ?>
                                <article class="popular-item">
                                    <img src="<?= htmlspecialchars($popular['image_url'] ?? 'https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News') ?>"
                                         alt="<?= htmlspecialchars($popular['title']) ?>">
                                    <div class="popular-content">
                                        <h4><a href="article_db.php?id=<?= $popular['id'] ?>"><?= htmlspecialchars($popular['title']) ?></a></h4>
                                        <span class="date"><?= format_date($popular['published_at'], 'ජූලි d') ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රවර්ග</h3>
                        <ul class="category-list">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="category_db.php?cat=<?= htmlspecialchars($category['slug']) ?>"
                                       <?= $category['id'] == $article['category_id'] ? 'class="current"' : '' ?>>
                                        <?= htmlspecialchars($category['name_sinhala']) ?>
                                        <span>(<?= $category['article_count'] ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Admin Links (if logged in) -->
                    <?php if (is_logged_in()): ?>
                        <div class="widget">
                            <h3 class="widget-title">Admin</h3>
                            <div class="admin-links">
                                <a href="admin-edit-article_db.php?id=<?= $article_id ?>" class="admin-link">Edit Article</a>
                                <a href="admin-dashboard_db.php" class="admin-link">Dashboard</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= SITE_TITLE ?></h3>
                    <p><?= SITE_TAGLINE ?>. විශ්වසනීය හා නිරවද්‍ය ප්‍රවෘත්ති ඔබ වෙත ගෙන එමු.</p>
                </div>
                <div class="footer-section">
                    <h4>ප්‍රවර්ග</h4>
                    <ul>
                        <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                            <li><a href="category_db.php?cat=<?= htmlspecialchars($category['slug']) ?>"><?= htmlspecialchars($category['name_sinhala']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>අප ගැන</h4>
                    <ul>
                        <li><a href="about.html">අප ගැන</a></li>
                        <li><a href="contact.html">අප සම්බන්ධ කරගන්න</a></li>
                        <li><a href="privacy.html">පෞද්ගලිකත්ව ප්‍රතිපත්තිය</a></li>
                        <li><a href="terms.html">භාවිත කිරීමේ නියම</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>අප අනුගමනය කරන්න</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Twitter</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">YouTube</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 <?= SITE_TITLE ?>. සියලුම හිමිකම් ඇවිරිණි.</p>
            </div>
        </div>
    </footer>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="index_db.php" class="bottom-nav-item">
            <span class="bottom-nav-icon">🏠</span>
            <span class="bottom-nav-label">මුල්</span>
        </a>
        <a href="category_db.php?cat=politics" class="bottom-nav-item">
            <span class="bottom-nav-icon">🏛️</span>
            <span class="bottom-nav-label">දේශපාලන</span>
        </a>
        <a href="category_db.php?cat=sports" class="bottom-nav-item">
            <span class="bottom-nav-icon">⚽</span>
            <span class="bottom-nav-label">ක්‍රීඩා</span>
        </a>
        <a href="search_db.php" class="bottom-nav-item">
            <span class="bottom-nav-icon">🔍</span>
            <span class="bottom-nav-label">සොයන්න</span>
        </a>
        <a href="about.html" class="bottom-nav-item">
            <span class="bottom-nav-icon">ℹ️</span>
            <span class="bottom-nav-label">අප ගැන</span>
        </a>
    </nav>

    <script src="assets/js/script.js"></script>
    <script>
        // Initialize reading progress for article page
        document.addEventListener('DOMContentLoaded', function() {
            initializeReadingProgress();
        });

        // Social sharing functions
        function shareToFacebook(title, url) {
            const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            openShareWindow(shareUrl, 'Facebook');
        }

        function shareToTwitter(title, url) {
            const text = `${title} - <?= SITE_TITLE ?>`;
            const shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
            openShareWindow(shareUrl, 'Twitter');
        }

        function shareToWhatsApp(title, url) {
            const text = `${title}\n\n${url}`;
            const shareUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;

            // Check if on mobile
            if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                window.open(shareUrl, '_blank');
            } else {
                openShareWindow(shareUrl, 'WhatsApp');
            }
        }

        function copyToClipboard(url) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('ලිංකය පිටපත් කරන ලදී!', 'success');
                }).catch(() => {
                    fallbackCopyToClipboard(url);
                });
            } else {
                fallbackCopyToClipboard(url);
            }
        }

        function fallbackCopyToClipboard(url) {
            const textArea = document.createElement('textarea');
            textArea.value = url;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                showNotification('ලිංකය පිටපත් කරන ලදී!', 'success');
            } catch (err) {
                showNotification('ලිංකය පිටපත් කිරීමට නොහැකි විය', 'error');
            }

            document.body.removeChild(textArea);
        }

        function openShareWindow(url, platform) {
            const width = 600;
            const height = 400;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;

            window.open(
                url,
                `share-${platform}`,
                `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
            );
        }
    </script>

    <style>
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 0;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #2c5aa0;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .article-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .article-content {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .article-header {
            margin-bottom: 2rem;
        }

        .article-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .article-meta .category {
            background: #2c5aa0;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .article-meta .date,
        .article-meta .author,
        .article-meta .views {
            color: #666;
        }

        .article-title {
            font-size: 2.2rem;
            line-height: 1.3;
            margin-bottom: 1rem;
            color: #333;
        }

        .article-summary {
            background: #f8f9fa;
            border-left: 4px solid #2c5aa0;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .article-share {
            padding: 1.5rem 0;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }

        .article-share > span {
            display: block;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #333;
        }

        .social-share-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .social-share-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 120px;
            justify-content: center;
        }

        .social-share-btn.facebook {
            background: #1877f2;
            color: white;
        }

        .social-share-btn.facebook:hover {
            background: #166fe5;
            transform: translateY(-2px);
        }

        .social-share-btn.twitter {
            background: #1da1f2;
            color: white;
        }

        .social-share-btn.twitter:hover {
            background: #1a94e0;
            transform: translateY(-2px);
        }

        .social-share-btn.whatsapp {
            background: #25d366;
            color: white;
        }

        .social-share-btn.whatsapp:hover {
            background: #22c55e;
            transform: translateY(-2px);
        }

        .social-share-btn.copy {
            background: #6c757d;
            color: white;
        }

        .social-share-btn.copy:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .social-share-btn.print {
            background: #28a745;
            color: white;
        }

        .social-share-btn.print:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .share-icon {
            font-size: 1.1rem;
        }

        .article-image {
            margin: 2rem 0;
        }

        .article-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }

        .article-body {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .article-body p {
            margin-bottom: 1.5rem;
        }

        .article-footer {
            border-top: 1px solid #e9ecef;
            padding-top: 1.5rem;
            margin-bottom: 2rem;
        }

        .article-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
        }

        .article-info p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #666;
        }

        .article-info a {
            color: #2c5aa0;
            text-decoration: none;
        }

        .article-info a:hover {
            text-decoration: underline;
        }

        .article-navigation {
            border-top: 1px solid #e9ecef;
            padding-top: 1.5rem;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
        }

        .prev-article,
        .next-article {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            transition: background 0.3s ease;
        }

        .prev-article:hover,
        .next-article:hover {
            background: #e9ecef;
        }

        .next-article {
            text-align: right;
        }

        .nav-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .nav-title {
            font-weight: 500;
            line-height: 1.3;
        }

        .article-sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .related-articles {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .related-item {
            display: flex;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .related-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .related-item img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .related-content h4 {
            font-size: 0.95rem;
            line-height: 1.3;
            margin-bottom: 0.25rem;
        }

        .related-content h4 a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }

        .related-content h4 a:hover {
            color: #2c5aa0;
        }

        .related-content .date {
            font-size: 0.8rem;
            color: #999;
        }

        .category-list li a.current {
            color: #2c5aa0;
            font-weight: 600;
        }

        .admin-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .admin-link {
            display: block;
            padding: 0.5rem 1rem;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .admin-link:hover {
            background: #1e3a72;
        }

        @media (max-width: 768px) {
            .article-layout {
                grid-template-columns: 1fr;
            }

            .article-content {
                padding: 1.5rem;
            }

            .article-title {
                font-size: 1.8rem;
            }

            .article-image img {
                height: 250px;
            }

            .article-body {
                font-size: 1rem;
            }

            .article-share {
                padding: 1rem 0;
            }

            .social-share-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .social-share-btn {
                min-width: auto;
                width: 100%;
            }

            .nav-links {
                flex-direction: column;
            }

            .next-article {
                text-align: left;
            }
        }
    </style>
</body>
</html>
