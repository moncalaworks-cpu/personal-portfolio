# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **WordPress portfolio site** running locally via **Local by Flywheel**. The site structure follows Local's standard environment setup with development server configurations (nginx, PHP, MySQL).

- **WordPress Root**: `/app/public/` - Contains all WordPress core files and content
- **Server Config**: `/conf/` - Local by Flywheel configuration for nginx, PHP, MySQL (should not need editing)
- **Database/Logs**: `/logs/` - Generated logs directory

## WordPress Site Structure

```
app/public/
├── wp-content/           # User-editable WordPress content
│   ├── themes/           # Theme files (active site templates)
│   ├── plugins/          # Plugin files (currently empty)
│   └── uploads/          # Media uploads
├── wp-admin/             # WordPress admin interface (don't edit)
├── wp-includes/          # WordPress core files (don't edit)
├── wp-config.php         # WordPress configuration
└── index.php             # WordPress entry point
```

**Important**: The `wp-admin/`, `wp-includes/`, and core WordPress files should never be edited directly. Make changes via:
- Custom theme files in `wp-content/themes/`
- Custom plugins in `wp-content/plugins/`
- Theme customization through the admin interface
- Code changes in a custom child theme or plugin

## Common Tasks

### Working with Themes

Active themes are in `/app/public/wp-content/themes/`. Currently using one of the default WordPress themes (twentytwentyfive, twentytwentyfour, or twentytwentythree).

To create a custom child theme or modify the active theme:
1. Edit files directly in the theme's directory
2. Any PHP changes take effect immediately (no build needed)
3. CSS/JS changes are cached by WordPress - may need to refresh browser or clear cache via admin

### Creating Custom Plugins

Plugins go in `/app/public/wp-content/plugins/`. Each plugin is a directory with a main PHP file.

Plugin structure:
```
wp-content/plugins/my-plugin/
├── my-plugin.php         # Main plugin file with plugin header
└── includes/             # Helper files (optional)
```

### Database Access

- Database: `local`
- Username: `root`
- Password: `root`
- Host: `localhost`

Via Local's admin: Use the Database Manager to browse/query the WordPress database.

### WordPress Debugging

Debugging is currently disabled in `wp-config.php`:
```php
define( 'WP_DEBUG', false );
```

To enable debugging during development:
1. Edit `/app/public/wp-config.php`
2. Change `WP_DEBUG` to `true` and add:
   ```php
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', false );
   ```
3. Errors will appear in `/app/public/wp-content/debug.log`

### Local by Flywheel Commands

Common Local commands (run from Terminal):
```bash
# Start/stop the site
local start
local stop

# SSH into the WordPress container
local shell

# Run WP-CLI commands
local wp --help
local wp post list
local wp plugin activate my-plugin
```

## File Editing Guidelines

- **Theme files** (PHP, CSS, JS): Edit directly in `/app/public/wp-content/themes/`
- **Plugin files**: Edit in `/app/public/wp-content/plugins/`
- **wp-config.php**: Can be edited but only for debugging/configuration constants
- **WordPress core files** (wp-admin, wp-includes): Should never be modified - Local manages these

## Architecture Notes

This is a standard WordPress installation. Key architectural concepts:
- **No version control**: This site is not a git repository, so changes are file-based only
- **Local environment only**: Built for local development, not designed for production
- **Standard WordPress hooks/filters**: Use WordPress actions and filters for extensibility, not direct core edits
- **Custom code goes in themes/plugins**: Always extend WordPress through proper plugin/theme architecture

## Performance Considerations

- WordPress caching may need to be cleared after code changes (browser cache or admin cache clear)
- No build process - changes to PHP take effect immediately on page reload
- CSS/JS changes require browser cache clear (Ctrl+Shift+R or Cmd+Shift+R)
