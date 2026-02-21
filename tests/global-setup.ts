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

    // Activate plugins if not already active
    await page.goto(`${WORDPRESS_URL}/wp-admin/plugins.php`, { waitUntil: 'networkidle' });

    // Activate post-editor-showcase plugin
    const activateLink1 = page.locator('a[href*="post-editor-showcase"]').filter({ hasText: /activate|activate plugin/i }).first();
    if (await activateLink1.count() > 0) {
      console.log('📦 Activating post-editor-showcase plugin...');
      await activateLink1.click();
      await page.waitForLoadState('networkidle');
      console.log('✅ post-editor-showcase activated');
    } else {
      console.log('ℹ️  post-editor-showcase already active');
    }

    // Activate task-manager plugin
    await page.reload({ waitUntil: 'networkidle' });
    const activateLink2 = page.locator('a[href*="task-manager"]').filter({ hasText: /activate|activate plugin/i }).first();
    if (await activateLink2.count() > 0) {
      console.log('📦 Activating task-manager plugin...');
      await activateLink2.click();
      await page.waitForLoadState('networkidle');
      console.log('✅ task-manager activated');
    } else {
      console.log('ℹ️  task-manager already active');
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
