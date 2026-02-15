# Database Explorer Plugin

## Overview

The Database Explorer plugin demonstrates WordPress data structure and database schema by creating sample data and visualizing it through an admin interface.

## What It Does

### On Activation
- Creates sample "Portfolio" category
- Creates 3 sample project posts with custom meta data
- Stores plugin settings in WordPress options
- Demonstrates: `wp_insert_term()`, `wp_insert_post()`, `add_post_meta()`, `update_option()`

### Admin Interface
- Displays WordPress options (from `wp_options` table)
- Shows posts filtered by meta data using `WP_Query`
- Lists all categories/terms with their relationships
- Shows all users, their roles, and capabilities

## Key Concepts Demonstrated

### 1. WordPress Options (wp_options table)
```php
update_option( 'de_plugin_title', 'Database Explorer' );
get_option( 'de_plugin_title' );
```

### 2. Posts and Post Meta (wp_posts, wp_postmeta)
```php
wp_insert_post( [ ... ] );
add_post_meta( $post_id, 'project_status', 'completed' );
get_post_meta( $post_id, 'project_status', true );
```

### 3. WP_Query with Meta Filtering
```php
$query = new WP_Query( [
    'post_type' => 'post',
    'meta_key'  => 'project_status',
    'meta_value' => 'completed',
] );
```

### 4. Taxonomies and Terms (wp_terms, wp_term_taxonomy, wp_term_relationships)
```php
wp_insert_term( 'Portfolio', 'category' );
wp_set_post_terms( $post_id, $term_id, 'category' );
get_terms( [ 'taxonomy' => 'category' ] );
```

### 5. Users and Capabilities (wp_users, wp_usermeta)
```php
get_users();
user_can( $user->ID, 'edit_posts' );
current_user_can( 'manage_options' );
```

## How to Use

1. **Activate the plugin** through WordPress admin
2. Go to **Database Explorer** menu in WordPress admin
3. View all database tables and their relationships
4. Study the source code to understand each concept

## Database Tables Referenced

| Table | Purpose |
|-------|---------|
| wp_posts | Post, page, and custom post type data |
| wp_postmeta | Custom field data for posts |
| wp_categories | Deprecated - now wp_terms |
| wp_terms | All taxonomy terms (categories, tags, etc) |
| wp_term_taxonomy | Taxonomy type definitions |
| wp_term_relationships | Which terms are assigned to posts |
| wp_users | User account data |
| wp_usermeta | Custom user field data |
| wp_options | Site-wide settings and options |
| wp_comments | Comments on posts |
| wp_commentmeta | Custom comment field data |
| wp_links | Deprecated - blogroll links |

## Files

- `database-explorer.php` - Main plugin file with all functionality
- `README.md` - This file
- `TUTORIAL.md` - Detailed explanation of each concept
