# WordPress Development with WP-CLI
**Purpose:** Command-line WordPress management for development, testing, and automation
**Last Updated:** 2026-02-15
**Confidence:** HIGH (industry standard approach)

---

## What is WP-CLI?

**WP-CLI** = Command-line interface for WordPress management without touching the admin UI.

**Installation:**
```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

**Verify:**
```bash
wp --version
```

---

## Core Operations

### WordPress Installation & Setup

```bash
# Install WordPress
wp core install --url=http://mysite.local --title="My Site" \
  --admin_user=admin --admin_password=password --admin_email=admin@example.com

# Update WordPress core
wp core update

# Verify installation
wp core verify-checksums
wp cli info
```

### Database Management

```bash
# Optimize database
wp db optimize

# Check database integrity
wp db check

# Export database (backup)
wp db export backup-$(date +%Y%m%d-%H%M%S).sql

# Import database (restore)
wp db import backup-20260215-143022.sql

# Delete all posts of a type
wp post delete $(wp post list --post_type=page --format=ids) --force

# Delete specific pages by slug
wp post delete $(wp post list --post_type=page --post_name=my-page --format=ids) --force

# Clear transients (cached data)
wp transient delete --all
```

---

## Plugin Management

### Basic Operations

```bash
# Install a plugin
wp plugin install woocommerce

# Activate/deactivate
wp plugin activate my-plugin
wp plugin deactivate my-plugin

# List plugins with status
wp plugin list

# Delete a plugin
wp plugin delete my-plugin

# List all available commands for plugins
wp help plugin
```

### Testing Activation/Deactivation Cycles

```bash
#!/bin/bash
# test-plugin-cycle.sh - Test plugin activation/deactivation

PLUGIN_NAME="my-portfolio-plugin"
WP_PATH="/path/to/wordpress"

echo "🧪 Testing $PLUGIN_NAME activation cycle..."

cd "$WP_PATH"

# 1. Clean state - deactivate
wp plugin deactivate "$PLUGIN_NAME" 2>/dev/null || true
echo "✅ Plugin deactivated"

# 2. Check for orphaned pages
echo "Checking for orphaned pages..."
wp post list --post_type=page --format=table

# 3. Activate plugin
echo "🔧 Activating plugin..."
wp plugin activate "$PLUGIN_NAME"
echo "✅ Plugin activated"

# 4. Check what was created
echo "Pages after activation:"
wp post list --post_type=page --format=table

# 5. Deactivate and verify cleanup
echo "🧹 Deactivating plugin..."
wp plugin deactivate "$PLUGIN_NAME"
echo "✅ Plugin deactivated"

# 6. Verify cleanup worked
echo "Pages after deactivation (should be empty):"
wp post list --post_type=page --format=table

echo ""
echo "✅ Test cycle complete!"
```

---

## Theme Management

```bash
# Install theme
wp theme install twentytwentyfour

# Activate theme
wp theme activate twentytwentyfour

# List themes
wp theme list
```

---

## Content Management

### Posts & Pages

```bash
# Create a page
wp post create --post_type=page --post_title="My Page" \
  --post_content="Page content here" --post_status=publish

# List pages with details
wp post list --post_type=page --format=table

# Get page by slug
wp post get $(wp post list --post_type=page --post_name=my-page --format=ids)

# Update page
wp post update 123 --post_title="New Title"

# Delete page (trash)
wp post delete 123

# Permanently delete page
wp post delete 123 --force
```

### Custom Post Types

```bash
# List posts of custom type
wp post list --post_type=portfolio_item --format=table

# Count posts by type
wp post list --post_type=custom_type --format=count
```

---

## User Management

```bash
# Create user
wp user create testuser test@example.com --role=editor --user_pass=password

# List users
wp user list --format=table

# Get user by ID
wp user get 1

# Delete user
wp user delete 123 --reassign=admin

# Update user
wp user update 1 --user_email=newemail@example.com
```

---

## Complete Development Workflow

### Setup Phase

```bash
# 1. Install WordPress with test data
wp core install --url=http://personal-portfolio.local \
  --title="Personal Portfolio" \
  --admin_user=admin --admin_password=dev --admin_email=dev@local

# 2. Install debugging plugins
wp plugin install debug-bar --activate
wp plugin install query-monitor --activate

# 3. Create test content
wp post create --post_type=page --post_title="Test Page" \
  --post_content="Test content" --post_status=publish

# 4. List what we have
wp post list --post_type=page --format=table
```

### Development Phase

```bash
# 1. Make changes to plugin code
# ... edit plugin files ...

# 2. Test activation
wp plugin deactivate personal-portfolio-plugin || true
wp plugin activate personal-portfolio-plugin

# 3. Check what plugin created
wp post list --post_type=page --format=table

# 4. Run database checks
wp db check

# 5. Monitor error logs
tail -f /path/to/wordpress/wp-content/debug.log
```

### Testing Phase - Automated Test Script

```bash
#!/bin/bash
# automated-plugin-test.sh

PLUGIN="personal-portfolio-plugin"
WP_PATH="/Users/kenshinzato/Local Sites/personal-portfolio"

cd "$WP_PATH"

echo "🧪 Running automated tests for $PLUGIN..."

# Test 1: Plugin activates without errors
echo "Test 1: Plugin activation..."
wp plugin activate "$PLUGIN" 2>&1 | tee test-output.log
if [ ${PIPESTATUS[0]} -eq 0 ]; then
  echo "✅ Plugin activation successful"
else
  echo "❌ Plugin activation failed"
  exit 1
fi

