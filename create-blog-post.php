<?php
/**
 * Create Claude AI Basics Blog Post
 *
 * This script loads WordPress and creates the blog post programmatically.
 * Run from project root: php create-blog-post.php
 */

// Load WordPress
require_once __DIR__ . '/app/public/wp-load.php';

// Verify we're logged in or bypass auth check
if (!function_exists('is_user_logged_in') || !is_user_logged_in()) {
    // Use admin user for post creation
    wp_set_current_user(1);
}

// Blog post content from markdown file
$content_file = __DIR__ . '/blog-posts/claude-ai-basics.md';

if (!file_exists($content_file)) {
    die("Error: Blog post file not found at $content_file\n");
}

$post_content = file_get_contents($content_file);

// Post data
$post_data = array(
    'post_title'    => 'Claude AI Basics: Context, Memory, Skills, and Tools Explained',
    'post_name'     => 'claude-ai-basics-context-memory-skills-tools',
    'post_content'  => $post_content,
    'post_excerpt'  => 'Deep-dive guide to Claude\'s core capabilities. Learn how context windows, persistent memory, skills, and tools work together to build production AI systems.',
    'post_status'   => 'draft',
    'post_type'     => 'post',
    'post_author'   => 1,
);

// Create the post
$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
    die("Error creating post: " . $post_id->get_error_message() . "\n");
}

echo "✅ Blog post created successfully!\n";
echo "Post ID: $post_id\n";
echo "Title: " . $post_data['post_title'] . "\n";
echo "URL: " . get_permalink($post_id) . "\n";
echo "Status: Draft (edit in WordPress to add featured image and publish)\n";

// Set categories
$categories = array();
$category_names = array('AI', 'Technical', 'Consulting');

foreach ($category_names as $cat_name) {
    $cat = get_term_by('name', $cat_name, 'category');
    if ($cat) {
        $categories[] = $cat->term_id;
    } else {
        $new_cat = wp_create_category($cat_name);
        if ($new_cat && !is_wp_error($new_cat)) {
            $categories[] = $new_cat;
        }
    }
}

if (!empty($categories)) {
    wp_set_post_categories($post_id, $categories);
    echo "Categories set: " . implode(', ', $category_names) . "\n";
}

// Set tags
$tags = array('claude-ai', 'prompt-engineering', 'ai-consulting', 'developer-guide');
wp_set_post_tags($post_id, $tags);
echo "Tags set: " . implode(', ', $tags) . "\n";

// Add post meta
update_post_meta($post_id, '_yoast_wpseo_title', 'Claude AI Basics: Context, Memory, Skills, and Tools Explained');
update_post_meta($post_id, '_yoast_wpseo_metadesc', 'Deep-dive technical guide to Claude\'s core capabilities for API-familiar developers. Learn context windows, memory systems, skills, and tools with real examples.');

echo "\n✅ Post setup complete! Now:\n";
echo "1. Go to WordPress admin\n";
echo "2. Edit the draft post\n";
echo "3. Add featured image: /app/public/wp-content/uploads/claude-ai-basics-featured.jpg\n";
echo "4. Publish the post\n";
