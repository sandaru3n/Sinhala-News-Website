<?php
/**
 * SEO Helper Class for Sinhala News Website
 * Handles meta tags, Open Graph, Twitter Cards, and Structured Data
 */

class SEOHelper {
    private $site_name;
    private $site_url;
    private $default_image;
    private $twitter_handle;
    private $facebook_app_id;

    public function __construct() {
        $this->site_name = SITE_TITLE;
        $this->site_url = SITE_URL;
        $this->default_image = SITE_URL . '/assets/images/default-og-image.jpg';
        $this->twitter_handle = '@sinhalanews'; // Update as needed
        $this->facebook_app_id = ''; // Add your Facebook App ID
    }

    /**
     * Generate complete SEO meta tags for article pages
     */
    public function generateArticleSEO($article) {
        $title = $this->cleanText($article['title']);
        $description = $this->generateDescription($article);
        $image = $this->getArticleImage($article);
        $url = $this->site_url . '/article_db.php?id=' . $article['id'];
        $published_time = date('c', strtotime($article['published_at']));
        $modified_time = date('c', strtotime($article['updated_at']));

        $meta_tags = [];

        // Basic meta tags
        $meta_tags[] = '<title>' . htmlspecialchars($title . ' | ' . $this->site_name) . '</title>';
        $meta_tags[] = '<meta name="description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<meta name="keywords" content="' . htmlspecialchars($this->generateKeywords($article)) . '">';
        $meta_tags[] = '<link rel="canonical" href="' . htmlspecialchars($url) . '">';

        // Open Graph tags
        $meta_tags[] = '<meta property="og:type" content="article">';
        $meta_tags[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
        $meta_tags[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<meta property="og:url" content="' . htmlspecialchars($url) . '">';
        $meta_tags[] = '<meta property="og:site_name" content="' . htmlspecialchars($this->site_name) . '">';
        $meta_tags[] = '<meta property="og:image" content="' . htmlspecialchars($image) . '">';
        $meta_tags[] = '<meta property="og:image:width" content="1200">';
        $meta_tags[] = '<meta property="og:image:height" content="630">';
        $meta_tags[] = '<meta property="og:locale" content="si_LK">';

        // Article specific Open Graph
        $meta_tags[] = '<meta property="article:published_time" content="' . $published_time . '">';
        $meta_tags[] = '<meta property="article:modified_time" content="' . $modified_time . '">';
        $meta_tags[] = '<meta property="article:author" content="' . htmlspecialchars($article['author_name']) . '">';
        $meta_tags[] = '<meta property="article:section" content="' . htmlspecialchars($article['category_name']) . '">';

        // Twitter Card tags
        $meta_tags[] = '<meta name="twitter:card" content="summary_large_image">';
        $meta_tags[] = '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">';
        $meta_tags[] = '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">';
        if ($this->twitter_handle) {
            $meta_tags[] = '<meta name="twitter:site" content="' . $this->twitter_handle . '">';
        }

        // Additional SEO tags
        $meta_tags[] = '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
        $meta_tags[] = '<meta name="googlebot" content="index, follow">';

        return implode("\n    ", $meta_tags);
    }

    /**
     * Generate NewsArticle structured data
     */
    public function generateNewsArticleStructuredData($article) {
        $image = $this->getArticleImage($article);
        $url = $this->site_url . '/article_db.php?id=' . $article['id'];

        $structured_data = [
            "@context" => "https://schema.org",
            "@type" => "NewsArticle",
            "headline" => $this->cleanText($article['title']),
            "description" => $this->generateDescription($article),
            "image" => [
                "@type" => "ImageObject",
                "url" => $image,
                "width" => 1200,
                "height" => 630
            ],
            "datePublished" => date('c', strtotime($article['published_at'])),
            "dateModified" => date('c', strtotime($article['updated_at'])),
            "author" => [
                "@type" => "Person",
                "name" => $article['author_name']
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $this->site_name,
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $this->site_url . "/assets/images/logo.png",
                    "width" => 300,
                    "height" => 100
                ]
            ],
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $url
            ],
            "url" => $url,
            "articleSection" => $article['category_name'],
            "wordCount" => str_word_count(strip_tags($article['content'])),
            "inLanguage" => "si-LK",
            "about" => [
                "@type" => "Thing",
                "name" => $article['category_name']
            ]
        ];

        // Add keywords if available
        $keywords = $this->generateKeywords($article);
        if ($keywords) {
            $structured_data["keywords"] = explode(', ', $keywords);
        }

        return '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate organization structured data
     */
    public function generateOrganizationStructuredData() {
        $structured_data = [
            "@context" => "https://schema.org",
            "@type" => "NewsMediaOrganization",
            "name" => $this->site_name,
            "url" => $this->site_url,
            "logo" => [
                "@type" => "ImageObject",
                "url" => $this->site_url . "/assets/images/logo.png"
            ],
            "sameAs" => [
                "https://www.facebook.com/sinhalanews",
                "https://www.twitter.com/sinhalanews",
                "https://www.instagram.com/sinhalanews",
                "https://www.youtube.com/sinhalanews"
            ],
            "description" => SITE_TAGLINE,
            "foundingDate" => "2025-01-01",
            "address" => [
                "@type" => "PostalAddress",
                "addressCountry" => "LK",
                "addressLocality" => "Colombo"
            ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "contactType" => "Editorial",
                "email" => ADMIN_EMAIL
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate breadcrumb structured data
     */
    public function generateBreadcrumbStructuredData($breadcrumbs) {
        $items = [];
        $position = 1;

        foreach ($breadcrumbs as $breadcrumb) {
            $items[] = [
                "@type" => "ListItem",
                "position" => $position++,
                "name" => $breadcrumb['name'],
                "item" => $breadcrumb['url']
            ];
        }

        $structured_data = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $items
        ];

        return '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate website structured data
     */
    public function generateWebsiteStructuredData() {
        $structured_data = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => $this->site_name,
            "url" => $this->site_url,
            "description" => SITE_TAGLINE,
            "inLanguage" => "si-LK",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => $this->site_url . "/search_db.php?q={search_term_string}"
                ],
                "query-input" => "required name=search_term_string"
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate meta tags for category pages
     */
    public function generateCategorySEO($category, $articles_count = 0) {
        $title = $category['name_sinhala'] . ' ප්‍රවෘත්ති';
        $description = $category['description'] ?: ($category['name_sinhala'] . ' ප්‍රවර්ගයේ නවතම සිංහල ප්‍රවෘත්ති. ' . $articles_count . ' ප්‍රවෘත්ති ලබා ගත හැක.');
        $url = $this->site_url . '/category_db.php?cat=' . $category['slug'];

        $meta_tags = [];

        $meta_tags[] = '<title>' . htmlspecialchars($title . ' | ' . $this->site_name) . '</title>';
        $meta_tags[] = '<meta name="description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<link rel="canonical" href="' . htmlspecialchars($url) . '">';

        // Open Graph
        $meta_tags[] = '<meta property="og:type" content="website">';
        $meta_tags[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
        $meta_tags[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<meta property="og:url" content="' . htmlspecialchars($url) . '">';
        $meta_tags[] = '<meta property="og:image" content="' . htmlspecialchars($this->default_image) . '">';

        return implode("\n    ", $meta_tags);
    }

    /**
     * Generate meta tags for homepage
     */
    public function generateHomepageSEO() {
        $title = $this->site_name;
        $description = SITE_TAGLINE . '. නවතම සිංහල ප්‍රවෘත්ති, දේශපාලන, ක්‍රීඩා, තාක්ෂණය, ව්‍යාපාර, විනෝදාස්වාදය සහ සෞඛ්‍ය පිළිබඳ ප්‍රවෘත්ති.';

        $meta_tags = [];

        $meta_tags[] = '<title>' . htmlspecialchars($title) . '</title>';
        $meta_tags[] = '<meta name="description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<link rel="canonical" href="' . htmlspecialchars($this->site_url) . '">';

        // Open Graph
        $meta_tags[] = '<meta property="og:type" content="website">';
        $meta_tags[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
        $meta_tags[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
        $meta_tags[] = '<meta property="og:url" content="' . htmlspecialchars($this->site_url) . '">';
        $meta_tags[] = '<meta property="og:image" content="' . htmlspecialchars($this->default_image) . '">';

        return implode("\n    ", $meta_tags);
    }

    /**
     * Generate description from article content
     */
    private function generateDescription($article) {
        if (!empty($article['summary'])) {
            return substr($this->cleanText($article['summary']), 0, 160);
        }

        $content = strip_tags($article['content']);
        $content = $this->cleanText($content);
        return substr($content, 0, 160) . (strlen($content) > 160 ? '...' : '');
    }

    /**
     * Get article image URL
     */
    private function getArticleImage($article) {
        if (!empty($article['image_url'])) {
            // If it's a relative URL, make it absolute
            if (strpos($article['image_url'], 'http') !== 0) {
                return $this->site_url . $article['image_url'];
            }
            return $article['image_url'];
        }

        return $this->default_image;
    }

    /**
     * Generate keywords from article
     */
    private function generateKeywords($article) {
        $keywords = [];

        // Add category
        $keywords[] = $article['category_name'];

        // Add some common Sinhala news keywords
        $keywords[] = 'සිංහල ප්‍රවෘත්ති';
        $keywords[] = 'ශ්‍රී ලංකා';
        $keywords[] = 'නවතම පුවත්';

        // Extract keywords from title (simple approach)
        $title_words = explode(' ', $this->cleanText($article['title']));
        foreach ($title_words as $word) {
            if (strlen($word) > 3) {
                $keywords[] = $word;
            }
        }

        return implode(', ', array_unique(array_slice($keywords, 0, 10)));
    }

    /**
     * Clean text for SEO
     */
    private function cleanText($text) {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Generate sitemap (basic implementation)
     */
    public function generateSitemap($articles, $categories) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($this->site_url) . '</loc>' . "\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        // Categories
        foreach ($categories as $category) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($this->site_url . '/category_db.php?cat=' . $category['slug']) . '</loc>' . "\n";
            $xml .= '    <changefreq>daily</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        // Articles
        foreach ($articles as $article) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($this->site_url . '/article_db.php?id=' . $article['id']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($article['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
?>
