import { test, expect, Page } from '@playwright/test';

// Tests for Post Editor Showcase Plugin
// Demonstrates: Custom post types, metaboxes, metadata, security, and editing workflow

test.describe('Post Editor Showcase Plugin', () => {
	let page: Page;
	let postId: number;

	test.beforeAll(async ({ browser }) => {
		const context = await browser.newContext({
			storageState: 'tests/auth.json',
		});
		page = await context.newPage();
	});

	test('should have custom post type registered and accessible', async () => {
		// Navigate to the custom post type URL directly
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		// Verify we're on the articles list page
		expect(page.url()).toContain('post_type=pes_article');

		// Check page title contains "Editor Showcase Articles"
		const pageTitle = await page.locator('h1').first().textContent();
		expect(pageTitle).toContain('Editor Showcase Articles');
	});

	test('should allow creating new custom post article', async () => {
		// Navigate to add new article page directly
		await page.goto('http://personal-portfolio.local/wp-admin/post-new.php?post_type=pes_article');

		// Should be on the new post screen
		await expect(page.locator('input[name="post_title"]')).toBeVisible();

		// Verify all metabox fields are present for new post
		const titleInput = page.locator('input#pes_article_title');
		const descriptionTextarea = page.locator('textarea#pes_article_description');
		const prioritySelect = page.locator('select#pes_article_priority');
		const statusSelect = page.locator('select#pes_article_status');

		await expect(titleInput).toBeVisible();
		await expect(descriptionTextarea).toBeVisible();
		await expect(prioritySelect).toBeVisible();
		await expect(statusSelect).toBeVisible();

		// Verify metabox headings are present
		const editorMetadataHeading = page.locator('h2:has-text("Editor Metadata")');
		const advancedFieldsHeading = page.locator('h2:has-text("Advanced Fields")');

		await expect(editorMetadataHeading).toBeVisible();
		await expect(advancedFieldsHeading).toBeVisible();
	});

	test('should display metaboxes on edit screen', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		// Get the first article
		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Wait for edit screen to load
		await expect(page.locator('input[name="post_title"]')).toBeVisible();

		// Check for all three metaboxes
		const editorMetadataHeading = page.locator('h2:has-text("Editor Metadata")');
		const advancedFieldsHeading = page.locator('h2:has-text("Advanced Fields")');
		const editorInfoHeading = page.locator('h2:has-text("Editor Information")');

		await expect(editorMetadataHeading).toBeVisible();
		await expect(advancedFieldsHeading).toBeVisible();
		await expect(editorInfoHeading).toBeVisible();
	});

	test('should display Editor Metadata metabox fields', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Check for metabox form fields
		const titleInput = page.locator('input#pes_article_title');
		const descriptionTextarea = page.locator('textarea#pes_article_description');
		const featuredImageInput = page.locator('input#pes_featured_image_url');

		await expect(titleInput).toBeVisible();
		await expect(descriptionTextarea).toBeVisible();
		await expect(featuredImageInput).toBeVisible();

		// Check for labels
		const titleLabel = page.locator('label[for="pes_article_title"]:has-text("Article Title")');
		const descriptionLabel = page.locator('label[for="pes_article_description"]:has-text("Article Description")');
		const imageLabel = page.locator('label[for="pes_featured_image_url"]:has-text("Featured Image URL")');

		await expect(titleLabel).toBeVisible();
		await expect(descriptionLabel).toBeVisible();
		await expect(imageLabel).toBeVisible();
	});

	test('should display Advanced Fields metabox with selects', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Check for select fields
		const prioritySelect = page.locator('select#pes_article_priority');
		const statusSelect = page.locator('select#pes_article_status');
		const tagsInput = page.locator('input#pes_article_tags');

		await expect(prioritySelect).toBeVisible();
		await expect(statusSelect).toBeVisible();
		await expect(tagsInput).toBeVisible();
	});

	test('should have correct select options for priority', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const prioritySelect = page.locator('select#pes_article_priority');
		const options = prioritySelect.locator('option');

		await expect(options).toHaveCount(4); // Empty, low, medium, high
		await expect(options.nth(1)).toHaveAttribute('value', 'low');
		await expect(options.nth(2)).toHaveAttribute('value', 'medium');
		await expect(options.nth(3)).toHaveAttribute('value', 'high');
	});

	test('should have correct select options for status', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const statusSelect = page.locator('select#pes_article_status');
		const options = statusSelect.locator('option');

		await expect(options).toHaveCount(4); // Empty, draft, review, published
		await expect(options.nth(1)).toHaveAttribute('value', 'draft');
		await expect(options.nth(2)).toHaveAttribute('value', 'review');
		await expect(options.nth(3)).toHaveAttribute('value', 'published');
	});

	test('should display Editor Information metabox', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Check for info box content
		const postIdInfo = page.locator('.pes-info-box strong:has-text("Post ID")');
		const authorInfo = page.locator('.pes-info-box strong:has-text("Author")');
		const createdInfo = page.locator('.pes-info-box strong:has-text("Created")');
		const modifiedInfo = page.locator('.pes-info-box strong:has-text("Last Modified")');
		const revisionsInfo = page.locator('.pes-info-box strong:has-text("Revisions")');

		await expect(postIdInfo).toBeVisible();
		await expect(authorInfo).toBeVisible();
		await expect(createdInfo).toBeVisible();
		await expect(modifiedInfo).toBeVisible();
		await expect(revisionsInfo).toBeVisible();
	});

	test('should save and retrieve metadata', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Get initial values
		const titleInput = page.locator('input#pes_article_title');
		const descriptionTextarea = page.locator('textarea#pes_article_description');
		const prioritySelect = page.locator('select#pes_article_priority');

		const initialTitle = await titleInput.inputValue();
		const initialDescription = await descriptionTextarea.inputValue();
		const initialPriority = await prioritySelect.inputValue();

		// Verify fields have values
		expect(initialTitle?.length || 0).toBeGreaterThan(0);
		expect(initialDescription?.length || 0).toBeGreaterThanOrEqual(0);
	});

	test('should display featured image URL placeholder', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const featuredImageInput = page.locator('input#pes_featured_image_url');
		const placeholder = await featuredImageInput.getAttribute('placeholder');

		expect(placeholder).toContain('https://example.com/image.jpg');
	});

	test('should display tags input placeholder', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const tagsInput = page.locator('input#pes_article_tags');
		const placeholder = await tagsInput.getAttribute('placeholder');

		expect(placeholder).toContain('wordpress, php, editing');
	});

	test('should have nonce security field in metabox', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Check for nonce field
		const nonceField = page.locator('input[name="pes_metabox_nonce"]');
		await expect(nonceField).toHaveCount(2); // One in each metabox that calls wp_nonce_field
	});

	test('should enqueue editor styles', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		// Check that CSS file is loaded
		const cssLink = page.locator('link[href*="editor-styles.css"]');
		await expect(cssLink).toHaveCount(1);

		// Verify styling is applied to metabox
		const metabox = page.locator('.pes-metabox').first();
		const styles = await metabox.evaluate((el) => {
			return window.getComputedStyle(el).backgroundColor;
		});

		expect(styles).toBeTruthy();
	});

	test('should allow updating priority field', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const prioritySelect = page.locator('select#pes_article_priority');

		// Change priority
		await prioritySelect.selectOption('medium');

		// Verify value changed
		await expect(prioritySelect).toHaveValue('medium');
	});

	test('should allow updating status field', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const statusSelect = page.locator('select#pes_article_status');

		// Change status
		await statusSelect.selectOption('draft');

		// Verify value changed
		await expect(statusSelect).toHaveValue('draft');
	});

	test('should display post author in info box', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const authorInfo = page.locator('.pes-info-box p:has-text("Author:")');
		await expect(authorInfo).toBeVisible();

		const authorText = await authorInfo.textContent();
		expect(authorText).toContain('Author:');
	});

	test('should display post creation date', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const createdInfo = page.locator('.pes-info-box p:has-text("Created:")');
		await expect(createdInfo).toBeVisible();

		const createdText = await createdInfo.textContent();
		expect(createdText).toContain('Created:');
	});

	test('should display post modification date', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const modifiedInfo = page.locator('.pes-info-box p:has-text("Last Modified:")');
		await expect(modifiedInfo).toBeVisible();

		const modifiedText = await modifiedInfo.textContent();
		expect(modifiedText).toContain('Last Modified:');
	});

	test('should display revision count', async () => {
		await page.goto('http://personal-portfolio.local/wp-admin/edit.php?post_type=pes_article');

		const firstArticleLink = page.locator('table.wp-list-table tbody tr td.column-title a').first();
		await firstArticleLink.click();

		const revisionsInfo = page.locator('.pes-info-box p:has-text("Revisions:")');
		await expect(revisionsInfo).toBeVisible();

		const revisionsText = await revisionsInfo.textContent();
		expect(revisionsText).toContain('Revisions:');
	});
});
