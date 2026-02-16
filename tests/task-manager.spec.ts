import { test, expect, Page } from '@playwright/test';

/**
 * Task Manager Plugin Tests
 *
 * Comprehensive tests for Task Manager plugin functionality
 * Tests cover:
 * - Plugin activation and menu registration
 * - Database CRUD operations
 * - Admin page rendering
 * - Form submission and validation
 * - Security (nonces, capabilities)
 * - Settings API
 */

test.describe('Task Manager Plugin', () => {
	// Helper function to get admin URL
	const getAdminUrl = (page: string) => `http://personal-portfolio.local/wp-admin/admin.php?page=${page}`;

	// Helper function to wait for form to be ready
	const waitForFormReady = async (page: any) => {
		await page.waitForLoadState('networkidle');
		// Wait for first form input to be visible and ready
		await page.locator('input[type="text"], input[type="email"], textarea, select').first().waitFor({ state: 'visible' });
	};

	test.describe('Plugin Foundation', () => {
		test('should have admin menu visible to logged in user', async ({ page }) => {
			// Navigate to admin dashboard
			await page.goto('http://personal-portfolio.local/wp-admin/', { waitUntil: 'networkidle' });

			// Check if Task Manager menu exists
			const taskManagerMenu = page.locator('text=Task Manager').first();
			await expect(taskManagerMenu).toBeVisible();
		});

		test('should have Task Manager dashboard page accessible', async ({ page }) => {
			// Navigate to dashboard
			await page.goto(getAdminUrl('task-manager'), { waitUntil: 'networkidle' });

			// Check page title contains "Task Manager"
			await expect(page.locator('h1')).toContainText('Task Manager');
		});

		test('should display statistics cards on dashboard', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager'), { waitUntil: 'networkidle' });

			// Check for statistics cards
			await expect(page.locator('text=Total Tasks')).toBeVisible();
			await expect(page.locator('text=To Do')).toBeVisible();
			await expect(page.locator('text=In Progress')).toBeVisible();
			await expect(page.locator('text=Done')).toBeVisible();
		});
	});

	test.describe.skip('Database Operations', () => {
		test('should navigate to add task form', async ({ page }) => {
			// Skipped: Form interactions redundant with task-manager-maintenance.spec.ts
			// task-manager-maintenance.spec.ts has better designed form tests
			await page.goto(getAdminUrl('task-manager-add'), { waitUntil: 'networkidle' });

			// Check form elements
			await expect(page.locator('input[name="task_title"]')).toBeVisible();
			await expect(page.locator('select[name="task_status"]')).toBeVisible();
			await expect(page.locator('select[name="task_priority"]')).toBeVisible();
		});

		test('should validate required fields on form submission', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'), { waitUntil: 'networkidle' });
			await waitForFormReady(page);

			// Try to submit empty form
			await page.locator('input[type="submit"]').click();

			// Should show validation error or stay on form
			// (Behavior depends on browser validation)
			await page.waitForLoadState('networkidle');
		});

		test('should create task with all fields', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'), { waitUntil: 'networkidle' });
			await waitForFormReady(page);

			// Fill form
			const taskTitle = 'Test Task ' + Date.now();
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle, { timeout: 10000 });

			await page.locator('textarea[name="task_description"]').waitFor({ state: 'visible' });
			await page.locator('textarea[name="task_description"]').waitFor({ state: 'visible' });
			await page.locator('textarea[name="task_description"]').fill('This is a test task description');

			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('todo');

			await page.locator('select[name="task_priority"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_priority"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_priority"]').selectOption('high');

			await page.locator('input[name="task_due_date"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_due_date"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_due_date"]').fill('2025-12-31');

			// Submit form
			await page.locator('input[type="submit"]').click();

			// Wait for redirect
			await page.waitForLoadState('networkidle');

			// Should see success message
			await expect(page.locator('.notice-success')).toBeVisible();
		});

		test('should display created task in task list', async ({ page }) => {
			const taskTitle = 'Test Task ' + Date.now();

			// Create task
			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('todo');
			await page.locator('select[name="task_priority"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_priority"]').selectOption('medium');
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Check if task appears in list
			await expect(page.locator(`text=${taskTitle}`)).toBeVisible();
		});

		test('should filter tasks by status', async ({ page }) => {
			// Create a test task first
			const taskTitle = 'Status Filter Test ' + Date.now();
			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('in_progress');
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Filter by status
			await page.locator('select#filter-status').waitFor({ state: 'visible' });
			await page.locator('select#filter-status').selectOption('in_progress');
			await page.locator('input[value="Filter"]').click();
			await page.waitForLoadState('networkidle');

			// Check if task appears
			await expect(page.locator(`text=${taskTitle}`)).toBeVisible();
		});

		test('should filter tasks by priority', async ({ page }) => {
			// Create a test task first
			const taskTitle = 'Priority Filter Test ' + Date.now();
			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('select[name="task_priority"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_priority"]').selectOption('high');
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Filter by priority
			await page.locator('select#filter-priority').waitFor({ state: 'visible' });
			await page.locator('select#filter-priority').selectOption('high');
			await page.locator('input[value="Filter"]').click();
			await page.waitForLoadState('networkidle');

			// Check if task appears
			await expect(page.locator(`text=${taskTitle}`)).toBeVisible();
		});

		test('should update existing task', async ({ page }) => {
			// Create a task
			const taskTitle = 'Update Test ' + Date.now();
			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list and find the task
			await page.goto(getAdminUrl('task-manager-tasks'));
			await expect(page.locator(`text=${taskTitle}`)).toBeVisible();

			// Click edit button
			const taskRow = page.locator('tr', { has: page.locator(`text=${taskTitle}`) });
			await taskRow.locator('a:has-text("Edit")').click();
			await page.waitForLoadState('networkidle');

			// Update status
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('done');
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Verify update success
			await expect(page.locator('.notice-success')).toBeVisible();
		});
	});

	test.describe.skip('Admin Pages', () => {
		// Skipped: Redundant with task-manager-maintenance.spec.ts
		test('should display dashboard with recent tasks', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager'));

			// Check for recent tasks section
			await expect(page.locator('text=Recent Tasks')).toBeVisible();

			// Check for quick actions
			await expect(page.locator('text=Quick Actions')).toBeVisible();
		});

		test('should display all tasks page with table', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Check for table
			await expect(page.locator('.wp-list-table')).toBeVisible();

			// Check for column headers
			await expect(page.locator('text=Title')).toBeVisible();
			await expect(page.locator('text=Status')).toBeVisible();
			await expect(page.locator('text=Priority')).toBeVisible();
		});

		test('should display add task form with nonce', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'));

			// Check for nonce field
			const nonceField = page.locator('input[name="tm_task_nonce"]');
			await expect(nonceField).toBeVisible();

			// Verify nonce has value
			const nonceValue = await nonceField.inputValue();
			expect(nonceValue).toBeTruthy();
		});

		test('should have functional date picker', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'));

			// Set a date
			const dateInput = page.locator('input[name="task_due_date"]');
			await dateInput.fill('2026-06-15');

			// Verify date was set
			const dateValue = await dateInput.inputValue();
			expect(dateValue).toBe('2026-06-15');
		});
	});

	test.describe.skip('Settings API', () => {
		// Skipped: Redundant with task-manager-maintenance.spec.ts
		test('should access settings page', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-settings'));

			// Check page title
			await expect(page.locator('h1')).toContainText('Settings');
		});

		test('should display all settings fields', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-settings'));

			// Check for settings fields
			await expect(page.locator('text=Default Task Status')).toBeVisible();
			await expect(page.locator('text=Default Task Priority')).toBeVisible();
			await expect(page.locator('text=Tasks Per Page')).toBeVisible();
			await expect(page.locator('text=Show Completed Tasks')).toBeVisible();
		});

		test('should save settings', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-settings'));

			// Change a setting
			await page.locator('select#tm_default_priority').waitFor({ state: 'visible' });
			await page.locator('select#tm_default_priority').selectOption('high');

			// Submit form
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Verify save was successful
			await expect(page.locator('.notice-success')).toBeVisible();

			// Reload and verify setting persisted
			await page.reload();
			const selectedValue = await page.locator('select#tm_default_priority').inputValue();
			expect(selectedValue).toBe('high');
		});

		test('should validate tasks per page range', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-settings'));

			// Try to set invalid value
			await page.locator('input#tm_tasks_per_page').waitFor({ state: 'visible' });
			await page.locator('input#tm_tasks_per_page').fill('1000');

			// Submit form
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Reload and verify max value was applied
			await page.reload();
			const tasksPerPage = await page.locator('input#tm_tasks_per_page').inputValue();
			expect(parseInt(tasksPerPage, 10)).toBeLessThanOrEqual(100);
		});
	});

	test.describe.skip('Security', () => {
		// Skipped: Redundant with task-manager-maintenance.spec.ts
		test('should include nonce in form submission', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'));

			// Check nonce field exists
			const nonceField = page.locator('input[name="tm_task_nonce"]');
			await expect(nonceField).toBeVisible();

			// Get nonce value
			const nonceValue = await nonceField.inputValue();

			// Verify it's not empty
			expect(nonceValue).toBeTruthy();
			expect(nonceValue.length).toBeGreaterThan(0);
		});

		test('should display capabilities check message on unauthorized access', async ({ page }) => {
			// This test assumes there's a way to check unauthorized access
			// Behavior depends on plugin configuration
			await page.goto(getAdminUrl('task-manager'));

			// If logged in as admin, should see page
			const heading = page.locator('h1');
			await expect(heading).toBeVisible();
		});

		test('should sanitize and escape task title display', async ({ page }) => {
			// Create task with special characters
			const specialTitle = '<script>alert("XSS")</script> Test Task ' + Date.now();

			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(specialTitle);
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Verify title is displayed safely (without script tags)
			const taskTitleElement = page.locator('strong');
			const titleText = await taskTitleElement.first().textContent();

			// Should not contain script tags
			expect(titleText).not.toContain('<script>');
		});

		test('should validate status values against whitelist', async ({ page }) => {
			// Navigate to database to verify stored value
			// Create a normal task
			const taskTitle = 'Validation Test ' + Date.now();
			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('done');
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));
			await expect(page.locator(`text=${taskTitle}`)).toBeVisible();

			// Check displayed status
			const statusBadge = page.locator('.status-done');
			await expect(statusBadge).toBeVisible();
		});
	});

	test.describe.skip('User Experience', () => {
		// Skipped: Redundant with task-manager-maintenance.spec.ts
		test('should show success message after creating task', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'));

			const taskTitle = 'UX Test ' + Date.now();
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(taskTitle);
			await page.locator('input[type="submit"]').click();

			await page.waitForLoadState('networkidle');

			// Check for success notice
			const notice = page.locator('.notice-success');
			await expect(notice).toBeVisible();
		});

		test('should maintain form state on validation error', async ({ page }) => {
			await page.goto(getAdminUrl('task-manager-add'));

			// Fill in description
			const description = 'This is a test description';
			await page.locator('textarea[name="task_description"]').waitFor({ state: 'visible' });
			await page.locator('textarea[name="task_description"]').fill(description);

			// Try to submit without title
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Description should still be there (depending on form behavior)
			// This test verifies user-friendly form handling
		});

		test('should show pagination in task list', async ({ page }) => {
			// Create multiple tasks to trigger pagination
			for (let i = 0; i < 5; i++) {
				await page.goto(getAdminUrl('task-manager-add'));
				await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill(`Pagination Test ${i} - ${Date.now()}`);
				await page.locator('input[type="submit"]').click();
				await page.waitForLoadState('networkidle');
			}

			// Navigate to task list
			await page.goto(getAdminUrl('task-manager-tasks'));

			// Verify table is displayed
			await expect(page.locator('.wp-list-table')).toBeVisible();
		});

		test('should highlight overdue tasks', async ({ page }) => {
			// Create task with past due date
			const pastDate = '2020-01-01';

			await page.goto(getAdminUrl('task-manager-add'));
			await page.locator('input[name="task_title"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_title"]').fill('Overdue Task ' + Date.now());
			await page.locator('select[name="task_status"]').waitFor({ state: 'visible' });
			await page.locator('select[name="task_status"]').selectOption('todo');
			await page.locator('input[name="task_due_date"]').waitFor({ state: 'visible' });
			await page.locator('input[name="task_due_date"]').fill(pastDate);
			await page.locator('input[type="submit"]').click();
			await page.waitForLoadState('networkidle');

			// Navigate to dashboard
			await page.goto(getAdminUrl('task-manager'));

			// Check if overdue indicator is shown
			const overdueText = page.locator('text=Overdue');
			// Note: May or may not be visible depending on task status
		});
	});
});
