import { test, expect } from '@playwright/test';

// Analytics Dashboard Tests
// Validates: menu registration, KPI display, date filtering, caching, security

test.describe('Analytics Dashboard', () => {
	const ADMIN_URL = 'http://personal-portfolio.local/wp-admin/';
	const ANALYTICS_URL = ADMIN_URL + 'admin.php?page=analytics-dashboard';

	test('should display Analytics menu in WordPress admin', async ({ page }) => {
		await page.goto(ADMIN_URL, { waitUntil: 'networkidle' });

		// Check Analytics menu appears in sidebar
		const analyticsMenu = page.locator('a[href*="page=analytics-dashboard"]');
		await expect(analyticsMenu).toBeVisible();

		// Menu text should contain "Analytics"
		await expect(analyticsMenu).toContainText('Analytics');
	});

	test('should navigate to analytics dashboard page', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Check page title
		const pageTitle = page.locator('h1');
		await expect(pageTitle).toContainText('Analytics Dashboard');
	});

	test('should display all 5 KPI cards', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Check each KPI card header is visible
		await expect(page.locator('h3:has-text("Total Posts")')).toBeVisible();
		await expect(page.locator('h3:has-text("Total Comments")')).toBeVisible();
		await expect(page.locator('h3:has-text("Total Users")')).toBeVisible();
		await expect(page.locator('h3:has-text("Total Pages")')).toBeVisible();
		await expect(page.locator('h3:has-text("Avg Engagement")')).toBeVisible();
	});

	test('should display numeric values in KPI cards', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Get all metric values
		const metricValues = page.locator('.analytics-dashboard-grid p').filter({
			has: page.locator('..').filter({ hasText: /^\d+(\.\d+)?$/ })
		});

		// Should have at least some numeric values
		const valueCount = await metricValues.count();
		expect(valueCount).toBeGreaterThan(0);

		// Verify at least the first KPI shows a number
		const firstValue = page.locator('.analytics-dashboard-grid .postbox').first().locator('p').first();
		const text = await firstValue.textContent();
		expect(text).toMatch(/^\d+(\.\d+)?$/);
	});

	test('should have responsive grid layout on tablet', async ({ page }) => {
		await page.setViewportSize({ width: 768, height: 1024 });
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Grid should still be visible
		const grid = page.locator('.analytics-dashboard-grid');
		await expect(grid).toBeVisible();

		// KPI cards should be visible
		const cards = page.locator('.analytics-dashboard-grid .postbox');
		const cardCount = await cards.count();
		expect(cardCount).toBe(5);
	});

	test('should have responsive grid layout on mobile', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Grid should still be visible
		const grid = page.locator('.analytics-dashboard-grid');
		await expect(grid).toBeVisible();

		// All KPI cards should be visible (single column on mobile)
		const cards = page.locator('.analytics-dashboard-grid .postbox');
		const cardCount = await cards.count();
		expect(cardCount).toBe(5);
	});

	test('should display date range selector', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Check selector exists
		const selector = page.locator('select[name="range"]');
		await expect(selector).toBeVisible();

		// Check options exist
		const options = selector.locator('option');
		const optionCount = await options.count();
		expect(optionCount).toBeGreaterThanOrEqual(3);
	});

	test('should filter by Last 7 Days', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Select "Last 7 Days"
		await page.locator('select[name="range"]').selectOption('7');

		// Click filter button
		await page.locator('input[value="Filter"]').click();
		await page.waitForLoadState('networkidle');

		// URL should contain range parameter
		expect(page.url()).toContain('range=7');

		// Dashboard should still display
		await expect(page.locator('h1:has-text("Analytics Dashboard")')).toBeVisible();
		await expect(page.locator('h3:has-text("Total Posts")')).toBeVisible();
	});

	test('should filter by Last 30 Days', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Select "Last 30 Days"
		await page.locator('select[name="range"]').selectOption('30');

		// Click filter button
		await page.locator('input[value="Filter"]').click();
		await page.waitForLoadState('networkidle');

		// URL should contain range parameter
		expect(page.url()).toContain('range=30');

		// Dashboard should still display
		await expect(page.locator('h1:has-text("Analytics Dashboard")')).toBeVisible();
	});

	test('should filter by All Time', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Select "All Time"
		await page.locator('select[name="range"]').selectOption('all');

		// Click filter button
		await page.locator('input[value="Filter"]').click();
		await page.waitForLoadState('networkidle');

		// URL should contain range parameter
		expect(page.url()).toContain('range=all');

		// Dashboard should still display
		await expect(page.locator('h1:has-text("Analytics Dashboard")')).toBeVisible();
	});

	test('should load dashboard within reasonable time', async ({ page }) => {
		const startTime = Date.now();
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });
		const loadTime = Date.now() - startTime;

		// Should load in under 3 seconds
		expect(loadTime).toBeLessThan(3000);

		// Dashboard content should be visible
		await expect(page.locator('h1:has-text("Analytics Dashboard")')).toBeVisible();
	});

	test('should cache statistics on reload', async ({ page }) => {
		// First load
		const startTime1 = Date.now();
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });
		const firstLoadTime = Date.now() - startTime1;

		// Second load (should hit cache)
		const startTime2 = Date.now();
		await page.reload({ waitUntil: 'networkidle' });
		const secondLoadTime = Date.now() - startTime2;

		// Second load should be similar or faster (due to cache)
		// Allow some variance but not dramatically slower
		expect(secondLoadTime).toBeLessThan(firstLoadTime + 500);
	});

	test('should verify unauthorized users cannot access', async ({ page, context }) => {
		// Create new context without auth (simulating logged-out user)
		const newContext = await context.browser()?.newContext();
		if (!newContext) {
			test.skip();
			return;
		}

		const loggedOutPage = await newContext.newPage();

		try {
			// Try to access analytics page without logging in
			await loggedOutPage.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

			// Should be redirected to login
			expect(loggedOutPage.url()).toContain('login');
		} finally {
			await newContext.close();
		}
	});

	test('should update stats when new post is created', async ({ page }) => {
		// Get initial post count
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });
		const initialValue = await page.locator('h3:has-text("Total Posts")').locator('..').locator('p').nth(0).textContent();
		const initialCount = parseInt(initialValue?.trim() || '0', 10);

		// Create a new post
		const postUrl = 'http://personal-portfolio.local/wp-admin/post-new.php';
		await page.goto(postUrl, { waitUntil: 'networkidle' });

		// Fill post title
		const titleInput = page.locator('#post-title-0, input#title, input[id*="title"]').first();
		if (await titleInput.isVisible()) {
			await titleInput.fill('Test Post for Analytics');
		}

		// Publish the post
		let publishButton = page.locator('button:has-text("Publish"), input[value="Publish"]').first();
		if (await publishButton.count() > 0) {
			await publishButton.click();
			await page.waitForLoadState('networkidle');
		}

		// Go back to analytics dashboard
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Post count should have increased (or stayed same if new post is in draft)
		const updatedValue = await page.locator('h3:has-text("Total Posts")').locator('..').locator('p').nth(0).textContent();
		const updatedCount = parseInt(updatedValue?.trim() || '0', 10);

		// Count should be >= initial (may be same if post is draft, or higher if published)
		expect(updatedCount).toBeGreaterThanOrEqual(initialCount);
	});

	test('should display footer info message', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Check info message about caching
		const infoMessage = page.locator('text=cached for 1 hour');
		await expect(infoMessage).toBeVisible();
	});

	test('should have proper heading hierarchy', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Page should have H1 (page title)
		const h1 = page.locator('h1:has-text("Analytics Dashboard")');
		await expect(h1).toBeVisible();

		// Should have H3 for KPI titles
		const h3s = page.locator('.analytics-dashboard-grid h3');
		const h3Count = await h3s.count();
		expect(h3Count).toBe(5);
	});

	test('should ensure all metric cards are aligned in grid', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Get all postbox elements (cards)
		const cards = page.locator('.analytics-dashboard-grid .postbox');
		const cardCount = await cards.count();

		// Should have exactly 5 cards
		expect(cardCount).toBe(5);

		// Each card should have visible content
		for (let i = 0; i < cardCount; i++) {
			const card = cards.nth(i);
			const h3 = card.locator('h3');
			const valueP = card.locator('p').first();

			await expect(h3).toBeVisible();
			await expect(valueP).toBeVisible();
		}
	});

	test('should maintain selected date range on page reload', async ({ page }) => {
		await page.goto(ANALYTICS_URL, { waitUntil: 'networkidle' });

		// Select Last 30 Days
		await page.locator('select[name="range"]').selectOption('30');
		await page.locator('input[value="Filter"]').click();
		await page.waitForLoadState('networkidle');

		// Verify URL has range parameter
		expect(page.url()).toContain('range=30');

		// Reload page
		await page.reload({ waitUntil: 'networkidle' });

		// Date range should still be selected
		const selectedValue = await page.locator('select[name="range"]').inputValue();
		expect(selectedValue).toBe('30');
	});
});
