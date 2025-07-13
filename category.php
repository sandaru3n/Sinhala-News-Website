<?php
// Get category from URL
$category = isset($_GET['cat']) ? $_GET['cat'] : 'politics';

// Category mappings
$categories = [
    'politics' => 'දේශපාලන',
    'sports' => 'ක්‍රීඩා',
    'technology' => 'තාක්ෂණය',
    'business' => 'ව්‍යාපාර',
    'entertainment' => 'විනෝදාස්වාදය',
    'health' => 'සෞඛ්‍ය'
];

$category_name = isset($categories[$category]) ? $categories[$category] : 'දේශපාලන';

// Sample category articles (in real application, this would come from database)
$category_articles = [
    'politics' => [
        [
            'id' => 1,
            'title' => 'ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය',
            'summary' => 'ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී.',
            'date' => '2025 ජූලි 13',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Politics+1'
        ],
        [
            'id' => 9,
            'title' => 'ජනාධිපතිවරණය සම්බන්ධ නව ප්‍රකාශනයක්',
            'summary' => 'ඉදිරි ජනාධිපතිවරණය සම්බන්ධයෙන් මැතිවරණ කොමිසමේ නව ප්‍රකාශනයක් නිකුත් කර ඇත.',
            'date' => '2025 ජූලි 12',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Politics+2'
        ],
        [
            'id' => 10,
            'title' => 'පාර්ලිමේන්තුවේ නව සභාගතයක්',
            'summary' => 'පාර්ලිමේන්තුව අද දින සභාගත වන අතර ජාතික වැදගත්කම සහිත කරුණු සාකච්ඡා කිරීමට නියමිතයි.',
            'date' => '2025 ජූලි 11',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Politics+3'
        ]
    ],
    'sports' => [
        [
            'id' => 2,
            'title' => 'ක්‍රිකට් ලෝක කුසලානයේ ශ්‍රී ලංකා කණ්ඩායම',
            'summary' => 'ශ්‍රී ලංකා ක්‍රිකට් කණ්ඩායම නැවත ලෝක කුසලානයට සූදානම් වෙමින් පවතී.',
            'date' => '2025 ජූලි 13',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Cricket'
        ],
        [
            'id' => 11,
            'title' => 'පාපන්දු ලීගයේ අලුත් වාරය',
            'summary' => 'ශ්‍රී ලංකා පාපන්දු ලීගයේ නව වාරය ආරම්භ වීමට නියමිතය.',
            'date' => '2025 ජූලි 12',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Football'
        ],
        [
            'id' => 12,
            'title' => 'ජාතික ක්‍රීඩා උත්සවය 2025',
            'summary' => 'ජාතික ක්‍රීඩා උත්සවය 2025 සඳහා සූදානම් වැඩ ආරම්භ වී ඇත.',
            'date' => '2025 ජූලි 10',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Sports+Festival'
        ]
    ],
    'technology' => [
        [
            'id' => 3,
            'title' => 'නව තාක්ෂණික නවෝත්පාදන ආයතනයක් පිහිටුවීම',
            'summary' => 'ශ්‍රී ලංකාවේ තාක්ෂණික ක්ෂේත්‍රය දියුණු කිරීම සඳහා නව ආයතනයක් පිහිටුවීමට කටයුතු ආරම්භ වී ඇත.',
            'date' => '2025 ජූලි 12',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Technology'
        ]
    ],
    'business' => [
        [
            'id' => 4,
            'title' => 'නව ව්‍යාපාරික ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීම',
            'summary' => 'රටේ ව්‍යාපාරික ක්ෂේත්‍රය සදහා නව ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීමට අදාළ අධිකාරීන් කටයුතු කරමින් සිටිති.',
            'date' => '2025 ජූලි 12',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Business'
        ]
    ],
    'entertainment' => [
        [
            'id' => 5,
            'title' => 'කලාකරුවන්ගේ නව සංගීත ප්‍රසංගයක්',
            'summary' => 'ශ්‍රී ලංකාවේ ප්‍රසිද්ධ කලාකරුවන් කිහිප දෙනෙකු සහභාගි වන නව සංගීත ප්‍රසංගයක් සංවිධානය කෙරේ.',
            'date' => '2025 ජූලි 11',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Entertainment'
        ]
    ],
    'health' => [
        [
            'id' => 13,
            'title' => 'සෞඛ්‍ය ක්ෂේත්‍රයේ නව දියුණුවක්',
            'summary' => 'රටේ සෞඛ්‍ය ක්ෂේත්‍රය වැඩි දියුණු කිරීම සඳහා නව වැඩසටහනක් ආරම්භ කර ඇත.',
            'date' => '2025 ජූලි 10',
            'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Health'
        ]
    ]
];

