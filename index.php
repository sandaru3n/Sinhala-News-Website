<?php
require_once 'includes/config.php';

try {
    $db = new Database();

    // Get featured articles
    $featured_articles = $db->getFeaturedArticles(1);
    $featured_article = !empty($featured_articles) ? $featured_articles[0] : null;

    // Get latest articles (excluding featured)
    $latest_articles = $db->getPublishedArticles(1, 8);
    if ($featured_article) {
        $latest_articles = array_filter($latest_articles, function($article) use ($featured_article) {
            return $article['id'] != $featured_article['id'];
        });
        $latest_articles = array_slice($latest_articles, 0, 4);
    }

    // Get popular articles for sidebar
    $popular_articles = $db->getPopularArticles(3);

    // Get categories with article counts
    $categories = $db->getCategories();

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $featured_article = null;
    $latest_articles = [];
    $popular_articles = [];
    $categories = [];
}

?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?></title>
    <meta name="description" content="<?= SITE_TAGLINE ?>">
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
                    <h1><a href="index.php" style="text-decoration: none; color: inherit;"><?= SITE_TITLE ?></a></h1>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="active">මුල් පිටුව</a></li>
                        <li><a href="category.php?cat=politics">දේශපාලන</a></li>
                        <li><a href="category.php?cat=sports">ක්‍රීඩා</a></li>
                        <li><a href="category.php?cat=technology">තාක්ෂණය</a></li>
                        <li><a href="category.php?cat=business">ව්‍යාපාර</a></li>
                        <li><a href="category.php?cat=entertainment">විනෝදාස්වාදය</a></li>
                    </ul>
                </nav>
                <div class="search-box">
                    <form action="search.php" method="GET">
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

    <!-- Breaking News Ticker -->
    <div class="breaking-news">
        <div class="container">
            <span class="breaking-label">ප්‍රවෘත්ති:</span>
            <div class="breaking-content">
                <p>නවතම ප්‍රවෘත්තිය මෙහි දිස්වේ • ශ්‍රී ලංකා ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රකාශනයක් • තාක්ෂණ ක්ෂේත්‍රයේ නව දියුණුවක්</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="content-grid">
                <!-- Featured News -->
                <section class="featured-news">
                    <h2 class="section-title">ප්‍රධාන ප්‍රවෘත්ති</h2>
                    <?php if ($featured_article): ?>
                        <div class="featured-article">
                            <img src="<?= htmlspecialchars($featured_article['image_url']) ?>" alt="<?= htmlspecialchars($featured_article['title']) ?>">
                            <div class="featured-content">
                                <div class="article-meta">
                                    <span class="category"><?= htmlspecialchars($featured_article['category_name']) ?></span>
                                    <span class="date"><?= format_date($featured_article['published_at'], 'Y ජූලි d') ?></span>
                                </div>
                                <h3><a href="article.php?id=<?= $featured_article['id'] ?>"><?= htmlspecialchars($featured_article['title']) ?></a></h3>
                                <p><?= htmlspecialchars($featured_article['summary']) ?></p>
                                <a href="article.php?id=<?= $featured_article['id'] ?>" class="read-more">සම්පූර්ණයෙන් කියවන්න</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="featured-article">
                            <img src="https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Featured+News" alt="ප්‍රධාන ප්‍රවෘත්තිය">
                            <div class="featured-content">
                                <div class="article-meta">
                                    <span class="category">දේශපාලන</span>
                                    <span class="date">2025 ජූලි 13</span>
                                </div>
                                <h3><a href="#">ප්‍රධාන ප්‍රවෘත්තිය ලෝඩ් වෙමින්...</a></h3>
                                <p>ප්‍රධාන ප්‍රවෘත්ති අන්තර්ගතය ලෝඩ් වෙමින් පවතී.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Latest News Grid -->
                <section class="latest-news">
                    <h2 class="section-title">නවතම ප්‍රවෘත්ති</h2>
                    <div class="news-grid">
                        <?php if (!empty($latest_articles)): ?>
                            <?php foreach ($latest_articles as $article): ?>
                                <article class="news-card">
                                    <img src="<?= htmlspecialchars($article['image_url'] ?? 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=News') ?>"
                                         alt="<?= htmlspecialchars($article['title']) ?>">
                                    <div class="news-content">
                                        <div class="article-meta">
                                            <span class="category"><?= htmlspecialchars($article['category_name']) ?></span>
                                            <span class="date"><?= format_date($article['published_at'], 'Y ජූලි d') ?></span>
                                        </div>
                                        <h3><a href="article.php?id=<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a></h3>
                                        <p><?= htmlspecialchars($article['summary']) ?></p>
                                        <a href="article.php?id=<?= $article['id'] ?>" class="read-more">සම්පූර්ණයෙන් කියවන්න</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-articles">
                                <p>ප්‍රවෘත්ති ලෝඩ් වෙමින් පවතී...</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Popular News -->
                    <div class="widget">
                        <h3 class="widget-title">ජනප්‍රිය ප්‍රවෘත්ති</h3>
                        <div class="popular-news">
                            <?php if (!empty($popular_articles)): ?>
                                <?php foreach ($popular_articles as $article): ?>
                                    <article class="popular-item">
                                        <img src="<?= htmlspecialchars($article['image_url'] ?? 'https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News') ?>"
                                             alt="<?= htmlspecialchars($article['title']) ?>">
                                        <div class="popular-content">
                                            <h4><a href="article.php?id=<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a></h4>
                                            <span class="date"><?= format_date($article['published_at'], 'ජූලි d') ?></span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>ජනප්‍රිය ප්‍රවෘත්ති ලෝඩ් වෙමින්...</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රවර්ග</h3>
                        <ul class="category-list">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="category.php?cat=<?= htmlspecialchars($category['slug']) ?>">
                                            <?= htmlspecialchars($category['name_sinhala']) ?>
                                            <span>(<?= $category['article_count'] ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>ප්‍රවර්ග ලෝඩ් වෙමින්...</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Admin Link (if logged in) -->
                    <?php if (is_logged_in()): ?>
                        <div class="widget">
                            <h3 class="widget-title">Admin</h3>
                            <div class="admin-links">
                                <a href="admin-dashboard.php" class="admin-link">Admin Dashboard</a>
                                <a href="admin-add-article.php" class="admin-link">Add Article</a>
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
                            <li><a href="category.php?cat=<?= htmlspecialchars($category['slug']) ?>"><?= htmlspecialchars($category['name_sinhala']) ?></a></li>
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
        <a href="index.php" class="bottom-nav-item active">
            <span class="bottom-nav-icon">🏠</span>
            <span class="bottom-nav-label">මුල්</span>
        </a>
        <a href="category.php?cat=politics" class="bottom-nav-item">
            <span class="bottom-nav-icon">🏛️</span>
            <span class="bottom-nav-label">දේශපාලන</span>
        </a>
        <a href="category.php?cat=sports" class="bottom-nav-item">
            <span class="bottom-nav-icon">⚽</span>
            <span class="bottom-nav-label">ක්‍රීඩා</span>
        </a>
        <a href="search.php" class="bottom-nav-item">
            <span class="bottom-nav-icon">🔍</span>
            <span class="bottom-nav-label">සොයන්න</span>
        </a>
        <a href="about.html" class="bottom-nav-item">
            <span class="bottom-nav-icon">ℹ️</span>
            <span class="bottom-nav-label">අප ගැන</span>
        </a>
    </nav>

    <script src="assets/js/script.js"></script>

    <style>
        .no-articles {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            color: #666;
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
    </style>
</body>
</html>
