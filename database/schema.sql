-- Sinhala News Website Database Schema
-- MySQL Database for managing news articles, categories, users, and settings

-- Create Database
CREATE DATABASE IF NOT EXISTS sinhala_news
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sinhala_news;

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    name_sinhala VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    article_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'editor', 'author') DEFAULT 'author',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Articles Table
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    summary TEXT,
    content LONGTEXT NOT NULL,
    category_id INT,
    author_id INT,
    image_url VARCHAR(500),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_featured (featured),
    INDEX idx_published_at (published_at),
    FULLTEXT(title, summary, content)
);

-- Tags Table
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    name_sinhala VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Article Tags Junction Table
CREATE TABLE article_tags (
    article_id INT,
    tag_id INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Comments Table (for future use)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Categories
INSERT INTO categories (name, name_sinhala, slug, description) VALUES
('Politics', 'දේශපාලන', 'politics', 'Political news and updates'),
('Sports', 'ක්‍රීඩා', 'sports', 'Sports news and events'),
('Technology', 'තාක්ෂණය', 'technology', 'Technology and innovation news'),
('Business', 'ව්‍යාපාර', 'business', 'Business and economic news'),
('Entertainment', 'විනෝදාස්වාදය', 'entertainment', 'Entertainment and celebrity news'),
('Health', 'සෞඛ්‍ය', 'health', 'Health and medical news');

-- Insert Default Admin User (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@sinhalanews.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin'),
('editor', 'editor@sinhalanews.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'News Editor', 'editor');

-- Insert Sample Tags
INSERT INTO tags (name, name_sinhala, slug) VALUES
('Breaking News', 'ප්‍රවෘත්ති', 'breaking-news'),
('Economy', 'ආර්ථිකය', 'economy'),
('Cricket', 'ක්‍රිකට්', 'cricket'),
('Government', 'රජය', 'government'),
('Technology', 'තාක්ෂණය', 'technology'),
('Education', 'අධ්‍යාපනය', 'education');

-- Insert Sample Articles
INSERT INTO articles (title, slug, summary, content, category_id, author_id, image_url, status, featured, published_at) VALUES
('ශ්‍රී ලංකාවේ නව ආර්ථික ප්‍රතිසංස්කරණ මාර්ගය', 'sri-lanka-economic-reform',
'ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී.',
'ශ්‍රී ලංකාවේ ආර්ථිකය සම්බන්ධයෙන් නව ප්‍රතිසංස්කරණ මාර්ගයක් ක්‍රියාත්මක කිරීමට රජය සූදානම් වෙමින් පවතී. මෙම ප්‍රතිසංස්කරණ මගින් රටේ ආර්ථික ස්ථාවරත්වය වැඩි දියුණු කිරීමට අපේක්ෂා කෙරේ.

ආර්ථික ප්‍රතිපත්ති සම්බන්ධ රාජ්‍ය ප්‍රතිපත්ති අනුව, රටේ සම්පත් කළමනාකරණය වඩාත් කාර්යක්ෂම ආකාරයකට සිදු කිරීමට පියවර ගනු ඇත. මේ සම්බන්ධයෙන් ප්‍රධානම වශයෙන් රටේ කර්මාන්ත ක්ෂේත්‍රය දියුණු කිරීම, කෘෂිකර්ම ක්ෂේත්‍රයේ නවීකරණය සහ සේවා ක්ෂේත්‍රයේ පුළුල් කිරීම ඇතුළත් වේ.

ආර්ථික අමාත්‍යවරයාගේ ප්‍රකාශයකට අනුව, මෙම ප්‍රතිසංස්කරණ වැඩසටහන තුළින් අදාළ ක්ෂේත්‍රවල රැකියා අවස්ථා වැඩි කිරීමට හැකි වනු ඇත. ඊට අමතරව විදේශ ආයෝජන ආකර්ෂණය කර ගැනීම සහ අපනයන ආදායම වැඩි කිරීම මගින් රටේ ආර්ථික තත්ත්වය සාධාරණීකරණය කිරීමට අපේක්ෂා කෙරේ.',
1, 1, 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Economic+Reform', 'published', TRUE, NOW()),

('ක්‍රිකට් ලෝක කුසලානයේ ශ්‍රී ලංකා කණ්ඩායම', 'cricket-world-cup-sri-lanka-team',
'ශ්‍රී ලංකා ක්‍රිකට් කණ්ඩායම නැවත ලෝක කුසලානයට සූදානම් වෙමින් පවතී.',
'ශ්‍රී ලංකා ක්‍රිකට් කණ්ඩායම නැවත ලෝක කුසලානයට සූදානම් වෙමින් පවතී. පසුගිය ක්‍රීඩා වාර කිහිපයේ දී ලැබූ අත්දැකීම් මත කණ්ඩායම තම ක්‍රීඩා ක්‍රමය වැඩි දියුණු කර ගෙන ඇත.

අලුතින් කණ්ඩායමට එක් වූ තරුණ ක්‍රීඩකයන්ගේ දක්ෂතා මගින් කණ්ඩායමේ ශක්තිය වැඩි කර ගැනීමට හැකි වී ඇත. ජාතික ක්‍රිකට් කණ්ඩායමේ නායකයාගේ මඟ පෙන්වීම සහ ප්‍රශික්ෂක මණ්ඩලයේ වෘත්තිමය අධ්‍යාපනය තුළින් කණ්ඩායම හොඳ කාර්ය සාධනයක් සිදු කිරීමට සූදානම් වෙමින් සිටී.',
2, 1, 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Cricket+Team', 'published', FALSE, NOW()),

('නව තාක්ෂණික නවෝත්පාදන ආයතනයක් පිහිටුවීම', 'new-technology-innovation-institute',
'ශ්‍රී ලංකාවේ තාක්ෂණික ක්ෂේත්‍රය දියුණු කිරීම සඳහා නව ආයතනයක් පිහිටුවීමට කටයුතු ආරම්භ වී ඇත.',
'ශ්‍රී ලංකාවේ තාක්ෂණික ක්ෂේත්‍රය දියුණු කිරීම සඳහා නව ආයතනයක් පිහිටුවීමට කටයුතු ආරම්භ වී ඇත. මෙම ආයතනය තුළින් තාක්ෂණික ක්ෂේත්‍රයේ නවෝත්පාදන වර්ධනය කිරීමට අපේක්ෂා කෙරේ.',
3, 2, 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Technology', 'published', FALSE, NOW()),

('නව ව්‍යාපාරික ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීම', 'new-business-policy-implementation',
'රටේ ව්‍යාපාරික ක්ෂේත්‍රය සදහා නව ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීමට අදාළ අධිකාරීන් කටයුතු කරමින් සිටිති.',
'රටේ ව්‍යාපාරික ක්ෂේත්‍රය සදහා නව ප්‍රතිපත්තියක් ක්‍රියාත්මක කිරීමට අදාළ අධිකාරීන් කටයුතු කරමින් සිටිති. මෙම ප්‍රතිපත්තිය මගින් කුඩා හා මධ්‍යම ව්‍යාපාර සඳහා වැඩි පහසුකම් ලබා දීමට අපේක්ෂා කෙරේ.',
4, 2, 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Business', 'published', FALSE, NOW()),

('කලාකරුවන්ගේ නව සංගීත ප්‍රසංගයක්', 'artists-new-music-concert',
'ශ්‍රී ලංකාවේ ප්‍රසිද්ධ කලාකරුවන් කිහිප දෙනෙකු සහභාගි වන නව සංගීත ප්‍රසංගයක් සංවිධානය කෙරේ.',
'ශ්‍රී ලංකාවේ ප්‍රසිද්ධ කලාකරුවන් කිහිප දෙනෙකු සහභාගි වන නව සංගීත ප්‍රසංගයක් සංවිධානය කෙරේ. මෙම ප්‍රසංගය තුළින් සිංහල සංගීත ක්ෂේත්‍රයේ නව දිශානතියක් ගැනීමට අපේක්ෂා කෙරේ.',
5, 1, 'https://via.placeholder.com/800x400/f8f9fa/6c757d?text=Entertainment', 'published', FALSE, NOW());

-- Insert Article Tags
INSERT INTO article_tags (article_id, tag_id) VALUES
(1, 1), (1, 2), (1, 4),  -- Economic reform article with breaking news, economy, government tags
(2, 3), (2, 1),          -- Cricket article with cricket, breaking news tags
(3, 5), (3, 6),          -- Technology article with technology, education tags
(4, 2), (4, 4),          -- Business article with economy, government tags
(5, 1);                  -- Entertainment article with breaking news tag

-- Insert Default Settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_title', 'සිංහල ප්‍රවෘත්ති', 'Main website title'),
('site_tagline', 'ශ්‍රී ලංකාවේ ප්‍රධාන ප්‍රවෘත්ති වෙබ් අඩවිය', 'Website tagline'),
('contact_email', 'info@sinhalanews.lk', 'Main contact email'),
('contact_phone', '+94 11 234 5678', 'Main contact phone'),
('contact_address', '123, ගාල්ල පාර, කොළඹ 03', 'Physical address'),
('articles_per_page', '10', 'Number of articles per page'),
('enable_comments', '1', 'Enable/disable comments'),
('site_maintenance', '0', 'Site maintenance mode');

-- Update category article counts
UPDATE categories SET article_count = (
    SELECT COUNT(*) FROM articles
    WHERE articles.category_id = categories.id
    AND articles.status = 'published'
);

-- Create indexes for better performance
CREATE INDEX idx_articles_title ON articles(title);
CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_tags_slug ON tags(slug);
CREATE INDEX idx_contact_status ON contact_messages(status);
CREATE INDEX idx_comments_status ON comments(status);

-- Create view for popular articles
CREATE OR REPLACE VIEW popular_articles AS
SELECT
    a.*,
    c.name_sinhala as category_name,
    u.full_name as author_name
FROM articles a
LEFT JOIN categories c ON a.category_id = c.id
LEFT JOIN users u ON a.author_id = u.id
WHERE a.status = 'published'
ORDER BY a.views DESC, a.created_at DESC;

-- Create view for latest articles
CREATE OR REPLACE VIEW latest_articles AS
SELECT
    a.*,
    c.name_sinhala as category_name,
    u.full_name as author_name
FROM articles a
LEFT JOIN categories c ON a.category_id = c.id
LEFT JOIN users u ON a.author_id = u.id
WHERE a.status = 'published'
ORDER BY a.published_at DESC, a.created_at DESC;
