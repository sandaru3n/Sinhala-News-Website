<?php
require_once 'includes/config.php';

// Get category from URL
$category_slug = sanitize_input($_GET['cat'] ?? '');

if (empty($category_slug)) {
    redirect('index.php');
}

try {
    $db = new Database();

    // Get category info
    $category = $db->getCategoryBySlug($category_slug);

    if (!$category) {
        // Category not found, redirect to homepage
        redirect('index.php');
    }

    // Get pagination parameters
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = ARTICLES_PER_PAGE;

    // Get articles for this category
    $articles = $db->getArticlesByCategory($category_slug, $page, $limit);

    // Get all categories for sidebar
    $all_categories = $db->getCategories();

    // Get popular articles
    $popular_articles = $db->getPopularArticles(3);

} catch (Exception $e) {
    error_log("Category page error: " . $e->getMessage());
    $category = null;
    $articles = [];
    $all_categories = [];
    $popular_articles = [];
}

?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category ? htmlspecialchars($category['name_sinhala']) . ' ප්‍රවෘත්ති' : 'ප්‍රවර්ගය' ?> | <?= SITE_TITLE ?></title>
    <meta name="description" content="<?= $category ? htmlspecialchars($category['name_sinhala']) . ' ප්‍රවර්ගයේ නවතම සිංහල ප්‍රවෘත්ති' : 'ප්‍රවර්ගය' ?>">
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
                        <li><a href="index.php">මුල් පිටුව</a></li>
                        <li><a href="category.php?cat=politics" <?= $category_slug === 'politics' ? 'class="active"' : '' ?>>දේශපාලන</a></li>
                        <li><a href="category.php?cat=sports" <?= $category_slug === 'sports' ? 'class="active"' : '' ?>>ක්‍රීඩා</a></li>
                        <li><a href="category.php?cat=technology" <?= $category_slug === 'technology' ? 'class="active"' : '' ?>>තාක්ෂණය</a></li>
                        <li><a href="category.php?cat=business" <?= $category_slug === 'business' ? 'class="active"' : '' ?>>ව්‍යාපාර</a></li>
                        <li><a href="category.php?cat=entertainment" <?= $category_slug === 'entertainment' ? 'class="active"' : '' ?>>විනෝදාස්වාදය</a></li>
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

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="index.php">මුල් පිටුව</a> &gt;
            <span><?= $category ? htmlspecialchars($category['name_sinhala']) : 'ප්‍රවර්ගය' ?> ප්‍රවෘත්ති</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="category-header">
                <h1 class="category-title"><?= $category ? htmlspecialchars($category['name_sinhala']) : 'ප්‍රවර්ගය' ?> ප්‍රවෘත්ති</h1>
                <p class="category-description">
                    <?php if ($category): ?>
                        <?= htmlspecialchars($category['description'] ?? '') ?>
                        <span class="article-count">(<?= $category['article_count'] ?> ප්‍රවෘත්ති)</span>
                    <?php else: ?>
                        ප්‍රවර්ගය සොයා ගත නොහැක
                    <?php endif; ?>
                </p>
            </div>

            <div class="content-grid">
                <!-- Articles List -->
                <section class="articles-list">
                    <?php if (!empty($articles)): ?>
                        <div class="category-filters">
                            <label for="sortBy">පෙරළන්න:</label>
                            <select id="sortBy" onchange="sortArticles(this.value)">
                                <option value="newest">නවතම පළමුව</option>
                                <option value="oldest">පැරණිතම පළමුව</option>
                                <option value="popular">ජනප්‍රිය පළමුව</option>
                            </select>
                        </div>

                        <div class="articles-grid" id="articlesGrid">
                            <?php foreach ($articles as $article): ?>
                                <article class="category-article">
                                    <div class="article-image">
                                        <img src="<?= htmlspecialchars($article['image_url'] ?? 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=News') ?>"
                                             alt="<?= htmlspecialchars($article['title']) ?>">
                                        <div class="article-category"><?= htmlspecialchars($article['category_name']) ?></div>
                                    </div>
                                    <div class="article-content">
                                        <div class="article-meta">
                                            <span class="date"><?= format_date($article['published_at'], 'Y ජූලි d') ?></span>
                                            <span class="author">කතුර: <?= htmlspecialchars($article['author_name']) ?></span>
                                        </div>
                                        <h2><a href="article.php?id=<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a></h2>
                                        <p><?= htmlspecialchars($article['summary']) ?></p>
                                        <a href="article.php?id=<?= $article['id'] ?>" class="read-more">සම්පූර්ණයෙන් කියවන්න</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?cat=<?= urlencode($category_slug) ?>&page=<?= $page - 1 ?>" class="page-link">&laquo; පෙර</a>
                            <?php else: ?>
                                <span class="page-link disabled">&laquo; පෙර</span>
                            <?php endif; ?>

                            <span class="page-link active"><?= $page ?></span>

                            <?php if (count($articles) == $limit): ?>
                                <a href="?cat=<?= urlencode($category_slug) ?>&page=<?= $page + 1 ?>" class="page-link">ඊළඟ &raquo;</a>
                            <?php else: ?>
                                <span class="page-link disabled">ඊළඟ &raquo;</span>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-articles">
                            <h2>ප්‍රවෘත්ති නොමැත</h2>
                            <p>මෙම ප්‍රවර්ගයේ ප්‍රවෘත්ති කිසිවක් නොමැත.</p>
                            <a href="index.php" class="back-home">මුල් පිටුවට යන්න</a>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Other Categories -->
                    <div class="widget">
                        <h3 class="widget-title">අනෙක් ප්‍රවර්ග</h3>
                        <ul class="other-categories">
                            <?php foreach ($all_categories as $cat): ?>
                                <?php if ($cat['slug'] !== $category_slug): ?>
                                    <li>
                                        <a href="category.php?cat=<?= htmlspecialchars($cat['slug']) ?>">
                                            <?= htmlspecialchars($cat['name_sinhala']) ?>
                                            <span>(<?= $cat['article_count'] ?>)</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

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
                                <p>ජනප්‍රිය ප්‍රවෘත්ති නොමැත</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රවෘත්ති ලැබීම</h3>
                        <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                            <p>නවතම ප්‍රවෘත්ති ඔබගේ ඊමේල් වෙත ලබා ගන්න</p>
                            <input type="email" placeholder="ඔබගේ ඊමේල් ලිපිනය" required>
                            <button type="submit">ලියාපදිංචි වන්න</button>
                        </form>
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
                        <?php foreach (array_slice($all_categories, 0, 4) as $cat): ?>
                            <li><a href="category.php?cat=<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name_sinhala']) ?></a></li>
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
        <a href="index.php" class="bottom-nav-item">
            <span class="bottom-nav-icon">🏠</span>
            <span class="bottom-nav-label">මුල්</span>
        </a>
        <a href="category.php?cat=politics" class="bottom-nav-item <?= $category_slug === 'politics' ? 'active' : '' ?>">
            <span class="bottom-nav-icon">🏛️</span>
            <span class="bottom-nav-label">දේශපාලන</span>
        </a>
        <a href="category.php?cat=sports" class="bottom-nav-item <?= $category_slug === 'sports' ? 'active' : '' ?>">
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
    <script>
        function sortArticles(sortBy) {
            // In a real application, this would make an AJAX call to sort articles
            console.log('Sorting by:', sortBy);
            showNotification('ප්‍රවෘත්ති ' + (sortBy === 'newest' ? 'නවතම පරිදි' : sortBy === 'oldest' ? 'පැරණිතම පරිදි' : 'ජනප්‍රියතාව පරිදි') + ' පෙරළන ලදී');
        }

        function subscribeNewsletter(event) {
            event.preventDefault();
            const email = event.target.querySelector('input[type="email"]').value;
            // In a real application, this would save to database
            showNotification('ඔබගේ ඊමේල් ලිපිනය (' + email + ') සාර්ථකව ලියාපදිංචි කරන ලදී!', 'success');
            event.target.reset();
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

        .category-header {
            text-align: center;
            padding: 2rem 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .category-title {
            font-size: 2.5rem;
            color: #2c5aa0;
            margin-bottom: 1rem;
        }

        .category-description {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .article-count {
            color: #2c5aa0;
            font-weight: 500;
        }

        .category-filters {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .category-filters select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .category-article {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category-article:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .category-article .article-image {
            position: relative;
        }

        .category-article .article-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .category-article .article-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #2c5aa0;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .category-article .article-content {
            padding: 1.5rem;
        }

        .category-article h2 {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .category-article h2 a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }

        .category-article h2 a:hover {
            color: #2c5aa0;
        }

        .category-article p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .category-article .article-meta {
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
            color: #666;
        }

        .category-article .article-meta .date,
        .category-article .article-meta .author {
            margin-right: 1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .page-link {
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #2c5aa0;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .page-link:hover:not(.disabled) {
            background: #2c5aa0;
            color: white;
        }

        .page-link.active {
            background: #2c5aa0;
            color: white;
            border-color: #2c5aa0;
        }

        .page-link.disabled {
            color: #6c757d;
            cursor: not-allowed;
        }

        .no-articles {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-articles h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .no-articles p {
            color: #666;
            margin-bottom: 2rem;
        }

        .back-home {
            display: inline-block;
            background: #2c5aa0;
            color: white;
            padding: 1rem 2rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .back-home:hover {
            background: #1e3a72;
        }

        .other-categories {
            list-style: none;
        }

        .other-categories li {
            border-bottom: 1px solid #e9ecef;
        }

        .other-categories li:last-child {
            border-bottom: none;
        }

        .other-categories li a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }

        .other-categories li a:hover {
            color: #2c5aa0;
        }

        .other-categories li a span {
            background-color: #e9ecef;
            color: #666;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .newsletter-form {
            text-align: center;
        }

        .newsletter-form p {
            margin-bottom: 1rem;
            color: #666;
        }

        .newsletter-form input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-family: inherit;
        }

        .newsletter-form button {
            width: 100%;
            padding: 0.75rem;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .newsletter-form button:hover {
            background: #1e3a72;
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
            .category-title {
                font-size: 2rem;
            }

            .articles-grid {
                grid-template-columns: 1fr;
            }

            .category-filters {
                flex-direction: column;
                align-items: flex-start;
            }

            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html>
