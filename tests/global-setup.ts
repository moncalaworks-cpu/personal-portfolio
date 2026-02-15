import { chromium, FullConfig } from '@playwright/test';
import * as dotenv from 'dotenv';
import * as path from 'path';

dotenv.config();

async function globalSetup(config: FullConfig) {
  const WORDPRESS_URL = process.env.WORDPRESS_URL || 'http://personal-portfolio.local';
  const ADMIN_USER = process.env.WORDPRESS_ADMIN_USER || 'admin';
  const ADMIN_PASS = process.env.WORDPRESS_ADMIN_PASSWORD || 'password';
  const authFile = path.join(__dirname, 'auth.json');

  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('🔐 Global Setup: Logging in to WordPress...');

    // Navigate to login page
    await page.goto(`${WORDPRESS_URL}/wp-login.php`, { waitUntil: 'networkidle' });

    // Fill in credentials
    await page.locator('input[name="log"]').fill(ADMIN_USER);
    await page.locator('input[name="pwd"]').fill(ADMIN_PASS);

    // Submit form
    await page.locator('input[type="submit"]').click();

    // Wait for redirect to admin
    await Promise.race([
      page.waitForURL(`**\/wp-admin\/**`, { timeout: 15000 }),
      page.waitForLoadState('networkidle', { timeout: 15000 })
    ]);

    console.log('✅ Successfully logged in to WordPress admin');

    // Activate post-editor-showcase plugin if not already active
    await page.goto(`${WORDPRESS_URL}/wp-admin/plugins.php`, { waitUntil: 'networkidle' });

    // Check if plugin needs activation
    const activateLink = page.locator('a[href*="post-editor-showcase"]').filter({ hasText: /activate|activate plugin/i }).first();
    if (await activateLink.count() > 0) {
      console.log('📦 Activating post-editor-showcase plugin...');
      await activateLink.click();
      await page.waitForLoadState('networkidle');
      console.log('✅ Plugin activated');
    } else {
      console.log('ℹ️  Post-editor-showcase plugin already active');
    }

    // Save authentication state
    await context.storageState({ path: authFile });
    console.log(`✅ Auth state saved to ${authFile}`);

  } catch (error) {
    console.error('❌ Login failed:', error);
    throw error;
  } finally {
    await context.close();
    await browser.close();
  }
}

export default globalSetup;
