<?php
// Get search query from URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Sample search results (in real application, this would search database)
$search_results = [];
if ($search_query) {
    // Simulate search results
    $all_articles = [
        [
            'id' => 1,
            'title' => 'ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය',
            'summary' => 'ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී.',
            'category' => 'දේශපාලන',
            'date' => '2025 ජූලි 13',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Politics'
        ],
        [
            'id' => 2,
            'title' => 'ක්‍රිකට් ලෝක කුසලානයේ ශ්‍රී ලංකා කණ්ඩායම',
            'summary' => 'ශ්‍රී ලංකා ක්‍රිකට් කණ්ඩායම නැවත ලෝක කුසලානයට සූදානම් වෙමින් පවතී.',
            'category' => 'ක්‍රීඩා',
            'date' => '2025 ජූලි 13',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Cricket'
        ],
        [
            'id' => 3,
            'title' => 'නව තාක්ෂණික නවෝත්පාදන ආයතනයක් පිහිටුවීම',
            'summary' => 'ශ්‍රී ලංකාවේ තාක්ෂණික ක්ෂේත්‍රය දියුණු කිරීම සඳහා නව ආයතනයක් පිහිටුවීමට කටයුතු ආරම්භ වී ඇත.',
            'category' => 'තාක්ෂණය',
            'date' => '2025 ජූලි 12',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Technology'
        ]
    ];

    // Simple search simulation
    $search_results = array_filter($all_articles, function($article) use ($search_query) {
        return stripos($article['title'], $search_query) !== false ||
               stripos($article['summary'], $search_query) !== false;
    });
}

