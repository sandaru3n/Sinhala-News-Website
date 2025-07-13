<?php
/**
 * Database Class for Sinhala News Website
 * Uses MySQLi with prepared statements for security
 */

class Database {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_NAME;

        $this->connect();
    }

    /**
     * Connect to MySQL database
     */
    private function connect() {
        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }

            // Set charset to utf8mb4 for proper Sinhala support
            $this->connection->set_charset(DB_CHARSET);

        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if database is connected
     */
    public function isConnected() {
        return $this->connection && $this->connection->ping();
    }

    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Execute prepared statement
     */
    private function execute($query, $params = []) {
        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt;
    }

    // ======================
    // ARTICLE METHODS
    // ======================

    /**
     * Get all published articles with pagination
     */
    public function getPublishedArticles($page = 1, $limit = ARTICLES_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT a.*, c.name_sinhala as category_name, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE a.status = 'published'
                  ORDER BY a.featured DESC, a.published_at DESC, a.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->execute($query, [$limit, $offset]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get featured articles
     */
    public function getFeaturedArticles($limit = 1) {
        $query = "SELECT a.*, c.name_sinhala as category_name, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE a.status = 'published' AND a.featured = 1
                  ORDER BY a.published_at DESC
                  LIMIT ?";

        $stmt = $this->execute($query, [$limit]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get article by ID
     */
    public function getArticle($id) {
        $query = "SELECT a.*, c.name_sinhala as category_name, c.slug as category_slug, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE a.id = ?";

        $stmt = $this->execute($query, [$id]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get article by slug
     */
    public function getArticleBySlug($slug) {
        $query = "SELECT a.*, c.name_sinhala as category_name, c.slug as category_slug, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE a.slug = ? AND a.status = 'published'";

        $stmt = $this->execute($query, [$slug]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get articles by category
     */
    public function getArticlesByCategory($category_slug, $page = 1, $limit = ARTICLES_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT a.*, c.name_sinhala as category_name, u.full_name as author_name
                  FROM articles a
                  JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE c.slug = ? AND a.status = 'published'
                  ORDER BY a.published_at DESC, a.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->execute($query, [$category_slug, $limit, $offset]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search articles
     */
    public function searchArticles($search_term, $page = 1, $limit = ARTICLES_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $search_term = "%$search_term%";

        $query = "SELECT a.*, c.name_sinhala as category_name, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  WHERE a.status = 'published'
                  AND (a.title LIKE ? OR a.summary LIKE ? OR a.content LIKE ?)
                  ORDER BY a.published_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->execute($query, [$search_term, $search_term, $search_term, $limit, $offset]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get popular articles
     */
    public function getPopularArticles($limit = 5) {
        $query = "SELECT a.*, c.name_sinhala as category_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.status = 'published'
                  ORDER BY a.views DESC, a.published_at DESC
                  LIMIT ?";

        $stmt = $this->execute($query, [$limit]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Increment article views
     */
    public function incrementViews($article_id) {
        $query = "UPDATE articles SET views = views + 1 WHERE id = ?";
        $this->execute($query, [$article_id]);
    }

    /**
     * Create new article
     */
    public function createArticle($data) {
        $query = "INSERT INTO articles (title, slug, summary, content, category_id, author_id, image_url, status, featured)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $slug = $this->generateUniqueSlug($data['title'], 'articles');

        $params = [
            $data['title'],
            $slug,
            $data['summary'],
            $data['content'],
            $data['category_id'],
            $data['author_id'],
            $data['image_url'] ?? null,
            $data['status'] ?? 'draft',
            $data['featured'] ?? 0
        ];

        $stmt = $this->execute($query, $params);

        $article_id = $this->connection->insert_id;

        // Update published_at if status is published
        if ($data['status'] === 'published') {
            $this->execute("UPDATE articles SET published_at = NOW() WHERE id = ?", [$article_id]);
        }

        return $article_id;
    }

    /**
     * Update article
     */
    public function updateArticle($id, $data) {
        $query = "UPDATE articles SET title = ?, summary = ?, content = ?, category_id = ?,
                  image_url = ?, status = ?, featured = ?, updated_at = NOW() WHERE id = ?";

        $params = [
            $data['title'],
            $data['summary'],
            $data['content'],
            $data['category_id'],
            $data['image_url'] ?? null,
            $data['status'],
            $data['featured'] ?? 0,
            $id
        ];

        $stmt = $this->execute($query, $params);

        // Update published_at if status is published
        if ($data['status'] === 'published') {
            $this->execute("UPDATE articles SET published_at = NOW() WHERE id = ? AND published_at IS NULL", [$id]);
        }

        return $stmt->affected_rows > 0;
    }

    /**
     * Delete article
     */
    public function deleteArticle($id) {
        $query = "DELETE FROM articles WHERE id = ?";
        $stmt = $this->execute($query, [$id]);
        return $stmt->affected_rows > 0;
    }

    /**
     * Get all articles for admin
     */
    public function getAllArticlesForAdmin($page = 1, $limit = ADMIN_ARTICLES_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT a.*, c.name_sinhala as category_name, u.full_name as author_name
                  FROM articles a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users u ON a.author_id = u.id
                  ORDER BY a.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->execute($query, [$limit, $offset]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ======================
    // CATEGORY METHODS
    // ======================

    /**
     * Get all categories
     */
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name_sinhala";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        $query = "SELECT * FROM categories WHERE slug = ?";
        $stmt = $this->execute($query, [$slug]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Update category article count
     */
    public function updateCategoryCount($category_id) {
        $query = "UPDATE categories SET article_count = (
                    SELECT COUNT(*) FROM articles
                    WHERE category_id = ? AND status = 'published'
                  ) WHERE id = ?";
        $this->execute($query, [$category_id, $category_id]);
    }

    // ======================
    // USER METHODS
    // ======================

    /**
     * Get user by username
     */
    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = ? AND status = 'active'";
        $stmt = $this->execute($query, [$username]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get user by ID
     */
    public function getUser($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->execute($query, [$id]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        $query = "INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['full_name'],
            $data['role'] ?? 'author'
        ];

        $stmt = $this->execute($query, $params);
        return $this->connection->insert_id;
    }

    /**
     * Verify user login
     */
    public function verifyLogin($username, $password) {
        $user = $this->getUserByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    // ======================
    // CONTACT METHODS
    // ======================

    /**
     * Save contact message
     */
    public function saveContactMessage($data) {
        $query = "INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['subject'],
            $data['message']
        ];

        $stmt = $this->execute($query, $params);
        return $this->connection->insert_id;
    }

    // ======================
    // COMMENT METHODS
    // ======================

    /**
     * Get comments for an article
     */
    public function getArticleComments($article_id, $status = 'approved', $limit = 20) {
        $query = "SELECT * FROM comments
                  WHERE article_id = ? AND status = ?
                  ORDER BY created_at DESC
                  LIMIT ?";

        $stmt = $this->execute($query, [$article_id, $status, $limit]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add new comment
     */
    public function addComment($data) {
        $query = "INSERT INTO comments (article_id, author_name, author_email, content, status)
                  VALUES (?, ?, ?, ?, ?)";

        $params = [
            $data['article_id'],
            $data['author_name'],
            $data['author_email'],
            $data['content'],
            $data['status'] ?? 'pending'
        ];

        $stmt = $this->execute($query, $params);
        return $this->connection->insert_id;
    }

    /**
     * Get comment by ID
     */
    public function getComment($comment_id) {
        $query = "SELECT c.*, a.title as article_title
                  FROM comments c
                  LEFT JOIN articles a ON c.article_id = a.id
                  WHERE c.id = ?";

        $stmt = $this->execute($query, [$comment_id]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Update comment status
     */
    public function updateCommentStatus($comment_id, $status) {
        $query = "UPDATE comments SET status = ? WHERE id = ?";
        $stmt = $this->execute($query, [$status, $comment_id]);
        return $stmt->affected_rows > 0;
    }

    /**
     * Delete comment
     */
    public function deleteComment($comment_id) {
        $query = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->execute($query, [$comment_id]);
        return $stmt->affected_rows > 0;
    }

    /**
     * Get comments for admin (all statuses)
     */
    public function getCommentsForAdmin($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT c.*, a.title as article_title
                  FROM comments c
                  LEFT JOIN articles a ON c.article_id = a.id
                  ORDER BY c.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->execute($query, [$limit, $offset]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get comment count by status
     */
    public function getCommentCounts() {
        $query = "SELECT status, COUNT(*) as count FROM comments GROUP BY status";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();

        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = $row['count'];
        }

        return $counts;
    }

    // ======================
    // IMAGE METHODS
    // ======================

    /**
     * Save uploaded image information
     */
    public function saveImageInfo($article_id, $filename, $original_name, $sizes) {
        $query = "INSERT INTO article_images (article_id, filename, original_name, sizes, created_at)
                  VALUES (?, ?, ?, ?, NOW())";

        $sizes_json = json_encode($sizes);
        $stmt = $this->execute($query, [$article_id, $filename, $original_name, $sizes_json]);
        return $this->connection->insert_id;
    }

    /**
     * Get images for an article
     */
    public function getArticleImages($article_id) {
        $query = "SELECT * FROM article_images WHERE article_id = ? ORDER BY created_at DESC";
        $stmt = $this->execute($query, [$article_id]);
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $row['sizes'] = json_decode($row['sizes'], true);
            $images[] = $row;
        }

        return $images;
    }

    /**
     * Delete image record
     */
    public function deleteImageRecord($image_id) {
        $query = "DELETE FROM article_images WHERE id = ?";
        $stmt = $this->execute($query, [$image_id]);
        return $stmt->affected_rows > 0;
    }

    // ======================
    // UTILITY METHODS
    // ======================

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($title, $table) {
        $base_slug = generate_slug($title);
        $slug = $base_slug;
        $counter = 1;

        while ($this->slugExists($slug, $table)) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists($slug, $table) {
        $query = "SELECT COUNT(*) as count FROM $table WHERE slug = ?";
        $stmt = $this->execute($query, [$slug]);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Get site statistics
     */
    public function getSiteStats() {
        $stats = [];

        // Total articles
        $query = "SELECT COUNT(*) as count FROM articles";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['total_articles'] = $result->fetch_assoc()['count'];

        // Published articles
        $query = "SELECT COUNT(*) as count FROM articles WHERE status = 'published'";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['published_articles'] = $result->fetch_assoc()['count'];

        // Draft articles
        $query = "SELECT COUNT(*) as count FROM articles WHERE status = 'draft'";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['draft_articles'] = $result->fetch_assoc()['count'];

        // Total categories
        $query = "SELECT COUNT(*) as count FROM categories";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['total_categories'] = $result->fetch_assoc()['count'];

        // Total users
        $query = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['total_users'] = $result->fetch_assoc()['count'];

        // Total contact messages
        $query = "SELECT COUNT(*) as count FROM contact_messages";
        $stmt = $this->execute($query);
        $result = $stmt->get_result();
        $stats['total_messages'] = $result->fetch_assoc()['count'];

        return $stats;
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity($limit = 10) {
        $query = "SELECT 'article' as type, title as item, created_at as date FROM articles
                  UNION ALL
                  SELECT 'message' as type, CONCAT(first_name, ' ', last_name) as item, created_at as date FROM contact_messages
                  ORDER BY date DESC
                  LIMIT ?";

        $stmt = $this->execute($query, [$limit]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->connection->autocommit(false);
    }

    /**
     * Commit transaction
     */
    public function commit() {
        $this->connection->commit();
        $this->connection->autocommit(true);
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->connection->rollback();
        $this->connection->autocommit(true);
    }

    /**
     * Destructor - close connection
     */
    public function __destruct() {
        $this->close();
    }

    public function getAdjacentArticle($current_id, $direction = 'prev') {
        if ($direction === 'prev') {
            $query = "SELECT id, title FROM articles WHERE id < ? AND status = 'published' ORDER BY id DESC LIMIT 1";
        } else {
            $query = "SELECT id, title FROM articles WHERE id > ? AND status = 'published' ORDER BY id ASC LIMIT 1";
        }
        $stmt = $this->execute($query, [$current_id]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
