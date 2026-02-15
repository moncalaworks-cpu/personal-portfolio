# Task Manager Plugin

A comprehensive WordPress plugin demonstrating advanced plugin development concepts including class-based architecture, custom database tables with $wpdb operations, Settings API, security best practices, and enterprise-grade maintenance features (v1.1.0).

## What's New in v1.1.0

- **Database Migrations**: Safe schema changes with version tracking
- **Performance Caching**: Intelligent transient-based caching for frequently accessed data
- **Comprehensive Logging**: Structured logging for debugging and monitoring
- **Dependency Checking**: Graceful handling of incompatible environments
- **WordPress Coding Standards**: Full compliance and validation configuration

See [MAINTENANCE.md](MAINTENANCE.md) for detailed information on v1.1.0 features.

## Learning Objectives Demonstrated

### 1. **Plugin Foundation** ✓
- Custom plugin structure with header comments
- Plugin activation/deactivation hooks
- Plugin constants (`__FILE__`, `__DIR__`, `plugin_dir_path()`)
- Text domains and i18n functions

### 2. **Essential Functions & APIs** ✓
- `add_action()` and `do_action()` for custom workflows
- `add_filter()` and `apply_filters()` for content modification
- `wp_enqueue_script()` and `wp_enqueue_style()` for assets
- `add_menu_page()` and `add_submenu_page()` for admin interface

### 3. **Security Best Practices** ✓
- Nonce creation with `wp_nonce_field()` and `wp_verify_nonce()`
- Input sanitization: `sanitize_text_field()`, `sanitize_email()`, `wp_kses_post()`
- Output escaping: `esc_html()`, `esc_url()`, `esc_attr()`
- Capability checking with `current_user_can()`

### 4. **Database Operations** ✓
- Direct `$wpdb` object usage for custom queries
- Prepared statements for SQL injection prevention (`$wpdb->prepare()`)
- Custom table creation on activation
- Complete CRUD operations (Create, Read, Update, Delete)

### 5. **Admin Pages & Forms** ✓
- Custom admin pages with `add_menu_page()`
- Admin forms with nonces for security
- Admin notices and error handling
- Settings API with `register_setting()` and `add_settings_section()`

### 6. **Plugin Patterns** ✓
- Multi-file plugin structure
- Class-based architecture with proper namespacing
- PSR-4 autoloader for class loading
- DRY principles throughout

## Features

### Admin Interface

#### 1. Dashboard
- Overview statistics (Total, To Do, In Progress, Done)
- Recent tasks with status indicators
- Quick action buttons

#### 2. All Tasks
- Filterable task list using WP_List_Table pattern
- Filter by status and priority
- Pagination support
- Edit and delete operations

#### 3. Add/Edit Task
- Form with title, description, status, priority, due date
- WYSIWYG editor for descriptions
- Nonce protection
- Validation error handling

#### 4. Settings
- Default task status
- Default task priority
- Tasks per page
- Show/hide completed tasks

## Database Schema

### `wp_tm_tasks` Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | `bigint(20)` | Primary key, auto-increment |
| `title` | `varchar(255)` | Task title (required) |
| `description` | `longtext` | Task description (optional) |
| `status` | `varchar(20)` | 'todo', 'in_progress', 'done' |
| `priority` | `varchar(20)` | 'low', 'medium', 'high' |
| `due_date` | `date` | Task due date (optional) |
| `created_by` | `bigint(20)` | User ID of creator |
| `created_at` | `datetime` | Creation timestamp |
| `updated_at` | `datetime` | Last update timestamp |

## Directory Structure

```
task-manager/
├── task-manager.php              # Main plugin file
├── uninstall.php                 # Uninstall cleanup
├── README.md                      # This file
├── MAINTENANCE.md                 # v1.1.0 maintenance guide
├── CHANGELOG.md                   # Version history
├── phpcs.xml                      # WordPress Coding Standards config
├── includes/
│   ├── class-activator.php       # Activation handler
│   ├── class-database.php        # Database operations
│   ├── class-task.php            # Task entity
│   ├── class-migrator.php        # v1.1.0 Migration manager
│   ├── class-logger.php          # v1.1.0 Logging system
│   ├── class-cache.php           # v1.1.0 Caching layer
│   └── migrations/               # v1.1.0 Database migrations
│       ├── class-migration-base.php
│       └── class-migration-110.php
├── admin/
│   ├── class-admin-pages.php     # Menu registration
│   ├── class-task-form.php       # Form handling
│   ├── class-settings.php        # Settings API
│   └── partials/
│       ├── dashboard.php         # Dashboard template
│       ├── task-list.php         # Task list template
│       ├── task-form.php         # Task form template
│       └── settings.php          # Settings template
├── security/
│   └── class-validator.php       # Input validation
└── assets/
    ├── css/admin-styles.css      # Admin styles
    └── js/admin-scripts.js       # Admin scripts
```

## Capabilities

Task Manager creates and assigns custom capabilities:

