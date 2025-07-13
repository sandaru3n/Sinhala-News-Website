<?php
require_once 'includes/config.php';

// Get search query from URL
$search_query = sanitize_input($_GET['q'] ?? '');
$category_filter = sanitize_input($_GET['category'] ?? '');

try {
    $db = new Database();

    // Initialize variables
    $search_results = [];
    $results_count = 0;

    // Perform search if query is provided
    if (!empty($search_query)) {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = ARTICLES_PER_PAGE;

        // Search articles
        $search_results = $db->searchArticles($search_query, $page, $limit);
        $results_count = count($search_results);

        // Filter by category if specified
        if (!empty($category_filter) && !empty($search_results)) {
            $search_results = array_filter($search_results, function($article) use ($category_filter) {
                return $article['category_name'] === $category_filter;
            });
            $results_count = count($search_results);
        }
    }

    // Get categories for search filter
    $categories = $db->getCategories();

    // Get popular articles for suggestions
    $popular_articles = $db->getPopularArticles(5);

} catch (Exception $e) {
    error_log("Search page error: " . $e->getMessage());
    $search_results = [];
    $results_count = 0;
    $categories = [];
    $popular_articles = [];
}

/**
 * Highlight search terms in results
 */
function highlightSearchTerm($text, $term) {
    if (empty($term)) return htmlspecialchars($text);

    $highlighted = preg_replace(
        '/(' . preg_quote($term, '/') . ')/ui',
        '<span class="highlight">$1</span>',
        htmlspecialchars($text)
    );
    return $highlighted;
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $search_query ? 'සොයුම් ප්‍රතිඵල: ' . htmlspecialchars($search_query) : 'සොයන්න' ?> | <?= SITE_TITLE ?></title>
    <meta name="description" content="<?= SITE_TITLE ?> වෙබ් අඩවියේ සොයුම් ප්‍රතිඵල">
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
                        <input type="text" name="q" id="searchInput" placeholder="ප්‍රවෘත්ති සොයන්න..."
                               value="<?= htmlspecialchars($search_query) ?>" required>
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
            <span>සොයුම් ප්‍රතිඵල</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="search-page">
                <!-- Search Header -->
                <div class="search-header">
                    <?php if ($search_query): ?>
                        <h1 class="search-title">සොයුම් ප්‍රතිඵල</h1>
                        <p class="search-info">
                            "<strong><?= htmlspecialchars($search_query) ?></strong>" සඳහා <?= $results_count ?> ප්‍රතිඵල හමු විය
                        </p>
                    <?php else: ?>
                        <h1 class="search-title">ප්‍රවෘත්ති සොයන්න</h1>
                        <p class="search-info">ඔබට අවශ්‍ය ප්‍රවෘත්ති සොයා ගන්න</p>
                    <?php endif; ?>
                </div>

                <!-- Advanced Search Form -->
                <div class="advanced-search">
                    <form method="GET" action="search_db.php" class="search-form">
                        <div class="search-inputs">
                            <input type="text" name="q" placeholder="මූල පද ඇතුළත් කරන්න..."
                                   value="<?= htmlspecialchars($search_query) ?>" class="search-input-main">
                            <select name="category" class="search-category">
                                <option value="">සියලුම ප්‍රවර්ග</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['name_sinhala']) ?>"
                                            <?= $category_filter === $category['name_sinhala'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name_sinhala']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="search-submit">සොයන්න</button>
                        </div>
                    </form>
                </div>

                <div class="search-content">
                    <?php if ($search_query && $results_count > 0): ?>
                        <!-- Search Results -->
                        <div class="search-results">
                            <?php foreach ($search_results as $article): ?>
                                <article class="search-result-item">
                                    <div class="result-image">
                                        <img src="<?= htmlspecialchars($article['image_url'] ?? 'https://via.placeholder.com/200x120/f8f9fa/6c757d?text=News') ?>"
                                             alt="<?= htmlspecialchars($article['title']) ?>">
                                    </div>
                                    <div class="result-content">
                                        <div class="result-meta">
                                            <span class="result-category"><?= htmlspecialchars($article['category_name']) ?></span>
                                            <span class="result-date"><?= format_date($article['published_at'], 'Y ජූලි d') ?></span>
                                            <span class="result-author">කතුර: <?= htmlspecialchars($article['author_name']) ?></span>
                                        </div>
                                        <h2 class="result-title">
                                            <a href="article_db.php?id=<?= $article['id'] ?>">
                                                <?= highlightSearchTerm($article['title'], $search_query) ?>
                                            </a>
                                        </h2>
                                        <p class="result-summary">
                                            <?= highlightSearchTerm($article['summary'], $search_query) ?>
                                        </p>
                                        <div class="result-actions">
                                            <a href="article_db.php?id=<?= $article['id'] ?>" class="result-link">සම්පූර්ණයෙන් කියවන්න</a>
                                            <span class="result-views"><?= $article['views'] ?> බැලීම්</span>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination for Search Results -->
                        <?php if ($results_count >= ARTICLES_PER_PAGE): ?>
                            <div class="search-pagination">
                                <?php
                                $current_page = $_GET['page'] ?? 1;
                                $base_url = "search_db.php?q=" . urlencode($search_query);
                                if ($category_filter) {
                                    $base_url .= "&category=" . urlencode($category_filter);
                                }
                                ?>

                                <?php if ($current_page > 1): ?>
                                    <a href="<?= $base_url ?>&page=<?= $current_page - 1 ?>" class="page-link">&laquo; පෙර</a>
                                <?php else: ?>
                                    <span class="page-link disabled">&laquo; පෙර</span>
                                <?php endif; ?>

                                <span class="page-link active"><?= $current_page ?></span>

                                <?php if ($results_count >= ARTICLES_PER_PAGE): ?>
                                    <a href="<?= $base_url ?>&page=<?= $current_page + 1 ?>" class="page-link">ඊළඟ &raquo;</a>
                                <?php else: ?>
                                    <span class="page-link disabled">ඊළඟ &raquo;</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($search_query && $results_count === 0): ?>
                        <!-- No Results -->
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h2>ප්‍රතිඵල හමු නොවීය</h2>
                            <p>"<?= htmlspecialchars($search_query) ?>" සඳහා කිසිදු ප්‍රතිඵලයක් හමු නොවීය.</p>

                            <div class="search-suggestions">
                                <h3>සොයන්න උත්සාහ කරන්න:</h3>
                                <ul>
                                    <li>වෙනස් මූල පද භාවිතා කරන්න</li>
                                    <li>වඩා සාමාන්‍ය පද භාවිතා කරන්න</li>
                                    <li>වත්මන් ප්‍රවෘත්ති පරීක්ෂා කරන්න</li>
                                    <li>අකුරු වැරදි නොමැතිද පරීක්ෂා කරන්න</li>
                                </ul>
                            </div>

                            <div class="popular-searches">
                                <h3>ජනප්‍රිය සොයුම්:</h3>
                                <div class="popular-tags">
                                    <a href="search_db.php?q=ආර්ථිකය" class="popular-tag">ආර්ථිකය</a>
                                    <a href="search_db.php?q=ක්‍රිකට්" class="popular-tag">ක්‍රිකට්</a>
                                    <a href="search_db.php?q=තාක්ෂණය" class="popular-tag">තාක්ෂණය</a>
                                    <a href="search_db.php?q=දේශපාලන" class="popular-tag">දේශපාලන</a>
                                    <a href="search_db.php?q=රජය" class="popular-tag">රජය</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Default Search Page -->
                        <div class="search-default">
                            <div class="search-categories">
                                <h2>ප්‍රවර්ග අනුව සොයන්න</h2>
                                <div class="category-grid">
                                    <?php foreach ($categories as $category): ?>
                                        <a href="category_db.php?cat=<?= htmlspecialchars($category['slug']) ?>" class="category-card">
                                            <h3><?= htmlspecialchars($category['name_sinhala']) ?></h3>
                                            <p><?= $category['article_count'] ?> ප්‍රවෘත්ති</p>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if (!empty($popular_articles)): ?>
                                <div class="trending-searches">
                                    <h2>ජනප්‍රිය ප්‍රවෘත්ති</h2>
                                    <div class="trending-list">
                                        <?php foreach ($popular_articles as $article): ?>
                                            <a href="article_db.php?id=<?= $article['id'] ?>" class="trending-item">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
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
        <a href="search_db.php" class="bottom-nav-item active">
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

        .search-page {
            padding: 2rem 0;
        }

        .search-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .search-title {
            font-size: 2.5rem;
            color: #2c5aa0;
            margin-bottom: 0.5rem;
        }

        .search-info {
            font-size: 1.1rem;
            color: #666;
        }

        .advanced-search {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .search-inputs {
            display: flex;
            gap: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-input-main {
            flex: 2;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }

        .search-category {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }

        .search-submit {
            padding: 0.75rem 2rem;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .search-submit:hover {
            background: #1e3a72;
        }

        .search-results {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .search-result-item {
            display: flex;
            gap: 1.5rem;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .result-image {
            flex-shrink: 0;
        }

        .result-image img {
            width: 200px;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
        }

        .result-content {
            flex: 1;
        }

        .result-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .result-category {
            background: #2c5aa0;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .result-date,
        .result-author {
            color: #666;
        }

        .result-title {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .result-title a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }

        .result-title a:hover {
            color: #2c5aa0;
        }

        .result-summary {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .result-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-link {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .result-link:hover {
            color: #1e3a72;
            text-decoration: underline;
        }

        .result-views {
            color: #999;
            font-size: 0.9rem;
        }

        .search-pagination {
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

        .no-results {
            text-align: center;
            padding: 3rem 0;
        }

        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .no-results h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .search-suggestions,
        .popular-searches {
            margin-top: 2rem;
            text-align: left;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-suggestions h3,
        .popular-searches h3 {
            color: #2c5aa0;
            margin-bottom: 1rem;
        }

        .search-suggestions ul {
            list-style: none;
            padding-left: 0;
        }

        .search-suggestions li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .popular-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .popular-tag {
            background: #e9ecef;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .popular-tag:hover {
            background: #2c5aa0;
            color: white;
        }

        .search-default {
            padding: 2rem 0;
        }

        .search-categories,
        .trending-searches {
            margin-bottom: 3rem;
        }

        .search-categories h2,
        .trending-searches h2 {
            text-align: center;
            color: #2c5aa0;
            margin-bottom: 2rem;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .category-card h3 {
            color: #2c5aa0;
            margin-bottom: 0.5rem;
        }

        .category-card p {
            color: #666;
        }

        .trending-list {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .trending-item {
            background: #f8f9fa;
            color: #2c5aa0;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .trending-item:hover {
            background: #2c5aa0;
            color: white;
        }

        .highlight {
            background-color: #ffeb3b;
            font-weight: bold;
            padding: 0.1rem 0.2rem;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .search-title {
                font-size: 2rem;
            }

            .search-inputs {
                flex-direction: column;
            }

            .search-result-item {
                flex-direction: column;
            }

            .result-image img {
                width: 100%;
                height: 200px;
            }

            .category-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .trending-list {
                flex-direction: column;
                align-items: center;
            }

            .result-actions {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
        }
    </style>
</body>
</html>
