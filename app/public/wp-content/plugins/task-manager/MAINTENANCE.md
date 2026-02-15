# Task Manager Plugin - Maintenance & Best Practices (v1.1.0)

This document describes the maintenance infrastructure, version management, and best practices implemented in Task Manager v1.1.0.

## Overview

Task Manager v1.1.0 introduces enterprise-grade features for long-term maintenance:

- **Version Management**: Track database schema version separately from plugin version
- **Database Migrations**: Safe schema changes without data loss
- **Performance Caching**: Reduce database load with intelligent caching
- **Comprehensive Logging**: Track operations for debugging and monitoring
- **Code Quality**: Full WordPress Coding Standards compliance
- **Dependency Checking**: Graceful handling of incompatible environments

## Version Management

### How It Works

The plugin maintains two version numbers:

1. **Plugin Version** (`TM_VERSION`): Software version (e.g., 1.1.0)
2. **Database Version** (`tm_db_version` option): Database schema version (e.g., 1.1.0)

### Version Checking

On plugin activation, the system:

1. Checks if `tm_db_version` exists
2. Compares with `TM_VERSION`
3. Runs migrations if versions differ
4. Updates `tm_db_version` after each migration

### Example

```php
// Check if database needs upgrading
if ( TaskManager\Migrator::needs_upgrade() ) {
    $result = TaskManager\Migrator::run_migrations();
    // result: ['success' => true, 'messages' => [...]]
}

// Get version information
$current = TaskManager\Migrator::get_current_version();  // '1.1.0'
$installed = TaskManager\Migrator::get_installed_version(); // '1.0.0'
```

## Database Migrations

### Creating a New Migration

1. Create a new file in `includes/migrations/class-migration-XXX.php`
2. Extend `Migration_Base` class
3. Implement `up()`, `down()`, `version()`, and `description()` methods

### Example Migration

```php
namespace TaskManager\Migrations;

class Migration_120 extends Migration_Base {
    public function up() {
        // Add new column
        if ( ! $this->add_column( 'tm_tasks', 'updated_by', 'bigint(20) UNSIGNED' ) ) {
            return false;
        }

        // Add index for performance
        if ( ! $this->add_index( 'tm_tasks', 'updated_by', [ 'updated_by' ] ) ) {
            return false;
        }

        return true;
    }

    public function down() {
        // Rollback: remove the column
        return $this->remove_column( 'tm_tasks', 'updated_by' );
    }

    public function version() {
        return '1.2.0';
    }

    public function description() {
        return 'Track who last updated each task';
    }
}
```

### Register Migration

Add to `class-migrator.php`:

```php
private static function get_pending_migrations() {
    $installed = self::get_installed_version();
    $all_migrations = [
        '1.1.0' => 'TaskManager\Migrations\Migration_110',
        '1.2.0' => 'TaskManager\Migrations\Migration_120', // Add new migration
    ];
    // ... rest of implementation
}
```

### Helper Methods

The `Migration_Base` class provides helpers:

- `add_column($table, $column, $definition)`
- `remove_column($table, $column)`
- `add_index($table, $index_name, $columns)`
- `remove_index($table, $index_name)`
- `execute($sql)` - Raw SQL execution

## Performance Caching

### Architecture

The plugin uses WordPress transients for caching:

- **Transients**: Persistent cache (survives page reloads)
- **TTL**: Configurable expiration (default: 1 hour for stats, 5 minutes for lists)
- **Invalidation**: Automatic on task CRUD operations

### Usage

```php
// Get cached value
$stats = TaskManager\Cache::get_statistics();
if ( false === $stats ) {
    // Not cached, get from database
    $stats = $db->get_statistics();
    // Cache for 1 hour
    TaskManager\Cache::set_statistics( $stats );
}

// Manually invalidate cache
TaskManager\Cache::invalidate_task( $task_id );

// Clear all cache
TaskManager\Cache::flush();
```

### Cache Keys

All cache keys use format: `{group}_{key}`

- Group: `task-manager` (configured constant)
- Keys: `statistics`, `recent_tasks`, `task_{id}`, etc.

