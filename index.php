<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>සිංහල ප්‍රවෘත්ති | Sinhala News</title>
    <meta name="description" content="ශ්‍රී ලංකාවේ ප්‍රධාන ප්‍රවෘත්ති වෙබ් අඩවිය">
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
                    <h1>සිංහල ප්‍රවෘත්ති</h1>
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
                    <div class="featured-article">
                        <img src="https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Featured+News" alt="ප්‍රධාන ප්‍රවෘත්තිය">
                        <div class="featured-content">
                            <div class="article-meta">
                                <span class="category">දේශපාලන</span>
                                <span class="date">2025 ජූලි 13</span>
                            </div>
                            <h3><a href="article.php?id=1">ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය</a></h3>
                            <p>ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී. මෙම ප්‍රතිසංස්කරණ මගින් රටේ ආර්ථික ස්ථාවරත්වය වැඩි දියුණු කිරීමට අපේක්ෂා කෙරේ.</p>
                            <a href="article.php?id=1" class="read-more">සම්පූර්ණයෙන් කියවන්න</a>
                        </div>
                    </div>
                </section>

                <!-- Latest News Grid -->
                <section class="latest-news">
                    <h2 class="section-title">නවතම ප්‍රවෘත්ති</h2>
                    <div class="news-grid">
                        <?php
                        // Sample news data (in real application, this would come from database)
                        $news = [
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
                            ],
                            [
                                'id' => 4,
                                'title' => 'නව ව්‍යාපාරික ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීම',
                                'summary' => 'රටේ ව්‍යාපාරික ක්ෂේත්‍රය සදහා නව ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීමට අදාළ අධිකාරීන් කටයුතු කරමින් සිටිති.',
                                'category' => 'ව්‍යාපාර',
                                'date' => '2025 ජූලි 12',
                                'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Business'
                            ],
                            [
                                'id' => 5,
                                'title' => 'කලාකරුවන්ගේ නව සංගීත ප්‍රසංගයක්',
                                'summary' => 'ශ්‍රී ලංකාවේ ප්‍රසිද්ධ කලාකරුවන් කිහිප දෙනෙකු සහභාගි වන නව සංගීත ප්‍රසංගයක් සංවිධානය කෙරේ.',
                                'category' => 'විනෝදාස්වාදය',
                                'date' => '2025 ජූලි 11',
                                'image' => 'https://via.placeholder.com/400x250/f8f9fa/6c757d?text=Entertainment'
                            ]
                        ];

                        foreach ($news as $article): ?>
                            <article class="news-card">
                                <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>">
                                <div class="news-content">
                                    <div class="article-meta">
                                        <span class="category"><?= $article['category'] ?></span>
                                        <span class="date"><?= $article['date'] ?></span>
                                    </div>
                                    <h3><a href="article.php?id=<?= $article['id'] ?>"><?= $article['title'] ?></a></h3>
                                    <p><?= $article['summary'] ?></p>
                                    <a href="article.php?id=<?= $article['id'] ?>" class="read-more">සම්පූර්ණයෙන් කියවන්න</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
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
                            <article class="popular-item">
                                <img src="https://via.placeholder.com/80x60/f8f9fa/6c757d?text=News" alt="ප්‍රවෘත්තිය">
                                <div class="popular-content">
                                    <h4><a href="article.php?id=8">අධ්‍යාපන ක්ෂේත්‍රයේ නව ප්‍රතිසංස්කරණ</a></h4>
                                    <span class="date">ජූලි 08</span>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="widget">
                        <h3 class="widget-title">ප්‍රවර්ග</h3>
                        <ul class="category-list">
                            <li><a href="category.php?cat=politics">දේශපාලන <span>(25)</span></a></li>
                            <li><a href="category.php?cat=sports">ක්‍රීඩා <span>(18)</span></a></li>
                            <li><a href="category.php?cat=technology">තාක්ෂණය <span>(12)</span></a></li>
                            <li><a href="category.php?cat=business">ව්‍යාපාර <span>(15)</span></a></li>
                            <li><a href="category.php?cat=entertainment">විනෝදාස්වාදය <span>(8)</span></a></li>
                            <li><a href="category.php?cat=health">සෞඛ්‍ය <span>(10)</span></a></li>
                        </ul>
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
</body>
</html>
