# WordPress Database Structure & Schema - Complete Tutorial

This tutorial explains WordPress data structure and demonstrates it through the Database Explorer plugin. Each section maps to the Issue #2 learning objectives.

## Table of Contents

1. [WordPress Database Structure](#wordpress-database-structure)
2. [Post Types & Post Meta](#post-types--post-meta)
3. [Taxonomies (Categories, Tags)](#taxonomies-categories-tags)
4. [Users & Capabilities](#users--capabilities)
5. [Options & Settings](#options--settings)
6. [Key Functions Reference](#key-functions-reference)

---

## WordPress Database Structure

### The 12 Core WordPress Tables

WordPress stores all content and configuration in these 12 core MySQL tables:

| # | Table | Purpose | Key Fields |
|---|-------|---------|-----------|
| 1 | `wp_posts` | Posts, pages, attachments | ID, post_title, post_content, post_type, post_status |
| 2 | `wp_postmeta` | Custom fields for posts | post_id, meta_key, meta_value |
| 3 | `wp_comments` | Comments on posts | comment_ID, post_ID, comment_content |
| 4 | `wp_commentmeta` | Custom fields for comments | comment_id, meta_key, meta_value |
| 5 | `wp_users` | User accounts | ID, user_login, user_email, user_pass |
| 6 | `wp_usermeta` | Custom user data | user_id, meta_key, meta_value |
| 7 | `wp_terms` | Categories, tags, terms | term_id, name, slug |
| 8 | `wp_term_taxonomy` | Taxonomy definitions | term_id, taxonomy, description |
| 9 | `wp_term_relationships` | Post-to-term assignments | object_id, term_taxonomy_id |
| 10 | `wp_options` | Site settings | option_id, option_name, option_value |
| 11 | `wp_links` | Blogroll links (deprecated) | link_id, link_name, link_url |
| 12 | `wp_termmeta` | Custom term fields | term_id, meta_key, meta_value |

### Understanding Table Relationships

```
┌─────────────────────────────────────────────────────┐
│                    wp_posts                         │
│ (Post content: pages, posts, attachments)          │
│ ┌──────────────────────────────────────────────┐   │
│ │ ID | post_title | post_content | post_type  │   │
│ │ 1  | "Project"  | "Details..." | "post"     │   │
│ └──────────────────────────────────────────────┘   │
└──────────────┬──────────────────────────────────────┘
               │ 1:N relationship
               ▼
        ┌─────────────────────┐
        │    wp_postmeta      │
        │ (Custom fields)     │
        │ ┌─────────────────┐ │
        │ │ post_id | key   │ │
        │ │ 1  |project_status
        │ │ 1  |project_date
        │ └─────────────────┘ │
        └─────────────────────┘

┌──────────────────────────────┐
│      wp_terms                │
│  (Categories, Tags, etc)     │
│  ┌────────────────────────┐  │
│  │ term_id | name | slug  │  │
│  │ 5   | "Portfolio" | .. │  │
│  └────────────────────────┘  │
└──────────────┬───────────────┘
               │ joins through
               ▼
┌──────────────────────────────────────┐
│ wp_term_relationships                │
│ (Posts assigned to terms)            │
│ ┌──────────────────────────────────┐ │
│ │ object_id | term_taxonomy_id     │ │
│ │ 1     | 8  (post 1 -> term 5)    │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘

┌──────────────────────────────────┐
│      wp_users                    │
│  (User accounts)                 │
│  ┌────────────────────────────┐  │
│  │ ID | user_login | user_email
│  │ 1  | admin  | admin@mail.com
│  └────────────────────────────┘  │
└────────────────┬─────────────────┘
                 │ 1:N relationship
                 ▼
        ┌─────────────────────┐
        │    wp_usermeta      │
        │ (User metadata)     │
        │ ┌─────────────────┐ │
        │ │ user_id | key   │ │
        │ │ 1  |role
        │ │ 1  |nickname
        │ └─────────────────┘ │
        └─────────────────────┘
```

---

## Post Types & Post Meta

### Understanding Posts

A **post** is any content in WordPress. The `post_type` field determines what it is:
- `post` - Blog posts
- `page` - Static pages
- `attachment` - Images, videos, files
- `custom` - Custom post types (created by plugins)

### Creating Posts Programmatically

```php
$post_id = wp_insert_post( [
    'post_title'     => 'Project 1',
    'post_content'   => 'This is project 1 description',
    'post_type'      => 'post',
    'post_status'    => 'publish',  // or 'draft', 'pending', etc.
    'post_author'    => 1,          // User ID
    'post_date'      => '2025-02-15 10:00:00',
] );
```

### Understanding Post Meta

Post meta stores custom field data for each post. Think of it as key-value pairs:

```
wp_posts (ID: 1)
  ├─ postmeta: project_status = "completed"
  ├─ postmeta: project_date = "2025-02-15"
  ├─ postmeta: project_tags = "wordpress,php"
  └─ postmeta: project_priority = 1
```

### Working with Post Meta

```php
// Add a meta field
add_post_meta( $post_id, 'project_status', 'completed' );

// Get a meta field
$status = get_post_meta( $post_id, 'project_status', true );

// Update a meta field
update_post_meta( $post_id, 'project_status', 'in-progress' );

// Delete a meta field
delete_post_meta( $post_id, 'project_status' );

// Get all meta for a post
$all_meta = get_post_meta( $post_id );
```

### Querying Posts with WP_Query

`WP_Query` is the main class for retrieving posts. It's used throughout WordPress and plugins.

```php
// Get all published posts
$query = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 10,
    'paged'          => 1,
] );

// Filter by post meta
$query = new WP_Query( [
    'post_type'      => 'post',
    'meta_key'       => 'project_status',
    'meta_value'     => 'completed',
] );

// Filter by multiple meta values
$query = new WP_Query( [
    'post_type'  => 'post',
    'meta_query' => [
        [
            'key'     => 'project_status',
            'value'   => 'completed',
            'compare' => '='
        ],
        [
            'key'     => 'project_priority',
            'value'   => [ 1, 2 ],
            'compare' => 'IN'
        ]
    ],
    'meta_compare' => 'AND'  // or 'OR'
] );

// Order by post meta
$query = new WP_Query( [
    'post_type'      => 'post',
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'project_priority',
    'order'          => 'ASC',  // or 'DESC'
] );
```

**Database Explorer Implementation:**
See `database-explorer.php` lines 80-110 for the WP_Query example that filters posts by "project_status" meta.

---

## Taxonomies (Categories, Tags)

### Understanding Taxonomies

Taxonomies are ways to organize posts into groups:
- **Categories** - Hierarchical (can have parent/child)
- **Tags** - Non-hierarchical
- **Custom Taxonomies** - Created by plugins

### The Taxonomy Tables

Three tables work together:

1. **wp_terms** - The actual term (category/tag name)
   ```
   term_id | name | slug | term_group
   5       | Portfolio | portfolio | 0
   ```

2. **wp_term_taxonomy** - Defines the taxonomy type
   ```
   term_taxonomy_id | term_id | taxonomy | parent | count
   8                | 5       | category | 0      | 3
   ```

3. **wp_term_relationships** - Which posts are in which terms
   ```
   object_id | term_taxonomy_id
   1         | 8   (post 1 is in category 5)
   2         | 8   (post 2 is in category 5)
   ```

### Creating and Assigning Terms

```php
// Create a term (category)
$result = wp_insert_term(
    'Portfolio',           // Term name
    'category',            // Taxonomy
    [
        'slug'        => 'portfolio',
        'description' => 'Portfolio projects'
    ]
);

// Check for errors
if ( ! is_wp_error( $result ) ) {
    $term_id = $result['term_id'];

    // Assign the term to a post
    wp_set_post_terms( $post_id, $term_id, 'category' );
}
```

### Querying Terms

```php
// Get all categories
$terms = get_terms( [
    'taxonomy'   => 'category',
    'hide_empty' => false,  // Show even if no posts
] );

// Iterate through terms
foreach ( $terms as $term ) {
    echo $term->name;           // Term name
    echo $term->slug;           // URL slug
    echo $term->term_id;        // WordPress ID
    echo $term->count;          // Number of posts
    echo $term->description;    // Term description
}

// Get categories for a specific post
$post_categories = wp_get_post_categories( $post_id, [ 'fields' => 'all' ] );

// Get terms for a specific post
$post_terms = wp_get_post_terms( $post_id, 'category' );
```

### Term Relationships

When you assign a post to a category, WordPress creates a relationship:

```php
// Assign post 1 to category 5
wp_set_post_terms( 1, 5, 'category' );

// This creates a row in wp_term_relationships:
// object_id: 1
// term_taxonomy_id: (the ID for category 5)
```

**Database Explorer Implementation:**
See `database-explorer.php` lines 135-165 where terms are queried and displayed.

---

## Users & Capabilities

### Understanding Users

WordPress users are stored in `wp_users` table with basic info:
- `ID` - User ID
- `user_login` - Username
- `user_email` - Email address
- `user_registered` - Registration date
- `user_status` - User status (0 = active)

User role and capabilities are stored in `wp_usermeta` table as serialized data.

### User Roles and Capabilities

**Roles** are pre-defined sets of capabilities:
- `administrator` - Full access
- `editor` - Can publish posts, edit all posts
- `author` - Can write their own posts
- `contributor` - Can write posts (not publish)
- `subscriber` - Can only read

**Capabilities** are individual permissions:
- `edit_posts` - Can edit posts
- `publish_posts` - Can publish posts
- `manage_options` - Can access admin settings
- `delete_pages` - Can delete pages
- Custom capabilities created by plugins

### Working with Users

```php
// Get all users
$users = get_users();

// Get a specific user
$user = get_user_by( 'login', 'admin' );
$user = get_user_by( 'email', 'admin@example.com' );

// Get user data
$username = $user->user_login;
$email = $user->user_email;
$id = $user->ID;

// Get user role
$role = $user->roles[0];  // First role

// Get all roles for a user
$roles = $user->roles;  // Array of role names
```

### User Capabilities

```php
// Check if user has a capability
if ( user_can( $user_id, 'edit_posts' ) ) {
    // User can edit posts
}

// Check if current logged-in user has capability
if ( current_user_can( 'manage_options' ) ) {
    // User is an admin
}

// Get all capabilities for a user
$user = get_user_by( 'login', 'admin' );
$capabilities = $user->allcaps;
```

### User Meta

```php
// Get user meta
$nickname = get_user_meta( $user_id, 'nickname', true );
$description = get_user_meta( $user_id, 'description', true );

// Update user meta
update_user_meta( $user_id, 'phone', '555-1234' );

// Add user meta
add_user_meta( $user_id, 'company', 'My Company' );
```

**Database Explorer Implementation:**
See `database-explorer.php` lines 180-210 where users are queried and capabilities displayed.

---

## Options & Settings

### Understanding WordPress Options

`wp_options` table stores site-wide settings as key-value pairs:

```
option_id | option_name | option_value
1         | siteurl     | http://localhost:8888
2         | home        | http://localhost:8888
3         | blogname    | My Blog
...
```

Options are used for:
- Theme settings
- Plugin settings
- WordPress core settings (site URL, admin email, etc.)

### Using get_option() and update_option()

```php
// Get an option
$site_url = get_option( 'siteurl' );
$blog_title = get_option( 'blogname' );

// Get with default value
$custom_value = get_option( 'my_plugin_setting', 'default' );

// Update or create option
update_option( 'my_plugin_setting', 'new value' );

// Delete option
delete_option( 'my_plugin_setting' );

// Check if option exists
if ( get_option( 'my_option' ) !== false ) {
    // Option exists
}
```

### Serialized Options

Options can store complex data (arrays, objects) which are serialized:

```php
// Store array as option
$settings = [
    'theme' => 'blue',
    'layout' => 'sidebar',
    'posts_per_page' => 10
];
update_option( 'my_plugin_settings', $settings );

// Retrieve array from option
$settings = get_option( 'my_plugin_settings' );
echo $settings['theme'];  // Output: blue
```

### Autoload Options

By default, options are "autoloaded" (loaded on every WordPress request). For better performance:

```php
// Create non-autoloaded option
add_option( 'heavy_data', $data, '', 'no' );

// Load it only when needed
$heavy_data = get_option( 'heavy_data' );
```

**Database Explorer Implementation:**
See `database-explorer.php` lines 40-47 where plugin options are created, and lines 120-130 where they're displayed.

---

## Key Functions Reference

### Post Functions

| Function | Purpose |
|----------|---------|
| `wp_insert_post()` | Create a new post |
| `wp_update_post()` | Update an existing post |
| `get_post()` | Retrieve a post |
| `get_posts()` | Get posts (simple query) |
| `new WP_Query()` | Complex post queries |
| `wp_delete_post()` | Delete a post |

### Post Meta Functions

| Function | Purpose |
|----------|---------|
| `add_post_meta()` | Add post custom field |
| `get_post_meta()` | Get post custom field |
| `update_post_meta()` | Update post custom field |
| `delete_post_meta()` | Delete post custom field |

### Term Functions

| Function | Purpose |
|----------|---------|
| `wp_insert_term()` | Create a term |
| `get_terms()` | Get terms |
| `wp_set_post_terms()` | Assign term to post |
| `wp_get_post_terms()` | Get terms for post |
| `wp_get_post_categories()` | Get categories for post |
| `get_term()` | Get single term |

### User Functions

| Function | Purpose |
|----------|---------|
| `get_users()` | Get all users |
| `get_user_by()` | Get user by login/email |
| `get_user_meta()` | Get user custom field |
| `update_user_meta()` | Update user field |
| `user_can()` | Check user capability |
| `current_user_can()` | Check current user capability |

### Option Functions

| Function | Purpose |
|----------|---------|
| `get_option()` | Get option value |
| `update_option()` | Update option value |
| `add_option()` | Add new option |
| `delete_option()` | Delete option |

---

## Learning Objectives Checklist

- [x] **Understand the 12 core WordPress tables** - See "The 12 Core WordPress Tables" section
- [x] **Learn purpose of key tables** - Each table explained in detail
- [x] **Understand wp_options table** - See "Options & Settings" section
- [x] **Learn wp_terms relationships** - See "Understanding Taxonomies" and table diagram
- [x] **Understand built-in post types** - See "Understanding Posts" section
- [x] **Learn WP_Query** - Comprehensive section with multiple examples
- [x] **Understand post meta** - Dedicated section with examples
- [x] **Learn add_post_meta/get_post_meta** - Multiple examples provided
- [x] **Understand taxonomy structure** - Complete section with SQL relationships
- [x] **Learn term relationships** - Term relationship diagram and code examples
- [x] **Understand categories vs tags vs custom** - All types explained
- [x] **Learn get_terms()** - Complete example with output
- [x] **Understand user roles** - Comprehensive role/capability section
- [x] **Learn user meta** - get_user_meta, update_user_meta examples
- [x] **Understand current_user_can()** - Capability checking explained
- [x] **Learn wp_options usage** - Complete get_option/update_option guide

---

## Database Explorer Plugin Code

The Database Explorer plugin demonstrates all these concepts:

### Main Plugin File: `database-explorer.php`

**Lines 27-75:** Activation hook creating sample data
- `wp_insert_term()` - Create Portfolio category
- `wp_insert_post()` - Create sample projects
- `add_post_meta()` - Add custom fields
- `wp_set_post_terms()` - Assign to category
- `update_option()` - Store plugin settings

**Lines 95-115:** Admin menu registration
- Demonstrates custom admin pages

**Lines 127-145:** Display options
- `get_option()` - Retrieve stored settings

**Lines 147-180:** WP_Query with meta filtering
- Complex query ordering by meta
- Displaying filtered results

**Lines 182-210:** Display terms and relationships
- `get_terms()` - Retrieve all terms
- Showing term relationships

**Lines 212-240:** Display users and capabilities
- `get_users()` - Get all users
- `user_can()` - Check capabilities

---

## Security Notes

### Data Validation
Always validate and sanitize input:
```php
$value = sanitize_text_field( $_POST['field'] );
$url = esc_url( $_POST['url'] );
```

### Capability Checking
Always check user capabilities before database operations:
```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized' );
}
```

### Escaping Output
Always escape data before output:
```php
echo esc_html( $post->post_title );
echo esc_attr( $attribute );
echo wp_kses_post( $html_content );
```

---

## Next Steps

1. **Activate the plugin** in WordPress admin
2. **Visit Database Explorer page** to see live examples
3. **Review the source code** in `database-explorer.php`
4. **Run the automated tests** to verify functionality
5. **Study the relationships** between tables
6. **Create your own queries** using WP_Query

This plugin is your reference for all WordPress database operations!
