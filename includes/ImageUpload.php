<?php
/**
 * Image Upload Class for Sinhala News Website
 * Handles image upload, resizing, and thumbnail generation
 */

class ImageUpload {
    private $upload_path;
    private $allowed_types;
    private $max_file_size;
    private $image_sizes;

    public function __construct() {
        $this->upload_path = __DIR__ . '/../uploads/';
        $this->allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $this->max_file_size = MAX_FILE_SIZE; // 5MB from config
        $this->image_sizes = [
            'thumbnail' => ['width' => 150, 'height' => 100],
            'medium' => ['width' => 400, 'height' => 250],
            'large' => ['width' => 800, 'height' => 500],
            'featured' => ['width' => 1200, 'height' => 600]
        ];

        $this->createDirectories();
    }

    /**
     * Create necessary upload directories
     */
    private function createDirectories() {
        $directories = [
            $this->upload_path . 'articles/',
            $this->upload_path . 'articles/thumbnails/',
            $this->upload_path . 'articles/medium/',
            $this->upload_path . 'articles/large/',
            $this->upload_path . 'articles/featured/',
            $this->upload_path . 'temp/'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Upload and process image
     */
    public function uploadImage($file, $article_id = null) {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['success']) {
                return $validation;
            }

            // Generate unique filename
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '_' . time() . '.' . $file_extension;

            // Move uploaded file to temp directory
            $temp_path = $this->upload_path . 'temp/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $temp_path)) {
                return ['success' => false, 'message' => 'ගොනුව උඩුගත කිරීමට නොහැකි විය'];
            }

            // Process and resize images
            $processed_images = $this->processImage($temp_path, $filename);

            // Remove temp file
            unlink($temp_path);

            if (!$processed_images['success']) {
                return $processed_images;
            }

            return [
                'success' => true,
                'message' => 'රූපය සාර්ථකව උඩුගත කරන ලදී',
                'filename' => $filename,
                'images' => $processed_images['images'],
                'main_url' => '/uploads/articles/large/' . $filename
            ];

        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            return ['success' => false, 'message' => 'රූපය උඩුගත කිරීමේදී දෝෂයක් ඇතිවිය'];
        }
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'ගොනුව උඩුගත කිරීමේදී දෝෂයක් ඇතිවිය'];
        }

        // Check file size
        if ($file['size'] > $this->max_file_size) {
            $max_mb = round($this->max_file_size / 1024 / 1024, 1);
            return ['success' => false, 'message' => "ගොනුවේ ප්‍රමාණය {$max_mb}MB ට වඩා අඩු විය යුතුය"];
        }

        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $this->allowed_types)) {
            return ['success' => false, 'message' => 'අවසර ලත් ගොනු වර්ග: ' . implode(', ', $this->allowed_types)];
        }

        // Check if it's actually an image
        $image_info = getimagesize($file['tmp_name']);
        if (!$image_info) {
            return ['success' => false, 'message' => 'වලංගු රූප ගොනුවක් නොවේ'];
        }

        // Check image dimensions (minimum)
        if ($image_info[0] < 200 || $image_info[1] < 150) {
            return ['success' => false, 'message' => 'රූපයේ අවම ප්‍රමාණය 200x150 pixels විය යුතුය'];
        }

        return ['success' => true];
    }

    /**
     * Process and resize image to different sizes
     */
    private function processImage($source_path, $filename) {
        try {
            // Get image info
            $image_info = getimagesize($source_path);
            $mime_type = $image_info['mime'];

            // Create image resource
            switch ($mime_type) {
                case 'image/jpeg':
                    $source_image = imagecreatefromjpeg($source_path);
                    break;
                case 'image/png':
                    $source_image = imagecreatefrompng($source_path);
                    break;
                case 'image/gif':
                    $source_image = imagecreatefromgif($source_path);
                    break;
                case 'image/webp':
                    $source_image = imagecreatefromwebp($source_path);
                    break;
                default:
                    return ['success' => false, 'message' => 'සහාය නොදක්වන රූප වර්ගය'];
            }

            if (!$source_image) {
                return ['success' => false, 'message' => 'රූපය ප්‍රක්‍රියා කිරීමට නොහැකි විය'];
            }

            $original_width = imagesx($source_image);
            $original_height = imagesy($source_image);

            $processed_images = [];

            // Create different sizes
            foreach ($this->image_sizes as $size_name => $dimensions) {
                $new_path = $this->upload_path . 'articles/' . $size_name . '/' . $filename;

                // Calculate new dimensions maintaining aspect ratio
                $new_dimensions = $this->calculateDimensions(
                    $original_width,
                    $original_height,
                    $dimensions['width'],
                    $dimensions['height']
                );

                // Create resized image
                $resized_image = imagecreatetruecolor($new_dimensions['width'], $new_dimensions['height']);

                // Preserve transparency for PNG and GIF
                if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
                    imagecolortransparent($resized_image, imagecolorallocatealpha($resized_image, 0, 0, 0, 127));
                    imagealphablending($resized_image, false);
                    imagesavealpha($resized_image, true);
                }

                // Resize image
                imagecopyresampled(
                    $resized_image, $source_image,
                    0, 0, 0, 0,
                    $new_dimensions['width'], $new_dimensions['height'],
                    $original_width, $original_height
                );

                // Save resized image
                $save_success = false;
                switch ($mime_type) {
                    case 'image/jpeg':
                        $save_success = imagejpeg($resized_image, $new_path, 85);
                        break;
                    case 'image/png':
                        $save_success = imagepng($resized_image, $new_path, 6);
                        break;
                    case 'image/gif':
                        $save_success = imagegif($resized_image, $new_path);
                        break;
                    case 'image/webp':
                        $save_success = imagewebp($resized_image, $new_path, 85);
                        break;
                }

                if ($save_success) {
                    $processed_images[$size_name] = '/uploads/articles/' . $size_name . '/' . $filename;
                }

                imagedestroy($resized_image);
            }

            imagedestroy($source_image);

            return [
                'success' => true,
                'images' => $processed_images
            ];

        } catch (Exception $e) {
            error_log("Image processing error: " . $e->getMessage());
            return ['success' => false, 'message' => 'රූපය ප්‍රක්‍රියා කිරීමේදී දෝෂයක් ඇතිවිය'];
        }
    }

    /**
     * Calculate new dimensions maintaining aspect ratio
     */
    private function calculateDimensions($original_width, $original_height, $max_width, $max_height) {
        $ratio = min($max_width / $original_width, $max_height / $original_height);

        return [
            'width' => (int)($original_width * $ratio),
            'height' => (int)($original_height * $ratio)
        ];
    }

    /**
     * Delete image files
     */
    public function deleteImage($filename) {
        try {
            foreach (array_keys($this->image_sizes) as $size_name) {
                $file_path = $this->upload_path . 'articles/' . $size_name . '/' . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Image deletion error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get image URL by size
     */
    public function getImageUrl($filename, $size = 'large') {
        if (empty($filename)) {
            return null;
        }

        if (!isset($this->image_sizes[$size])) {
            $size = 'large';
        }

        return '/uploads/articles/' . $size . '/' . $filename;
    }

    /**
     * Check if image exists
     */
    public function imageExists($filename, $size = 'large') {
        if (empty($filename)) {
            return false;
        }

        $file_path = $this->upload_path . 'articles/' . $size . '/' . $filename;
        return file_exists($file_path);
    }
}
?>