## Logging & Debugging

### Enable Logging

Add to `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );  // Don't display errors
```

Logs go to `wp-content/debug.log`

### Log Levels

```php
TaskManager\Logger::error( 'Critical issue', ['task_id' => 123] );
TaskManager\Logger::warning( 'Deprecated usage', ['function' => 'old_func'] );
TaskManager\Logger::info( 'Task created', ['user_id' => 1] );
TaskManager\Logger::debug( 'Query executed', ['sql' => '...'] );  // Debug mode only
```

### Log Format

```
[2026-02-15 14:23:45] [TASK-MANAGER] [INFO] Task created | {"user_id":1} | User: admin (1)
[2026-02-15 14:23:46] [TASK-MANAGER] [ERROR] Database error | {"code":"no_table"} | User: admin (1)
[2026-02-15 14:23:47] [TASK-MANAGER] [DEBUG] Cache hit | {"key":"statistics"} | User: admin (1) | database.php:145 in get_statistics()
```

### Sensitive Data

The logger automatically redacts:

- `password`
- `token`
- `secret`
- `api_key`
- `auth`
- `key`

These are logged as `***REDACTED***`

## Dependency Management

### Minimum Requirements

```php
define( 'TM_MIN_PHP', '7.4.0' );
define( 'TM_MIN_WP', '5.8.0' );
```

### Requirement Checking

The plugin checks requirements on load:

1. Compares PHP version with minimum
2. Compares WordPress version with minimum
3. Shows admin notice if requirements not met
4. Gracefully deactivates if incompatible

### Custom Notices

Add to admin pages:

```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have permission.' );
}
```

## Code Quality & Standards

### WordPress Coding Standards

Run validation:

```bash
vendor/bin/phpcs --standard=WordPress --extensions=php .
```

Configuration in `phpcs.xml`

### PHPDoc Comments

All classes, methods, and functions documented:

```php
/**
 * Get task statistics
 *
 * Returns cached statistics with task counts by status
 *
 * @param int $user_id Optional user ID to filter tasks
 *
 * @return array Array of statistics
 *
 * @since 1.0.0
 */
public function get_statistics( $user_id = null ) {
```

## Backward Compatibility

### Strategy

The plugin maintains backward compatibility:

1. **Never Remove Columns**: Mark deprecated instead
2. **Graceful Degradation**: Old code still works
3. **Migration Path**: Data is transformed, not lost
4. **API Stability**: Public methods don't change signatures

### Example

```php
// Old code still works (v1.0.0 style)
$task = TaskManager\Database::get_instance()->get_task( 123 );

// New code with more features (v1.1.0+)
$stats = TaskManager\Cache::get_statistics();
```

## Testing

### Run Tests

```bash
npm test -- task-manager-maintenance.spec.ts
```

### Test Coverage

- 25 tests covering all maintenance features
- Plugin activation and version management
- Database operations and caching
- Security and validation
- Performance and load times
- Code quality and standards

## Deployment

### Updating from v1.0.0 to v1.1.0

1. Backup database
2. Upload new plugin files
3. Go to Plugins page
4. Deactivate and reactivate plugin
5. Migrations run automatically
6. Check `wp-content/debug.log` for migration status

### Upgrade Checklist

- [ ] Backup database
- [ ] Test in staging environment
- [ ] Review CHANGELOG.md
- [ ] Check log files for migration success
- [ ] Verify all tasks display correctly
- [ ] Test task creation/editing
- [ ] Verify settings still accessible

## Future Enhancements

### Planned for v1.2.0+

- [ ] Debug admin page with detailed logging
- [ ] Cache statistics display and management
- [ ] Automated database optimization
- [ ] Task history/audit log
- [ ] Performance profiling tools
- [ ] REST API endpoints

## Support

For issues or questions:

1. Check `wp-content/debug.log` for error details
2. Review CHANGELOG.md for known issues
3. Verify minimum requirements are met
4. Check Task Manager dashboard for warnings

## References

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Database Migrations](https://developer.wordpress.org/reference/functions/dbdelta/)
- [Transients API](https://developer.wordpress.org/apis/transients/)
