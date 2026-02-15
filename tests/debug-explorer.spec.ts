import { test, expect } from '@playwright/test';
import * as dotenv from 'dotenv';

dotenv.config();

const WORDPRESS_URL = process.env.WORDPRESS_URL || 'http://personal-portfolio.local';
const ADMIN_USER = process.env.WORDPRESS_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WORDPRESS_ADMIN_PASSWORD || 'password';

async function loginToWordPress(page) {
	try {
		await page.goto(`${WORDPRESS_URL}/wp-login.php`, { waitUntil: 'networkidle' });
		const usernameField = page.locator('input[name="log"]');
		await usernameField.waitFor({ state: 'visible', timeout: 5000 });
		await usernameField.fill(ADMIN_USER);
		await page.locator('input[name="pwd"]').fill(ADMIN_PASS);
		const submitButton = page.locator('input[type="submit"]');
		await submitButton.click();
		await Promise.race([
			page.waitForURL(`**\/wp-admin\/**`, { timeout: 15000 }),
			page.waitForLoadState('networkidle', { timeout: 15000 })
		]);
	} catch (error) {
		console.error('Login failed:', error.message);
		throw error;
	}
}

test('Debug: Database Explorer page content', async ({ page }) => {
	await loginToWordPress(page);
	await page.goto(`${WORDPRESS_URL}/wp-admin/admin.php?page=de-explorer`);
	await page.waitForLoadState('networkidle');

	// Get full page HTML
	const html = await page.content();
	console.log('\n=== PAGE HTML (first 2000 chars) ===');
	console.log(html.substring(0, 2000));

	// Check for h2 elements
	const h2s = await page.locator('h2').all();
	console.log(`\n=== Found ${h2s.length} h2 elements ===`);
	for (let i = 0; i < h2s.length; i++) {
		const text = await h2s[i].textContent();
		const visible = await h2s[i].isVisible().catch(() => 'error');
		console.log(`h2[${i}]: "${text}" | visible: ${visible}`);
	}

	// Check for card elements
	const cards = await page.locator('.card').all();
	console.log(`\n=== Found ${cards.length} .card elements ===`);
	for (let i = 0; i < cards.length; i++) {
		const visible = await cards[i].isVisible().catch(() => 'error');
		const html = await cards[i].innerHTML();
		console.log(`card[${i}]: visible=${visible}, html length=${html.length}`);
		console.log(`  Content preview: ${html.substring(0, 150)}`);
	}

	// Check for tables
	const tables = await page.locator('table').all();
	console.log(`\n=== Found ${tables.length} table elements ===`);
	for (let i = 0; i < tables.length; i++) {
		const visible = await tables[i].isVisible().catch(() => 'error');
		const rows = await tables[i].locator('tbody tr').count();
		console.log(`table[${i}]: visible=${visible}, rows=${rows}`);
	}

	// Check page structure
	const wrap = page.locator('.wrap');
	const wrapVisible = await wrap.isVisible().catch(() => false);
	console.log(`\n=== Wrap element ===`);
	console.log(`Visible: ${wrapVisible}`);
	const wrapHtml = await wrap.innerHTML().catch(() => 'error');
	console.log(`HTML length: ${typeof wrapHtml === 'string' ? wrapHtml.length : wrapHtml}`);

	// Take screenshot
	await page.screenshot({ path: '/tmp/debug-explorer.png', fullPage: true });
	console.log('\nScreenshot saved to /tmp/debug-explorer.png');
});