# Test 2: Expected pages created
echo "Test 2: Pages created..."
PAGE_COUNT=$(wp post list --post_type=page --format=count)
if [ "$PAGE_COUNT" -gt 0 ]; then
  echo "✅ Pages created: $PAGE_COUNT"
else
  echo "❌ No pages created"
  exit 1
fi

# Test 3: Check for errors in logs
echo "Test 3: Error checking..."
ERROR_COUNT=$(grep -ic "error\|fatal\|warning" /path/to/debug.log || true)
if [ "$ERROR_COUNT" -eq 0 ]; then
  echo "✅ No errors in logs"
else
  echo "⚠️  Found $ERROR_COUNT warnings/errors"
fi

# Test 4: Deactivation and cleanup
echo "Test 4: Cleanup on deactivation..."
wp plugin deactivate "$PLUGIN"
REMAINING=$(wp post list --post_type=page --format=count)
if [ "$REMAINING" -eq 0 ]; then
  echo "✅ Cleanup successful - all pages removed"
else
  echo "❌ Cleanup failed - $REMAINING pages remain"
  exit 1
fi

echo ""
echo "✅ All tests passed!"
```

---

## Best Practices for Plugin Development

### Testing Page Creation on Activation

**Problem:** Plugin creates pages on activation, but doesn't clean them up properly.

**Solution with WP-CLI:**

1. **Verify plugin creates pages correctly:**
   ```bash
   wp plugin activate my-plugin
   wp post list --post_type=page --format=table
   ```

2. **Verify deactivation cleanup:**
   ```bash
   wp plugin deactivate my-plugin
   wp post list --post_type=page --format=table  # Should be empty
   ```

3. **Test multiple activation/deactivation cycles:**
   ```bash
   for i in {1..5}; do
     wp plugin activate my-plugin
     wp plugin deactivate my-plugin
   done
   # No orphaned pages should accumulate
   ```

### Database Snapshots for Quick Rollback

```bash
#!/bin/bash
# save-snapshot.sh

SNAPSHOT_NAME="before-plugin-test-$(date +%Y%m%d-%H%M%S)"
wp db export "snapshots/$SNAPSHOT_NAME.sql"
echo "✅ Snapshot saved: snapshots/$SNAPSHOT_NAME.sql"

# Restore later:
# wp db import snapshots/before-plugin-test-20260215-143022.sql
```

---

## Advanced: Scripting & Automation

### Run PHP in WordPress Context

```bash
# Execute PHP with WordPress loaded
wp eval 'echo get_option("siteurl");'

# Run a script file
wp eval-file script.php

# Access WordPress functions
wp eval 'print_r(get_posts(["post_type" => "page"]));'
```

### Batch Operations

```bash
# Update all pages with specific meta
wp post list --post_type=page --format=ids | \
  xargs -I {} wp post meta update {} "_test_flag" "true"

# Delete all pages created by plugin
wp post delete $(wp post list --post_type=page \
  --meta_key=_created_by_plugin \
  --meta_value=my-plugin \
  --format=ids) --force
```

### Integration with Git/CI

```bash
#!/bin/bash
# pre-commit.sh - Test plugin before committing

echo "Testing plugin before commit..."

# Ensure clean database state
wp db import backup-clean.sql

# Test plugin
wp plugin activate my-plugin
if wp post list --post_type=page --format=count | grep -q "^[1-9]"; then
  echo "✅ Plugin works"
else
  echo "❌ Plugin failed"
  exit 1
fi

# Allow commit
exit 0
```

---

## Useful Help Commands

```bash
# Get help on any command
wp help post create
wp help plugin activate
wp help db

# List all available commands
wp cli commands

# Get WordPress system info
wp cli info

# Check for conflicts/issues
wp doctor diagnose
```

---

## Common Issues & Solutions

### Issue: "Error: Site not properly set up"
**Solution:** WP-CLI can't find WordPress installation
```bash
# Navigate to WordPress root directory
cd /path/to/wordpress
wp cli info  # Should work now
```

### Issue: "Error: No such post"
**Solution:** Post doesn't exist
```bash
# List all posts first
wp post list --format=table

# Use the correct post ID
wp post get 123
```

### Issue: Plugin activation fails silently
**Solution:** Check error logs
```bash
# Enable debug mode
wp config set WP_DEBUG true

# Check logs
tail -f wp-content/debug.log

# Activate plugin with output
wp plugin activate my-plugin -v
```

---

## Integration with Personal-Portfolio Project

**Example setup for personal-portfolio plugin:**

```bash
#!/bin/bash
# personal-portfolio-setup.sh

WP_PATH="/Users/kenshinzato/Local Sites/personal-portfolio"
cd "$WP_PATH"

# Install WordPress if needed
if ! wp core is-installed; then
  wp core install --url=http://personal-portfolio.local \
    --title="Personal Portfolio" \
    --admin_user=admin --admin_password=dev --admin_email=dev@local
fi

# Activate plugin
wp plugin activate personal-portfolio-plugin

# Create portfolio page if it doesn't exist
if ! wp post list --post_type=page --post_name=portfolio --format=ids | grep -q .; then
  wp post create --post_type=page --post_title="Portfolio" \
    --post_content="[portfolio_showcase]" --post_status=publish
fi

echo "✅ Personal portfolio setup complete!"
```

---

## Status

✅ **WP-CLI guide created:** 2026-02-15
✅ **Testing patterns documented:** Multiple scenarios covered
✅ **Integration examples provided:** For plugin development
✅ **Ready for reference:** Across all WordPress projects

---

**Key Takeaway:** WP-CLI enables repeatable, scriptable testing without touching the WordPress admin. Essential for plugin development and CI/CD automation.