$results_count = count($search_results);
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $search_query ? 'සොයුම් ප්‍රතිඵල: ' . htmlspecialchars($search_query) : 'සොයන්න' ?> | සිංහල ප්‍රවෘත්ති</title>
    <meta name="description" content="සිංහල ප්‍රවෘත්ති වෙබ් අඩවියේ සොයුම් ප්‍රතිඵල">
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
                    <h1><a href="index.php" style="text-decoration: none; color: inherit;">සිංහල ප්‍රවෘත්ති</a></h1>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php">මුල් පිටුව</a></li>
                        <li><a href="category.php?cat=politics">දේශපාලන</a></li>
                        <li><a href="category.php?cat=sports">ක්‍රීඩා</a></li>
                        <li><a href="category.php?cat=technology">තාක්ෂණය</a></li>
                        <li><a href="category.php?cat=business">ව්‍යාපාර</a></li>
                        <li><a href="category.php?cat=entertainment">විනෝදාස්වාදය</a></li>
                    </ul>
                </nav>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="ප්‍රවෘත්ති සොයන්න..." value="<?= htmlspecialchars($search_query) ?>">
                    <button type="button" id="searchBtn">සොයන්න</button>
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
                    <form method="GET" action="search.php" class="search-form">
                        <div class="search-inputs">
                            <input type="text" name="q" placeholder="මූල පද ඇතුළත් කරන්න..." value="<?= htmlspecialchars($search_query) ?>" class="search-input-main">
                            <select name="category" class="search-category">
                                <option value="">සියලුම ප්‍රවර්ග</option>
                                <option value="politics">දේශපාලන</option>
                                <option value="sports">ක්‍රීඩා</option>
                                <option value="technology">තාක්ෂණය</option>
                                <option value="business">ව්‍යාපාර</option>
                                <option value="entertainment">විනෝදාස්වාදය</option>
                                <option value="health">සෞඛ්‍ය</option>
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
                                        <img src="<?= $article['image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                                    </div>
                                    <div class="result-content">
                                        <div class="result-meta">
                                            <span class="result-category"><?= $article['category'] ?></span>
                                            <span class="result-date"><?= $article['date'] ?></span>
                                        </div>
                                        <h2 class="result-title">
                                            <a href="article.php?id=<?= $article['id'] ?>">
                                                <?= highlightSearchTerm($article['title'], $search_query) ?>
                                            </a>
                                        </h2>
                                        <p class="result-summary">
                                            <?= highlightSearchTerm($article['summary'], $search_query) ?>
                                        </p>
                                        <a href="article.php?id=<?= $article['id'] ?>" class="result-link">සම්පූර්ණයෙන් කියවන්න</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination for Search Results -->
                        <div class="search-pagination">
                            <a href="#" class="page-link disabled">&laquo; පෙර</a>
                            <a href="#" class="page-link active">1</a>
                            <a href="#" class="page-link">2</a>
                            <a href="#" class="page-link">ඊළඟ &raquo;</a>
                        </div>
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
                                </ul>
                            </div>

                            <div class="popular-searches">
                                <h3>ජනප්‍රිය සොයුම්:</h3>
                                <div class="popular-tags">
                                    <a href="search.php?q=ආර්ථිකය" class="popular-tag">ආර්ථිකය</a>
                                    <a href="search.php?q=ක්‍රිකට්" class="popular-tag">ක්‍රිකට්</a>
                                    <a href="search.php?q=තාක්ෂණය" class="popular-tag">තාක්ෂණය</a>
                                    <a href="search.php?q=දේශපාලන" class="popular-tag">දේශපාලන</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Default Search Page -->
                        <div class="search-default">
                            <div class="search-categories">
                                <h2>ප්‍රවර්ග අනුව සොයන්න</h2>
                                <div class="category-grid">
                                    <a href="category.php?cat=politics" class="category-card">
                                        <h3>දේශපාලන</h3>
                                        <p>25 ප්‍රවෘත්ති</p>
                                    </a>
                                    <a href="category.php?cat=sports" class="category-card">
                                        <h3>ක්‍රීඩා</h3>
                                        <p>18 ප්‍රවෘත්ති</p>
                                    </a>
                                    <a href="category.php?cat=technology" class="category-card">
                                        <h3>තාක්ෂණය</h3>
                                        <p>12 ප්‍රවෘත්ති</p>
                                    </a>
                                    <a href="category.php?cat=business" class="category-card">
                                        <h3>ව්‍යාපාර</h3>
                                        <p>15 ප්‍රවෘත්ති</p>
                                    </a>
                                    <a href="category.php?cat=entertainment" class="category-card">
                                        <h3>විනෝදාස්වාදය</h3>
                                        <p>8 ප්‍රවෘත්ති</p>
                                    </a>
                                    <a href="category.php?cat=health" class="category-card">
                                        <h3>සෞඛ්‍ය</h3>
                                        <p>10 ප්‍රවෘත්ති</p>
                                    </a>
                                </div>
                            </div>

                            <div class="trending-searches">
                                <h2>ජනප්‍රිය සොයුම්</h2>
                                <div class="trending-list">
                                    <a href="search.php?q=ආර්ථිකය">ආර්ථිකය</a>
                                    <a href="search.php?q=ක්‍රිකට්">ක්‍රිකට්</a>
                                    <a href="search.php?q=ජනාධිපතිවරණය">ජනාධිපතිවරණය</a>
                                    <a href="search.php?q=තාක්ෂණය">තාක්ෂණය</a>
                                    <a href="search.php?q=සෞඛ්‍ය">සෞඛ්‍ය</a>
                                </div>
                            </div>
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
                    <h3>සිංහල ප්‍රවෘත්ති</h3>
                    <p>ශ්‍රී ලංකාවේ ප්‍රධාන ප්‍රවෘත්ති වෙබ් අඩවිය. විශ්වසනීය හා නිරවද්‍ය ප්‍රවෘත්ති ඔබ වෙත ගෙන එමු.</p>
                </div>
                <div class="footer-section">
                    <h4>ප්‍රවර්ග</h4>
                    <ul>
                        <li><a href="category.php?cat=politics">දේශපාලන</a></li>
                        <li><a href="category.php?cat=sports">ක්‍රීඩා</a></li>
                        <li><a href="category.php?cat=technology">තාක්ෂණය</a></li>
                        <li><a href="category.php?cat=business">ව්‍යාපාර</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>අප ගැන</h4>
                    <ul>
                        <li><a href="about.php">අප ගැන</a></li>
                        <li><a href="contact.php">අප සම්බන්ධ කරගන්න</a></li>
                        <li><a href="privacy.php">පෞද්ගලිකත්ව ප්‍රතිපත්තිය</a></li>
                        <li><a href="terms.php">භාවිත කිරීමේ නියම</a></li>
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
                <p>&copy; 2025 සිංහල ප්‍රවෘත්ති. සියලුම හිමිකම් ඇවිරිණි.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        // Override search functionality for search page
        function performSearch() {
            const searchInput = document.getElementById('searchInput');
            const query = searchInput.value.trim();

            if (query) {
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
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
        }

        .result-category {
            background: #2c5aa0;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .result-date {
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

        .search-pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
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

        .trending-list a {
            background: #f8f9fa;
            color: #2c5aa0;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .trending-list a:hover {
            background: #2c5aa0;
            color: white;
        }

        .highlight {
            background-color: #ffeb3b;
            font-weight: bold;
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
        }
    </style>
</body>
</html>

<?php
// Function to highlight search terms in results
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