$articles = isset($category_articles[$category]) ? $category_articles[$category] : $category_articles['politics'];
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category_name ?> ප්‍රවෘත්ති | සිංහල ප්‍රවෘත්ති</title>
    <meta name="description" content="<?= $category_name ?> ප්‍රවර්ගයේ නවතම සිංහල ප්‍රවෘත්ති">
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
                        <li><a href="category.php?cat=politics" <?= $category === 'politics' ? 'class="active"' : '' ?>>දේශපාලන</a></li>
                        <li><a href="category.php?cat=sports" <?= $category === 'sports' ? 'class="active"' : '' ?>>ක්‍රීඩා</a></li>
                        <li><a href="category.php?cat=technology" <?= $category === 'technology' ? 'class="active"' : '' ?>>තාක්ෂණය</a></li>
                        <li><a href="category.php?cat=business" <?= $category === 'business' ? 'class="active"' : '' ?>>ව්‍යාපාර</a></li>
                        <li><a href="category.php?cat=entertainment" <?= $category === 'entertainment' ? 'class="active"' : '' ?>>විනෝදාස්වාදය</a></li>
                    </ul>
                </nav>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="ප්‍රවෘත්ති සොයන්න...">
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
            <span><?= $category_name ?> ප්‍රවෘත්ති</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="category-header">
                <h1 class="category-title"><?= $category_name ?> ප්‍රවෘත්ති</h1>
                <p class="category-description">
                    <?php
                    $descriptions = [
                        'politics' => 'ශ්‍රී ලංකාවේ දේශපාලන ක්ෂේත්‍රයේ නවතම ප්‍රවර්තන සහ සිදුවීම්',
                        'sports' => 'ක්‍රීඩා ක්ෂේත්‍රයේ නවතම ප්‍රවෘත්ති සහ ක්‍රීඩා ප්‍රතිඵල',
                        'technology' => 'තාක්ෂණික දියුණුව සහ නවෝත්පාදන පිළිබඳ ප්‍රවෘත්ති',
                        'business' => 'ව්‍යාපාරික ක්ෂේත්‍රයේ නවතම ප්‍රවර්තන සහ ආර්ථික ප්‍රවෘත්ති',
                        'entertainment' => 'විනෝදාස්වාදය සහ සංස්කෘතික සිදුවීම් පිළිබඳ ප්‍රවෘත්ති',
                        'health' => 'සෞඛ්‍ය ක්ෂේත්‍රයේ නවතම ප්‍රවර්තන සහ සෞඛ්‍ය උපදෙස්'
                    ];
                    echo isset($descriptions[$category]) ? $descriptions[$category] : $descriptions['politics'];
                    ?>
                </p>
            </div>

            <div class="content-grid">
                <!-- Articles List -->
                <section class="articles-list">
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
                                    <img src="<?= $article['image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                                    <div class="article-category"><?= $category_name ?></div>
                                </div>
                                <div class="article-content">
                                    <div class="article-meta">
                                        <span class="date"><?= $article['date'] ?></span>
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
                        <a href="#" class="page-link disabled">&laquo; පෙර</a>
                        <a href="#" class="page-link active">1</a>
                        <a href="#" class="page-link">2</a>
                        <a href="#" class="page-link">3</a>
                        <a href="#" class="page-link">ඊළඟ &raquo;</a>
                    </div>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Other Categories -->
                    <div class="widget">
                        <h3 class="widget-title">අනෙක් ප්‍රවර්ග</h3>
                        <ul class="other-categories">
                            <?php foreach ($categories as $cat_key => $cat_name): ?>
                                <?php if ($cat_key !== $category): ?>
                                    <li><a href="category.php?cat=<?= $cat_key ?>"><?= $cat_name ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Popular News -->
                    <div class="widget">
                        <h3 class="widget-title">ජනප්‍රිය ප්‍රවෘත්ති</h3>
                        <div class="popular-news">
                            <article class="popular-item">
                                <img src="https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News" alt="ප්‍රවෘත්තිය">
                                <div class="popular-content">
                                    <h4><a href="article.php?id=6">ජනාධිපතිවරණය සම්බන්ධ නව ප්‍රකාශනයක්</a></h4>
                                    <span class="date">ජූලි 10</span>
                                </div>
                            </article>
                            <article class="popular-item">
                                <img src="https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News" alt="ප්‍රවෘත්තිය">
                                <div class="popular-content">
                                    <h4><a href="article.php?id=7">කාලගුණ විද්‍යා දෙපාර්තමේන්තුවේ අනතුරු ඇඟවීමක්</a></h4>
                                    <span class="date">ජූලි 09</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රවෘත්ති ලැබීම</h3>
                        <form class="newsletter-form">
                            <p>නවතම ප්‍රවෘත්ති ඔබගේ ඊමේල් වෙත ලබා ගන්න</p>
                            <input type="email" placeholder="ඔබගේ ඊමේල් ලිපිනය" required>
                            <button type="submit">ලියාපදිංචි වන්න</button>
                        </form>
                    </div>

                    <!-- Advertisement -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රචාරණ</h3>
                        <div class="ad-space" style="background: #f8f9fa; padding: 60px 20px; text-align: center; color: #6c757d; border-radius: 4px;">
                            ප්‍රචාරණ ස්ථානය
                        </div>
                    </div>
                </aside>
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
        function sortArticles(sortBy) {
            // In a real application, this would make an AJAX call to sort articles
            console.log('Sorting by:', sortBy);
            showNotification('ප්‍රවෘත්ති ' + (sortBy === 'newest' ? 'නවතම පරිදි' : sortBy === 'oldest' ? 'පැරණිතම පරිදි' : 'ජනප්‍රියතාව පරිදි') + ' පෙරළන ලදී');
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
            display: block;
            padding: 0.75rem 0;
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }

        .other-categories li a:hover {
            color: #2c5aa0;
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
