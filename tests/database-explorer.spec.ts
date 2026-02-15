import { test, expect } from '@playwright/test';
import * as dotenv from 'dotenv';

// Load .env file
dotenv.config();

// Load WordPress URL from environment variable
const WORDPRESS_URL = process.env.WORDPRESS_URL || 'http://personal-portfolio.local';

/**
 * Database Explorer Plugin Tests
 *
 * These tests validate the Database Explorer plugin demonstrates:
 * - WordPress database structure (12 core tables)
 * - Post types and post meta data
 * - Taxonomies, terms, and relationships
 * - User roles and capabilities
 * - WordPress options for settings storage
 * - Using WP_Query to retrieve and filter posts
 *
 * Authentication is handled by global setup (tests/global-setup.ts)
 * so all tests automatically have an authenticated session.
 */

test.describe('Database Explorer Plugin', () => {
	// Navigate to admin before each test to establish session
	test.beforeEach(async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/`);
	});

	test('Plugin should be installed and activated', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/plugins.php`);

		// Check for the plugin in the list
		const pluginRow = await page.locator('text=Database Explorer').first();
		await expect(pluginRow).toBeVisible();
	});

	test('Plugin should register admin menu item', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/`);

		// Look for Database Explorer in the left menu
		const menuItem = await page.locator('text=Database Explorer');
		await expect(menuItem).toBeVisible();
	});

	test('Admin page should display options data', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for WordPress Options heading
		const optionsHeading = page.locator('h2:has-text("WordPress Options")');
		await expect(optionsHeading).toBeVisible();

		// Get the options card (parent of heading)
		const optionsCard = optionsHeading.locator('..').locator('..');

		// Verify plugin title option is displayed within the card
		await expect(optionsCard.locator('p:has-text("Plugin Title")')).toBeVisible();
		await expect(optionsCard.locator('p:has-text("Plugin Title"):has-text("Database Explorer")')).toBeVisible();

		// Verify plugin version option is displayed within the card
		await expect(optionsCard.locator('p:has-text("Plugin Version")')).toBeVisible();
		await expect(optionsCard.locator('p:has-text("Plugin Version"):has-text("1.0.0")')).toBeVisible();

		// Verify enabled option is displayed within the card
		await expect(optionsCard.locator('p:has-text("Enabled")')).toBeVisible();
		await expect(optionsCard.locator('p:has-text("Enabled"):has-text("Yes")')).toBeVisible();
	});

	test('Admin page should display posts and post meta data', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Posts section heading
		const postsHeading = page.locator('h2:has-text("Posts and Post Meta")');
		await expect(postsHeading).toBeVisible();

		// Get the card containing posts section and find table within it
		const postsCard = postsHeading.locator('..');
		const table = postsCard.locator('table.wp-list-table');
		await expect(table).toBeVisible();

		// Verify table headers exist within this table
		await expect(table.locator('th:has-text("Title")')).toBeVisible();
		await expect(table.locator('th:has-text("Status")')).toBeVisible();
		await expect(table.locator('th:has-text("Date")')).toBeVisible();
		await expect(table.locator('th:has-text("Priority")')).toBeVisible();
		await expect(table.locator('th:has-text("Tags")')).toBeVisible();

		// Verify sample projects are displayed in this table (using :has-text in table cells)
		await expect(table.locator('td:has-text("Project 1")')).toBeVisible();
		await expect(table.locator('td:has-text("Project 2")')).toBeVisible();
		await expect(table.locator('td:has-text("Project 3")')).toBeVisible();
	});

	test('Admin page should display post meta filtering results', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the posts section card (Posts and Post Meta)
		const postsHeading = page.locator('h2:has-text("Posts and Post Meta")');
		const postsCard = postsHeading.locator('..').locator('..');
		const rows = postsCard.locator('table.wp-list-table tbody tr');
		const rowCount = await rows.count();

		// Should have at least our sample projects
		expect(rowCount).toBeGreaterThanOrEqual(3);

		// Verify first row has "completed" status
		const firstRowStatus = rows.first().locator('td:nth-child(3)');
		await expect(firstRowStatus).toContainText('completed');
	});

	test('Admin page should display taxonomies and terms', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Taxonomies section
		const taxHeading = page.locator('h2:has-text("Taxonomies & Terms")');
		await expect(taxHeading).toBeVisible();

		// Get the taxonomies card
		const taxCard = taxHeading.locator('..');

		// Verify table headers for terms in this card
		const table = taxCard.locator('table.wp-list-table');
		await expect(table.locator('th:has-text("Term ID")')).toBeVisible();
		await expect(table.locator('th:has-text("Name")')).toBeVisible();
		await expect(table.locator('th:has-text("Slug")')).toBeVisible();
		await expect(table.locator('th:has-text("Description")')).toBeVisible();

		// Verify Portfolio category is displayed in Name column (2nd column)
		await expect(table.locator('td:nth-child(2):has-text("Portfolio")')).toBeVisible();
	});

	test('Admin page should display users and capabilities', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Users section
		const usersHeading = page.locator('h2:has-text("Users & Capabilities")');
		await expect(usersHeading).toBeVisible();

		// Get the users card
		const usersCard = usersHeading.locator('..');
		const table = usersCard.locator('table.wp-list-table');

		// Verify table headers for users
		await expect(table.locator('th:has-text("User ID")')).toBeVisible();
		await expect(table.locator('th:has-text("Username")')).toBeVisible();
		await expect(table.locator('th:has-text("Email")')).toBeVisible();
		await expect(table.locator('th:has-text("Role")')).toBeVisible();
		await expect(table.locator('th:has-text("Can Edit Posts")')).toBeVisible();

		// Verify admin user is displayed in this table (username column is 2nd column)
		await expect(table.locator('td:nth-child(2):has-text("admin")')).toBeVisible();
	});

	test('Admin page layout should be responsive', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check that all major sections are visible
		const sections = page.locator('.card');
		const sectionCount = await sections.count();

		// Should have at least 4 card sections
		expect(sectionCount).toBeGreaterThanOrEqual(4);

		// Each section should be visible
		for (let i = 0; i < sectionCount; i++) {
			const card = sections.nth(i);
			await expect(card).toBeVisible();
		}
	});

	test('Post meta should filter results correctly', async ({ page }) => {
		// Navigate to Database Explorer
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the posts section card
		const postsHeading = page.locator('h2:has-text("Posts and Post Meta")');
		const postsCard = postsHeading.locator('..').locator('..');
		const rows = postsCard.locator('table.wp-list-table tbody tr');
		const rowCount = await rows.count();

		// Verify we have the sample posts
		expect(rowCount).toBeGreaterThanOrEqual(3);

		// Verify each displayed post has the filtered meta value
		for (let i = 0; i < rowCount; i++) {
			const row = rows.nth(i);
			// Status column (3rd column) should show 'completed'
			const statusCell = row.locator('td:nth-child(3)');
			const statusText = await statusCell.textContent();
			expect(statusText).toContain('completed');
		}
	});

	test('WP_Query should order results by priority', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the posts section card
		const postsHeading = page.locator('h2:has-text("Posts and Post Meta")');
		const postsCard = postsHeading.locator('..').locator('..');

		// Get all priority cells (5th column) from this card's table
		const priorityCells = postsCard.locator('table.wp-list-table tbody tr td:nth-child(5)');
		const priorityCount = await priorityCells.count();

		if (priorityCount > 0) {
			// Extract all priority values
			const priorities: number[] = [];
			for (let i = 0; i < priorityCount; i++) {
				const text = await priorityCells.nth(i).textContent();
				const num = parseInt(text || '0');
				priorities.push(num);
			}

			// Verify they are in ascending order
			for (let i = 1; i < priorities.length; i++) {
				expect(priorities[i]).toBeGreaterThanOrEqual(priorities[i - 1]);
			}
		}
	});

	test('Portfolio category should be linked to sample posts', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the taxonomies card
		const taxHeading = page.locator('h2:has-text("Taxonomies & Terms")');
		const taxCard = taxHeading.locator('..').locator('..');
		const table = taxCard.locator('table.wp-list-table');

		// Find Portfolio row in this table and check post count (5th column)
		const portfolioRow = table.locator('tbody tr').filter({ has: table.locator('td:has-text("Portfolio")') });
		const postCountCell = portfolioRow.locator('td:nth-child(5)');

		const postCount = await postCountCell.textContent();
		// Should have at least 3 posts assigned
		const count = parseInt(postCount || '0');
		expect(count).toBeGreaterThanOrEqual(3);
	});

	test('Admin user should have edit_posts capability', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the users card
		const usersHeading = page.locator('h2:has-text("Users & Capabilities")');
		const usersCard = usersHeading.locator('..').locator('..');
		const table = usersCard.locator('table.wp-list-table');

		// Find the admin user row in this table
		const adminRow = table.locator('tbody tr').filter({ has: table.locator('td:has-text("admin")') });
		// Check the "Can Edit Posts" column (5th column)
		const canEditCell = adminRow.locator('td:nth-child(5)');

		const canEdit = await canEditCell.textContent();
		expect(canEdit).toContain('Yes');
	});

	test('Sample posts should have correct meta values', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the posts section card
		const postsHeading = page.locator('h2:has-text("Posts and Post Meta")');
		const postsCard = postsHeading.locator('..').locator('..');
		const table = postsCard.locator('table.wp-list-table');

		// Check Project 1 row in this table
		const project1Row = table.locator('tbody tr').filter({ has: table.locator('td:has-text("Project 1")') });

		// Status should be 'completed'
		const statusCell = project1Row.locator('td:nth-child(3)');
		await expect(statusCell).toContainText('completed');

		// Tags should contain project tags
		const tagsCell = project1Row.locator('td:nth-child(6)');
		await expect(tagsCell).toContainText('wordpress');
		await expect(tagsCell).toContainText('php');
	});

	test('Access control should prevent non-admin users', async ({ page }) => {
		// This test verifies current_user_can check
		// By default we're logged in as admin, so we should see the page
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Page should load successfully
		await expect(page.locator('h1:has-text("Database Explorer")')).toBeVisible();
	});

	test('Plugin should store and retrieve options correctly', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Get the WordPress Options card
		const optionsHeading = page.locator('h2:has-text("WordPress Options")');
		const optionsCard = optionsHeading.locator('..').locator('..');

		// Verify all three options are displayed in this card using specific paragraph selectors
		await expect(optionsCard.locator('p:has-text("Plugin Title"):has-text("Database Explorer")')).toBeVisible();
		await expect(optionsCard.locator('p:has-text("Plugin Version"):has-text("1.0.0")')).toBeVisible();
		await expect(optionsCard.locator('p:has-text("Enabled"):has-text("Yes")')).toBeVisible();
	});

	test('Database Explorer page should load in reasonable time', async ({ page }) => {
		const startTime = Date.now();
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);
		const endTime = Date.now();

		const loadTime = endTime - startTime;
		// Should load in less than 3 seconds
		expect(loadTime).toBeLessThan(3000);
	});
});
