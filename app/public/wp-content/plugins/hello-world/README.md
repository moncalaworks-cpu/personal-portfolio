# Hello World Plugin

A simple WordPress plugin that creates a "Hello World" page to demonstrate core WordPress development concepts.

## What This Plugin Teaches

### Plugin Structure
- **Plugin Header Comments** - Plugin metadata and information
- **Plugin Files** - Basic plugin file organization
- **Text Domain** - Internationalization setup

### WordPress Hooks & Actions
- **init Hook** - Plugin initialization and post type registration
- **wp_enqueue_scripts** - Frontend resource management
- **the_content Filter** - Content modification and filtering

### WordPress Functions Demonstrated
- `add_action()` - Register functions to hooks
- `add_filter()` - Modify content through filters
- `wp_insert_post()` - Create posts/pages programmatically
- `get_page_by_title()` - Query pages by title
- `wp_enqueue_style()` - Load stylesheets properly
- `is_page()` - Conditional checks
- `register_activation_hook()` - Plugin lifecycle management
- `register_deactivation_hook()` - Cleanup on deactivation
- `plugin_dir_url()` - Get plugin directory path

### Key Concepts
1. **Direct File Access Prevention** - Security practice with `ABSPATH` check
2. **Plugin Lifecycle** - Activation and deactivation hooks
3. **Frontend Resource Management** - Proper stylesheet enqueueing
4. **Content Filtering** - Modifying output with filters
5. **Post Creation** - Programmatic page creation

## How It Works

1. **Activation** - When activated, creates a page titled "Hello World"
2. **Initialization** - Registers the page on the init hook
3. **Styling** - Enqueues custom CSS for the Hello World page
4. **Display** - Filters page content to add custom styling and messaging

## Files

- `hello-world.php` - Main plugin file with all functionality
- `css/hello-world.css` - Custom styling for the Hello World page
- `README.md` - This documentation

## Installation

1. This plugin is already installed in `wp-content/plugins/hello-world/`
2. Activate via WordPress admin or CLI

## Usage

Once activated:
1. A page titled "Hello World" will be created automatically
2. Visit the Hello World page to see the plugin in action
3. The page displays with custom styling and messaging

## Learning Notes

- Check `/debug.log` for activation/deactivation messages
- This demonstrates the "hooks and filters" architecture that powers WordPress
- All output is escaped and sanitized for security
- The plugin follows WordPress Coding Standards