| Capability | Admin | Editor |
|------------|-------|--------|
| `manage_tasks` | ✓ | ✗ |
| `create_tasks` | ✓ | ✓ |
| `edit_tasks` | ✓ | ✓ |
| `delete_tasks` | ✓ | ✓ |

## Usage

### Create a Task

1. Navigate to Task Manager → Add New Task
2. Fill in task details:
   - **Title** (required)
   - **Description** (optional)
   - **Status**: To Do, In Progress, Done
   - **Priority**: Low, Medium, High
   - **Due Date** (optional)
3. Click "Save Task"

### View and Filter Tasks

1. Navigate to Task Manager → All Tasks
2. Filter by status and/or priority
3. Click "Filter" to apply filters
4. Use pagination to view more tasks

### Edit a Task

1. Navigate to Task Manager → All Tasks
2. Click "Edit" on desired task
3. Modify task details
4. Click "Save Task"

### Delete a Task

1. Navigate to Task Manager → All Tasks
2. Click "Delete" on desired task
3. Confirm deletion

### Configure Settings

1. Navigate to Task Manager → Settings
2. Configure:
   - Default status for new tasks
   - Default priority for new tasks
   - Number of tasks per page
   - Whether to show completed tasks
3. Click "Save Changes"

## Security Implementation

### Input Validation & Sanitization

```php
// All inputs are sanitized using wp_kses_post() or sanitize_text_field()
Security\Validator::sanitize_task( $data );

// Status and priority validated against whitelists
if ( ! in_array( $status, ['todo', 'in_progress', 'done'], true ) ) {
    $status = 'todo'; // Default if invalid
}
```

### SQL Injection Prevention

```php
// All database queries use prepared statements
$wpdb->prepare( 'SELECT * FROM table WHERE id = %d', $task_id );
```

### CSRF Protection

```php
// All forms include nonce verification
wp_nonce_field( 'tm_save_task', 'tm_task_nonce' );
wp_verify_nonce( $_POST['tm_task_nonce'], 'tm_save_task' );
```

### Output Escaping

```php
// All output is escaped for context
echo esc_html( $task->title );           // HTML context
echo esc_attr( $css_class );             // HTML attribute context
echo esc_url( $link );                   // URL context
```

### Capability Checking

```php
// All admin functions check user capabilities
if ( ! current_user_can( 'manage_tasks' ) ) {
    wp_die( 'You do not have permission...' );
}
```

## Testing

Run the comprehensive test suite:

```bash
# Run all tests
npm test

# Run Task Manager tests only
npm test -- task-manager.spec.ts

# Run tests in headed mode (see browser)
npm run test:headed -- task-manager.spec.ts

# Generate HTML report
npm run test:report
```

## WordPress Coding Standards

The plugin follows WordPress coding standards:

- Proper indentation (tabs)
- Descriptive variable naming
- PHPDoc blocks for classes and methods
- Proper use of `wp_*()` functions
- Text domain usage for all user-facing strings
- Security best practices (sanitization, escaping, capability checks)

## API Reference

### TaskManager\Database

```php
$db = TaskManager\Database::get_instance();

// Create task
$task_id = $db->create_task( [
    'title'       => 'Task Title',
    'description' => 'Description',
    'status'      => 'todo',
    'priority'    => 'medium',
    'due_date'    => '2026-12-31',
    'created_by'  => get_current_user_id(),
] );

// Get tasks
$tasks = $db->get_tasks( [
    'status'   => 'todo',
    'priority' => 'high',
    'limit'    => 20,
    'offset'   => 0,
] );

// Get single task
$task = $db->get_task( $task_id );

// Update task
$db->update_task( $task_id, [ 'status' => 'done' ] );

// Delete task
$db->delete_task( $task_id );

// Get statistics
$stats = $db->get_task_statistics(); // ['total' => X, 'todo' => X, ...]
```

### TaskManager\Task

```php
$task = new TaskManager\Task( $data );

// Properties
$task->id;
$task->title;
$task->description;
$task->status;
$task->priority;
$task->due_date;
$task->created_by;

// Methods
$task->to_array();
$task->get_status_label();
$task->get_priority_label();
$task->get_creator();
$task->get_formatted_due_date();
$task->is_overdue();
```

### TaskManager\Admin\Settings

```php
// Get all settings
$settings = TaskManager\Admin\Settings::get_all();

// Get single setting
$default_status = TaskManager\Admin\Settings::get( 'default_status' );
```

## Troubleshooting

### Plugin not appearing in admin menu
- Check if you're logged in as admin
- Verify plugin is activated in Plugins page
- Check browser console for JavaScript errors

### Tasks not saving
- Check if form includes nonce field
- Verify database table was created (check wp_tm_tasks)
- Check WordPress debug.log for errors

### Styles not loading
- Check CSS file path in admin-styles.css
- Verify wp_enqueue_style() is being called
- Check browser Network tab for 404 errors

## License

GPL v2 or later

## Author

Learning Project - Personal Portfolio WordPress Development Course

## Version

1.1.0 - See [CHANGELOG.md](CHANGELOG.md) for full version history
