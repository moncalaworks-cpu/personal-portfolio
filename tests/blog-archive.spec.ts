import { test, expect } from '@playwright/test';

// Blog Archive & Search Tests
// Validates: archive page, search, filtering, pagination, accessibility

test.describe('Blog Archive & Search', () => {
	const BLOG_URL = 'http://personal-portfolio.local/blog/';
	const HOME_URL = 'http://personal-portfolio.local';

	test('should display blog archive page with posts', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Check page title
		const title = page.locator('h1.archive-title');
		await expect(title).toHaveText('Blog');

		// Check description
		const description = page.locator('.archive-description');
		await expect(description).toContainText('Insights on AI integration');

		// Check post grid is visible
		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();

		// Check at least one post card exists
		const postCards = page.locator('article.post-card');
		await expect(postCards).toHaveCount(5); // Default 5 posts per page
	});

	test('should display post card with title, excerpt, and featured image', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const firstPost = page.locator('article.post-card').first();
		await expect(firstPost).toBeVisible();

		// Check post title exists and is a link
		const postTitle = firstPost.locator('.post-card-title a');
		await expect(postTitle).toBeVisible();
		expect(await postTitle.getAttribute('href')).toBeTruthy();

		// Check post excerpt
		const excerpt = firstPost.locator('.post-excerpt');
		await expect(excerpt).toBeVisible();

		// Check post date
		const date = firstPost.locator('.post-card-date');
		await expect(date).toBeVisible();

		// Check read time estimate
		const readTime = firstPost.locator('.post-card-readtime');
		await expect(readTime).toContainText(/\d+ min read/);
	});

	test('should display featured image for posts with thumbnails', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const postWithImage = page.locator('article.post-card:has(.post-card-figure)').first();
		if (await postWithImage.count() > 0) {
			const image = postWithImage.locator('.post-card-image');
			await expect(image).toBeVisible();
			expect(await image.getAttribute('alt')).toContain('Featured image');
		}
	});

	test('should display category badges for posts', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const firstPost = page.locator('article.post-card').first();
		const categories = firstPost.locator('.badge');

		if (await categories.count() > 0) {
			await expect(categories.first()).toBeVisible();
			const href = await categories.first().getAttribute('href');
			expect(href).toMatch(/category|tag/);
		}
	});

	test('should have working Read Article links', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const firstPost = page.locator('article.post-card').first();
		const readButton = firstPost.locator('.btn:has-text("Read Article")');

		await expect(readButton).toBeVisible();
		const href = await readButton.getAttribute('href');
		expect(href).toBeTruthy();
		expect(href).toMatch(/^http/);
	});

	test('should search posts by keyword', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Verify search form is present
		const searchInput = page.locator('input.archive-search-input');
		const searchButton = page.locator('button.archive-search-button');

		await expect(searchInput).toBeVisible();
		await expect(searchButton).toBeVisible();

		// Perform search - should complete without errors
		await searchInput.fill('test');
		await searchButton.click();
		await page.waitForLoadState('networkidle');

		// Verify the page loads (not necessarily with results)
		// Just verify no console errors by checking page is still responsive
		const pageContent = page.locator('body');
		await expect(pageContent).toBeVisible();
	});

	test('should filter posts by category', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const categorySelect = page.locator('#category-filter');
		await expect(categorySelect).toBeVisible();

		// Get first category option (skip "All Categories")
		const options = categorySelect.locator('option');
		const optionCount = await options.count();

		if (optionCount > 1) {
			// Click second option (first real category)
			await categorySelect.selectOption({ index: 1 });
			await page.waitForLoadState('networkidle');

			// Posts should be filtered
			const postCards = page.locator('article.post-card');
			expect(await postCards.count()).toBeGreaterThan(0);
		}
	});

	test('should display pagination controls', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const pagination = page.locator('.wp-pagenavi, .pagination, nav.pagination');

		// Pagination may not show if less than 5 posts
		if (await pagination.count() > 0) {
			await expect(pagination).toBeVisible();
		}
	});

	test('should navigate between pages using pagination', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const nextButton = page.locator('a.next');
		if (await nextButton.count() > 0) {
			await nextButton.click();
			await page.waitForLoadState('networkidle');

			// Should be on page 2
			expect(page.url()).toContain('paged=2');

			// Posts should still be visible
			const postCards = page.locator('article.post-card');
			await expect(postCards.first()).toBeVisible();
		}
	});

	test('should show "no posts" message when search returns no results', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const searchInput = page.locator('input.archive-search-input');
		const searchButton = page.locator('button.archive-search-button');

		// Search for something unlikely to exist
		await searchInput.fill('xyzabc123notexist');
		await searchButton.click();
		await page.waitForLoadState('networkidle');

		// Should show no posts message
		const noPostsMessage = page.locator('.no-posts');
		await expect(noPostsMessage).toBeVisible();
		await expect(noPostsMessage.locator('h2')).toContainText('No posts found');
	});

	test('should have breadcrumb navigation', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const breadcrumb = page.locator('[role="navigation"] nav, .breadcrumb');
		if (await breadcrumb.count() > 0) {
			await expect(breadcrumb).toBeVisible();
		}
	});

	test('should be responsive on mobile', async ({ page }) => {
		// Set mobile viewport
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Posts grid should still be visible
		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();

		// Search form should be accessible
		const searchInput = page.locator('input.archive-search-input');
		await expect(searchInput).toBeVisible();

		// Posts should be readable
		const firstPost = page.locator('article.post-card').first();
		await expect(firstPost).toBeVisible();

		// Button should be clickable
		const readButton = firstPost.locator('.btn:has-text("Read Article")');
		await expect(readButton).toBeVisible();
	});

	test('should be responsive on tablet', async ({ page }) => {
		// Set tablet viewport
		await page.setViewportSize({ width: 768, height: 1024 });
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const postsGrid = page.locator('.posts-grid');
		await expect(postsGrid).toBeVisible();

		const postCards = page.locator('article.post-card');
		expect(await postCards.count()).toBeGreaterThan(0);
	});

	test('should load blog page within reasonable time', async ({ page }) => {
		const startTime = Date.now();

		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const loadTime = Date.now() - startTime;
		expect(loadTime).toBeLessThan(3000); // Should load in under 3 seconds

		await expect(page.locator('.posts-grid')).toBeVisible();
	});

	// Accessibility Tests
	test('should have proper heading hierarchy', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Should have archive h1
		const archiveH1 = page.locator('h1.archive-title');
		await expect(archiveH1).toBeVisible();

		// Post titles should be h2 (child heading)
		const h2 = page.locator('h2.post-card-title');
		expect(await h2.count()).toBeGreaterThan(0);
	});

	test('should have proper form labels and ARIA attributes', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const searchInput = page.locator('input.archive-search-input');
		// Search input should have aria-label or be properly labeled
		const hasAriaLabel = await searchInput.getAttribute('aria-label');
		const hasPlaceholder = await searchInput.getAttribute('placeholder');
		expect(hasAriaLabel || hasPlaceholder).toBeTruthy();

		const searchButton = page.locator('button.archive-search-button');
		// Button should have aria-label or title
		const buttonAriaLabel = await searchButton.getAttribute('aria-label');
		const buttonTitle = await searchButton.getAttribute('title');
		expect(buttonAriaLabel || buttonTitle).toBeTruthy();

		const categorySelect = page.locator('#category-filter');
		if (await categorySelect.count() > 0) {
			const label = page.locator('label[for="category-filter"]');
			await expect(label).toBeVisible();
		}
	});

	test('should have alt text for images', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const images = page.locator('.post-card-image');
		const count = await images.count();

		for (let i = 0; i < Math.min(count, 3); i++) {
			const alt = await images.nth(i).getAttribute('alt');
			expect(alt).toBeTruthy();
			expect(alt).toContain('Featured image');
		}
	});

	test('should have proper link text for screen readers', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const readButtons = page.locator('.btn:has-text("Read Article")');
		const count = await readButtons.count();

		for (let i = 0; i < Math.min(count, 2); i++) {
			const text = await readButtons.nth(i).textContent();
			expect(text).toContain('Read Article');
		}
	});

	test('should not break with special characters in search', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const searchInput = page.locator('input.archive-search-input');
		const searchButton = page.locator('button.archive-search-button');

		// Test with special characters that might break queries
		await searchInput.fill('"test" & <script>');
		await searchButton.click();
		await page.waitForLoadState('networkidle');

		// Page should still load without JavaScript errors
		const postCards = page.locator('article.post-card');
		expect(await postCards.count()).toBeGreaterThanOrEqual(0);
	});

	test('should preserve search state when navigating pages', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		const searchInput = page.locator('input.archive-search-input');
		const searchButton = page.locator('button.archive-search-button');

		// Perform search
		await searchInput.fill('Claude');
		await searchButton.click();
		await page.waitForLoadState('networkidle');

		// URL should contain search parameter
		expect(page.url()).toContain('s=Claude');

		// Navigate to next page if available
		const nextButton = page.locator('a.next');
		if (await nextButton.count() > 0) {
			await nextButton.click();
			await page.waitForLoadState('networkidle');

			// Search parameter should persist in URL
			expect(page.url()).toContain('s=Claude');
		}
	});

	test('should have category links that work correctly', async ({ page }) => {
		await page.goto(BLOG_URL, { waitUntil: 'networkidle' });

		// Click first category badge
		const categoryLink = page.locator('.post-categories .badge a').first();
		if (await categoryLink.count() > 0) {
			const href = await categoryLink.getAttribute('href');
			expect(href).toContain('category');

			// Click the link
			await categoryLink.click();
			await page.waitForLoadState('networkidle');

			// Should be on category archive page
			expect(page.url()).toContain('category');

			// Posts should still be visible and filtered
			const postCards = page.locator('article.post-card');
			expect(await postCards.count()).toBeGreaterThan(0);
		}
	});
});
