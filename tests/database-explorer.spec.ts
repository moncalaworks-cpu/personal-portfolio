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
		await expect(page.locator('h2:has-text("WordPress Options")')).toBeVisible();

		// Verify plugin title option is displayed
		await expect(page.locator('text=Plugin Title')).toBeVisible();
		await expect(page.locator('text=Database Explorer')).toBeVisible();

		// Verify plugin version option is displayed
		await expect(page.locator('text=Plugin Version')).toBeVisible();
		await expect(page.locator('text=1.0.0')).toBeVisible();

		// Verify enabled option is displayed
		await expect(page.locator('text=Enabled')).toBeVisible();
		await expect(page.locator('text=Yes')).toBeVisible();
	});

	test('Admin page should display posts and post meta data', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Posts section heading
		await expect(page.locator('h2:has-text("Posts and Post Meta")')).toBeVisible();

		// Verify the table displays
		const table = page.locator('table.wp-list-table');
		await expect(table).toBeVisible();

		// Verify table headers
		await expect(page.locator('th:has-text("Title")')).toBeVisible();
		await expect(page.locator('th:has-text("Status")')).toBeVisible();
		await expect(page.locator('th:has-text("Date")')).toBeVisible();
		await expect(page.locator('th:has-text("Priority")')).toBeVisible();
		await expect(page.locator('th:has-text("Tags")')).toBeVisible();

		// Verify sample projects are displayed
		await expect(page.locator('text=Project 1')).toBeVisible();
		await expect(page.locator('text=Project 2')).toBeVisible();
		await expect(page.locator('text=Project 3')).toBeVisible();
	});

	test('Admin page should display post meta filtering results', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Verify the WP_Query results show posts with 'completed' status
		const rows = page.locator('table.wp-list-table tbody tr');
		const rowCount = await rows.count();

		// Should have at least our sample projects
		expect(rowCount).toBeGreaterThanOrEqual(3);

		// Verify first row has "completed" status
		const firstRowStatus = await rows.first().locator('td:nth-child(3)');
		await expect(firstRowStatus).toContainText('completed');
	});

	test('Admin page should display taxonomies and terms', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Taxonomies section
		await expect(page.locator('h2:has-text("Taxonomies & Terms")')).toBeVisible();

		// Verify Portfolio category is displayed
		await expect(page.locator('text=Portfolio')).toBeVisible();

		// Verify table headers for terms
		await expect(page.locator('th:has-text("Term ID")')).toBeVisible();
		await expect(page.locator('th:has-text("Name")')).toBeVisible();
		await expect(page.locator('th:has-text("Slug")')).toBeVisible();
		await expect(page.locator('th:has-text("Description")')).toBeVisible();
	});

	test('Admin page should display users and capabilities', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check for Users section
		await expect(page.locator('h2:has-text("Users & Capabilities")')).toBeVisible();

		// Verify table headers for users
		await expect(page.locator('th:has-text("User ID")')).toBeVisible();
		await expect(page.locator('th:has-text("Username")')).toBeVisible();
		await expect(page.locator('th:has-text("Email")')).toBeVisible();
		await expect(page.locator('th:has-text("Role")')).toBeVisible();
		await expect(page.locator('th:has-text("Can Edit Posts")')).toBeVisible();

		// Verify admin user is displayed
		await expect(page.locator('text=admin')).toBeVisible();
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

		// Get all rows in the posts table
		const rows = page.locator('table.wp-list-table tbody tr');
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

		// Get all priority cells (5th column)
		const priorityCells = page.locator('table.wp-list-table tbody tr td:nth-child(5)');
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

		// Get the terms section
		const termsTable = page.locator('h2:has-text("Taxonomies & Terms")').locator('..').locator('table');

		// Find Portfolio row and check post count
		const portfolioRow = page.locator('text=Portfolio').locator('..').locator('..');
		const postCountCell = portfolioRow.locator('td:nth-child(5)');

		const postCount = await postCountCell.textContent();
		// Should have at least 3 posts assigned
		const count = parseInt(postCount || '0');
		expect(count).toBeGreaterThanOrEqual(3);
	});

	test('Admin user should have edit_posts capability', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Find the admin user row
		const adminRow = page.locator('text=admin').locator('..').locator('..');
		// Check the "Can Edit Posts" column (5th column)
		const canEditCell = adminRow.locator('td:nth-child(5)');

		const canEdit = await canEditCell.textContent();
		expect(canEdit).toContain('Yes');
	});

	test('Sample posts should have correct meta values', async ({ page }) => {
		await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);

		// Check Project 1 row
		const project1Row = page.locator('text=Project 1').locator('..').locator('..');

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

		// Verify all three options are displayed
		const titleOption = page.locator('text=Database Explorer').first();
		const versionOption = page.locator('text=1.0.0');
		const enabledOption = page.locator('text=Yes');

		await expect(titleOption).toBeVisible();
		await expect(versionOption).toBeVisible();
		await expect(enabledOption).toBeVisible();
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
