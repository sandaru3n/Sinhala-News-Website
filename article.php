<?php
// Get article ID from URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Sample article data (in real application, this would come from database)
$articles = [
    1 => [
        'title' => 'ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය',
        'content' => 'ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී. මෙම ප්‍රතිසංස්කරණ මගින් රටේ ආර්ථික ස්ථාවරත්වය වැඩි දියුණු කිරීමට අපේක්ෂා කෙරේ.

ආර්ථික ප්‍රතිපත්ති සම්බන්ධ රාජ්‍ය ප්‍රතිපත්ති අනුව, රටේ සම්පත් කළමනාකරණය වඩාත් කාර්යක්ෂම ආකාරයකට සිදු කිරීමට පියවර ගනු ඇත. මේ සම්බන්ධයෙන් ප්‍රධානම වශයෙන් රටේ කර්මාන්ත ක්ෂේත්‍රය දියුණු කිරීම, කෘෂිකර්ම ක්ෂේත්‍රයේ නවීකරණය සහ සේවා ක්ෂේත්‍රයේ පුළුල් කිරීම ඇතුළත් වේ.

ආර්ථික අමාත්‍යවරයාගේ ප්‍රකාශයකට අනුව, මෙම ප්‍රතිසංස්කරණ වැඩසටහන තුළින් අදාළ ක්ෂේත්‍රවල රැකියා අවස්ථා වැඩි කිරීමට හැකි වනු ඇත. ඊට අමතරව විදේශ ආයෝජන ආකර්ෂණය කර ගැනීම සහ අපනයන ආදායම වැඩි කිරීම මගින් රටේ ආර්ථික තත්ත්වය සාධාරණීකරණය කිරීමට අපේක්ෂා කෙරේ.

මෙම ප්‍රතිසංස්කරණ වැඩසටහන ක්‍රියාත්මක කිරීම සම්බන්ධයෙන් ජාත්‍යන්තර මූල්‍ය අරමුදල සහ ලෝක බැංකුව වැනි ආයතනවල සහයෝගය ලබා ගැනීමට රජය කටයුතු කරමින් සිටී.',
        'category' => 'දේශපාලන',
        'date' => '2025 ජූලි 13',
        'author' => 'ප්‍රවෘත්ති කණ්ඩායම',
        'image' => 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Economic+Reform',
        'tags' => ['ආර්ථිකය', 'ප්‍රතිසංස්කරණ', 'ආර්ථික ප්‍රතිපත්ති', 'රජය']
    ],
    2 => [
        'title' => 'ක්‍රිකට් ලෝක කුසලානයේ ශ්‍රී ලංකා කණ්ඩායම',
        'content' => 'ශ්‍රී ලංකා ක්‍රිකට් කණ්ඩායම නැවත ලෝක කුසලානයට සූදානම් වෙමින් පවතී. පසුගිය ක්‍රීඩා වාර කිහිපයේ දී ලැබූ අත්දැකීම් මත කණ්ඩායම තම ක්‍රීඩා ක්‍රමය වැඩි දියුණු කර ගෙන ඇත.

අලුතින් කණ්ඩායමට එක් වූ තරුණ ක්‍රීඩකයන්ගේ දක්ෂතා මගින් කණ්ඩායමේ ශක්තිය වැඩි කර ගැනීමට හැකි වී ඇත. ජාතික ක්‍රිකට් කණ්ඩායමේ නායකයාගේ මඟ පෙන්වීම සහ ප්‍රශික්ෂක මණ්ඩලයේ වෘත්තිමය අධ්‍යාපනය තුළින් කණ්ඩායම හොඳ කාර්ය සාධනයක් සිදු කිරීමට සූදානම් වෙමින් සිටී.',
        'category' => 'ක්‍රීඩා',
        'date' => '2025 ජූලි 13',
        'author' => 'ක්‍රීඩා වාර්තාකරු',
        'image' => 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Cricket+Team',
        'tags' => ['ක්‍රිකට්', 'ලෝක කුසලානය', 'ශ්‍රී ලංකා', 'ක්‍රීඩා']
    ]
];

$article = isset($articles[$article_id]) ? $articles[$article_id] : $articles[1];
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | සිංහල ප්‍රවෘත්ති</title>
    <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($article['content']), 0, 160)) ?>">
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
            <a href="category.php?cat=<?= strtolower($article['category']) ?>"><?= $article['category'] ?></a> &gt;
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
                            <span class="category"><?= $article['category'] ?></span>
                            <span class="date"><?= $article['date'] ?></span>
                            <span class="author">කතුර: <?= $article['author'] ?></span>
                        </div>
                        <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>

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
                    <div class="article-image">
                        <img src="<?= $article['image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                    </div>

                    <!-- Article Body -->
                    <div class="article-body">
                        <?php
                        // Convert line breaks to paragraphs
                        $paragraphs = explode("\n\n", $article['content']);
                        foreach ($paragraphs as $paragraph) {
                            if (trim($paragraph)) {
                                echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
                            }
                        }
                        ?>
                    </div>

                    <!-- Article Tags -->
                    <div class="article-tags">
                        <strong>ටැග්:</strong>
                        <?php foreach ($article['tags'] as $tag): ?>
                            <span class="tag"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Navigation -->
                    <div class="article-navigation">
                        <a href="article.php?id=<?= $article_id > 1 ? $article_id - 1 : count($articles) ?>" class="prev-article">&larr; පෙර ප්‍රවෘත්තිය</a>
                        <a href="article.php?id=<?= $article_id < count($articles) ? $article_id + 1 : 1 ?>" class="next-article">ඊළඟ ප්‍රවෘත්තිය &rarr;</a>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <!-- Related Articles -->
                    <div class="widget">
                        <h3 class="widget-title">අදාළ ප්‍රවෘත්ති</h3>
                        <div class="related-articles">
                            <?php
                            // Show other articles as related
                            $related_count = 0;
                            foreach ($articles as $id => $related_article) {
                                if ($id != $article_id && $related_count < 3) {
                                    $related_count++;
                            ?>
                                <article class="related-item">
                                    <img src="<?= $related_article['image'] ?>" alt="<?= htmlspecialchars($related_article['title']) ?>">
                                    <div class="related-content">
                                        <h4><a href="article.php?id=<?= $id ?>"><?= htmlspecialchars($related_article['title']) ?></a></h4>
                                        <span class="date"><?= $related_article['date'] ?></span>
                                    </div>
                                </article>
                            <?php
                                }
                            }
                            ?>
                        </div>
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

                    <!-- Advertisement Space -->
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
            const text = `${title} - සිංහල ප්‍රවෘත්ති`;
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

        .article-title {
            font-size: 2.2rem;
            line-height: 1.3;
            margin-bottom: 1rem;
            color: #333;
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

        .article-tags {
            padding: 1rem 0;
            border-top: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin: 0.25rem 0.5rem 0.25rem 0;
        }

        .article-navigation {
            display: flex;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .prev-article, .next-article {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .prev-article:hover, .next-article:hover {
            color: #1e3a72;
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

        .ad-space {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
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

            .article-navigation {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</body>
</html>
