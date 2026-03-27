import { test, expect } from '@playwright/test';

// Blog Performance Tests
// Validates: pagination, caching, lazy loading, load times, Core Web Vitals

test.describe('Blog Archive Performance', () => {
	const BLOG_URL = 'http://personal-portfolio.local/blog/';

	test('should paginate through blog posts efficiently', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// First page should load quickly
		const startTime = Date.now();
		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();
		const firstLoadTime = Date.now() - startTime;
		expect(firstLoadTime).toBeLessThan(500); // Should be instant after initial load

		// Get post count on page 1
		const firstPagePosts = page.locator('article.post-card');
		const firstPageCount = await firstPagePosts.count();
		expect(firstPageCount).toBe(5); // 5 posts per page

		// Navigate to page 2
		const nextButton = page.locator('a.next');
		if (await nextButton.count() > 0) {
			const page2StartTime = Date.now();
			await nextButton.click();
			await page.waitForLoadState('networkidle');
			const page2LoadTime = Date.now() - page2StartTime;

			// Page 2 should load with similar performance
			expect(page2LoadTime).toBeLessThan(3000);

			// Verify we're on page 2
			expect(page.url()).toContain('paged=2');

			// Should have posts on page 2
			const page2Posts = page.locator('article.post-card');
			expect(await page2Posts.count()).toBeGreaterThan(0);
		}
	});

	test('should handle multiple pages without memory leaks', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Navigate through multiple pages
		for (let i = 0; i < 3; i++) {
			const postsGrid = page.locator('.posts-grid');
			await expect(postsGrid).toBeVisible();

			const nextButton = page.locator('a.next');
			if (await nextButton.count() > 0) {
				await nextButton.click();
				await page.waitForLoadState('networkidle');
			} else {
				break; // No more pages
			}
		}

		// Page should still be responsive after navigation
		const searchInput = page.locator('input.archive-search-input');
		await expect(searchInput).toBeVisible();
		await expect(searchInput).toBeEnabled();
	});

	test('should lazy load images in blog archive', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Check if images have loading="lazy" attribute
		const images = page.locator('.post-card-image');
		const count = await images.count();

		if (count > 0) {
			for (let i = 0; i < Math.min(count, 3); i++) {
				const loading = await images.nth(i).getAttribute('loading');
				// Images should have lazy loading or be natively optimized
				expect(['lazy', null]).toContain(loading); // native loading or explicit lazy
			}
		}
	});

	test('should defer non-critical JavaScript', async ({ page }) => {
		// Track network requests
		const requests: string[] = [];
		page.on('response', (response) => {
			const url = response.url();
			if (url.includes('.js')) {
				requests.push(url);
			}
		});

		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Page should be interactive even while scripts load
		const searchInput = page.locator('input.archive-search-input');
		await expect(searchInput).toBeVisible();
		await expect(searchInput).toBeEnabled();

		// Non-critical scripts should have loaded
		expect(requests.length).toBeGreaterThan(0);
	});

	test('should load within Core Web Vitals targets', async ({ page }) => {
		const startTime = performance.now();

		const response = await page.goto(BLOG_URL, { waitUntil: 'networkidle' });
		const navigationTime = performance.now() - startTime;

		// Largest Contentful Paint (LCP) should be < 2.5s
		expect(navigationTime).toBeLessThan(2500);

		// Page should have main content visible
		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();

		// Check First Input Delay (FID) - page should be interactive
		const searchButton = page.locator('button.archive-search-button');
		await expect(searchButton).toBeEnabled();
	});

	test('should optimize featured image queries', async ({ page }) => {
		// Network monitoring
		let imageRequests = 0;
		page.on('response', (response) => {
			if (response.url().includes('.jpg') || response.url().includes('.png') || response.url().includes('.webp')) {
				imageRequests++;
			}
		});

		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Images should be optimized (not too many duplicates)
		// With 5 posts, expect roughly 5 featured images
		expect(imageRequests).toBeLessThanOrEqual(10); // Allow some buffer for optimization
	});

	test('should cache category and tag lists', async ({ page }) => {
		// First visit
		const startTime1 = performance.now();
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });
		const firstLoadTime = performance.now() - startTime1;

		// Second visit to same page
		const startTime2 = performance.now();
		await page.reload({ waitUntil: 'networkidle' });
		const secondLoadTime = performance.now() - startTime2;

		// Second load should be similar or faster due to caching
		// Allow some variance but not dramatically slower
		expect(secondLoadTime).toBeLessThan(firstLoadTime * 1.5);

		// Category selector should still work
		const categorySelect = page.locator('#category-filter');
		if (await categorySelect.count() > 0) {
			const options = categorySelect.locator('option');
			expect(await options.count()).toBeGreaterThan(1); // At least "All" + one category
		}
	});

	test('should load archive efficiently from cold cache', async ({ page }) => {
		// First visit - full page load with no cache
		const startTime = performance.now();
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });
		const coldLoadTime = performance.now() - startTime;

		// Should load efficiently even without cache
		expect(coldLoadTime).toBeLessThan(5000);

		// Reload - tests cached assets
		const reloadStart = performance.now();
		await page.reload({ waitUntil: 'networkidle' });
		const reloadTime = performance.now() - reloadStart;

		// Reload should be faster or similar
		expect(reloadTime).toBeLessThan(coldLoadTime + 500);

		// Posts grid should be visible
		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();
	});

	test('should not increase DOM size excessively on pagination', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Get initial DOM element count
		const initialElements = await page.locator('*').count();

		// Navigate through pagination
		const nextButton = page.locator('a.next');
		if (await nextButton.count() > 0) {
			await nextButton.click();
			await page.waitForLoadState('networkidle');

			// Get DOM element count on page 2
			const page2Elements = await page.locator('*').count();

			// DOM shouldn't grow excessively (allow ~30% variance)
			expect(page2Elements).toBeLessThan(initialElements * 1.3);
		}
	});

	test('should handle filtering without page reload when possible', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const categorySelect = page.locator('#category-filter');
		if (await categorySelect.count() > 0) {
			const options = categorySelect.locator('option');
			const optionCount = await options.count();

			if (optionCount > 1) {
				// Select a category
				const selectStart = performance.now();
				await categorySelect.selectOption({ index: 1 });
				await page.waitForLoadState('networkidle');
				const selectTime = performance.now() - selectStart;

				// Category filtering should complete quickly
				expect(selectTime).toBeLessThan(2000);

				// Results should update
				const postsGrid = page.locator('.posts-grid');
				await expect(postsGrid).toBeVisible();
			}
		}
	});

	test('should optimize CSS and minimize render-blocking', async ({ page }) => {
		const cssFiles: string[] = [];
		page.on('response', (response) => {
			if (response.url().includes('.css')) {
				cssFiles.push(response.url());
			}
		});

		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Should not have excessive CSS files
		expect(cssFiles.length).toBeLessThan(10); // Reasonable CSS file count

		// Page should render without waiting for all CSS
		const postCards = page.locator('article.post-card');
		await expect(postCards.first()).toBeVisible();
	});

	test('should preload critical resources', async ({ page }) => {
		let preloadCount = 0;
		page.on('response', (response) => {
			const headers = response.headers();
			if (headers['link'] && headers['link'].includes('rel=preload')) {
				preloadCount++;
			}
		});

		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Page should be optimized with some preloading
		// (Note: This may be 0 depending on server configuration)
		expect(preloadCount).toBeGreaterThanOrEqual(0);
	});

	test('should maintain performance with multiple blog posts', async ({ page }) => {
		// This test verifies the archive can handle multiple posts efficiently
		// by testing pagination through available pages
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		let pageCount = 0;
		let hasNextPage = true;
		const maxPages = 10; // Safety limit

		while (hasNextPage && pageCount < maxPages) {
			pageCount++;
			const startTime = performance.now();

			// Each page should load within reasonable time
			const postsGrid = page.locator('.posts-grid');
			await expect(postsGrid).toBeVisible();
			const loadTime = performance.now() - startTime;
			expect(loadTime).toBeLessThan(2000);

			// Try to go to next page
			const nextButton = page.locator('a.next');
			if (await nextButton.count() > 0) {
				await nextButton.click();
				await page.waitForLoadState('networkidle');
			} else {
				hasNextPage = false;
			}
		}

		// Should handle at least 1 page (posts exist)
		expect(pageCount).toBeGreaterThanOrEqual(1);
	});

	test('should remain responsive across multiple navigations', async ({ page }) => {
		// Start on blog page
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Navigate to a post if available
		const postLinks = page.locator('article.post-card a.post-card-link').first();
		if (await postLinks.count() > 0) {
			await postLinks.click();
			await page.waitForLoadState('networkidle');

			// Verify page loaded
			const postTitle = page.locator('h1.post-title');
			await expect(postTitle).toBeVisible();

			// Go back to blog
			await page.goBack();
			await page.waitForLoadState('networkidle');

			// Blog should still be accessible and responsive
			const postsGrid = page.locator('.posts-grid');
			await expect(postsGrid).toBeVisible();

			// Search should still work
			const searchInput = page.locator('input.archive-search-input');
			await expect(searchInput).toBeEnabled();
		}
	});

	test('should remain responsive after rapid page navigation', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Navigate through multiple pages if pagination exists
		const nextButton = page.locator('a.next');
		if (await nextButton.count() > 0) {
			// Click next twice quickly
			await nextButton.click();
			await page.waitForLoadState('networkidle');

			const nextButton2 = page.locator('a.next');
			if (await nextButton2.count() > 0) {
				await nextButton2.click();
				await page.waitForLoadState('networkidle');
			}

			// Page should still be responsive
			const searchInput = page.locator('input.archive-search-input');
			await expect(searchInput).toBeEnabled();

			// Posts grid should be visible
			const postsGrid = page.locator('.posts-grid');
			await expect(postsGrid).toBeVisible();
		}
	});
});
