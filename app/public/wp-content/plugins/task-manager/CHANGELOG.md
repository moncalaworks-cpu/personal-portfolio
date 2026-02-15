# Changelog

All notable changes to the Task Manager plugin are documented in this file.

## [1.1.0] - 2026-02-15

### Added
- Database migration system with Migration_Base class
- Version tracking: separate database version from plugin version
- Comprehensive logging system (ERROR, WARNING, INFO, DEBUG levels)
- Logging respects WP_DEBUG and WP_DEBUG_LOG constants
- Cache wrapper class for performance optimization
- Transient-based caching for task statistics and recent tasks
- Cache invalidation on task create/update/delete
- WordPress coding standards compliance (phpcs configuration)
- PHP/WordPress requirement checking on plugin load
- Dependency validation with graceful deactivation

### Changed
- Updated plugin version to 1.1.0
- Enhanced plugin description with new features
- Activator now initializes database version tracking

### Technical Details
- Minimum PHP version: 7.4.0
- Minimum WordPress version: 5.8.0
- Migration to v1.1.0 adds `completed_at` column to track task completion time
- All new classes follow PSR-4 autoloading standards
- Full prepared statement usage for all database queries

## [1.0.0] - 2026-02-15

### Added
- Initial plugin release
- Task Manager admin interface with dashboard
- Task creation, reading, updating, and deletion (CRUD)
- Custom database table (wp_tm_tasks) with proper indexes
- Settings API implementation
- Security features: nonces, sanitization, escaping, capability checks
- Custom capabilities: manage_tasks, create_tasks, edit_tasks, delete_tasks
- Admin pages: Dashboard, Task List, Add/Edit Task, Settings
- Input validation with custom Validator class
- Comprehensive automated tests with Playwright

### Technical Implementation
- Class-based architecture with PSR-4 autoloading
- Direct wpdb operations with prepared statements
- Custom capability assignment to admin and editor roles
- Admin styles and JavaScript assets
- Full backward compatibility
