import { test, expect } from '@playwright/test';
import * as dotenv from 'dotenv';

// Load .env file
dotenv.config();

const WORDPRESS_URL = process.env.WORDPRESS_URL || 'http://personal-portfolio.local';
const ADMIN_USER = process.env.WORDPRESS_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WORDPRESS_ADMIN_PASSWORD || 'password';

test.describe('Authentication Only', () => {
  test('Should login to WordPress admin', async ({ page }) => {
    console.log(`Attempting login to ${WORDPRESS_URL}`);
    console.log(`Username: ${ADMIN_USER}`);

    // Step 1: Navigate to login page
    console.log('Step 1: Navigating to login page...');
    await page.goto(`${WORDPRESS_URL}/wp-login.php`, { waitUntil: 'domcontentloaded' });

    // Step 2: Check login form exists
    console.log('Step 2: Checking login form...');
    const usernameField = page.locator('input[name="log"]');
    await expect(usernameField).toBeVisible({ timeout: 5000 });
    console.log('✓ Login form found');

    // Step 3: Fill credentials
    console.log('Step 3: Filling credentials...');
    await usernameField.fill(ADMIN_USER);
    await page.locator('input[name="pwd"]').fill(ADMIN_PASS);
    console.log('✓ Credentials entered');

    // Step 4: Submit form
    console.log('Step 4: Submitting login form...');
    await page.locator('input[type="submit"]').click();
    console.log('✓ Form submitted');

    // Step 5: Wait for redirect with detailed logging
    console.log('Step 5: Waiting for redirect...');
    const currentUrl = page.url();
    console.log(`Current URL before wait: ${currentUrl}`);

    try {
      // Wait for URL change
      await page.waitForURL(/wp-admin/, { timeout: 10000 });
      console.log(`✓ URL changed to: ${page.url()}`);
    } catch (e) {
      console.log(`✗ URL wait failed: ${e.message}`);
      console.log(`Current URL: ${page.url()}`);
      console.log(`Page content preview: ${(await page.content()).substring(0, 500)}`);
      throw e;
    }

    // Step 6: Verify we're in admin
    console.log('Step 6: Verifying admin access...');
    const bodyClass = await page.getAttribute('body', 'class');
    console.log(`Body classes: ${bodyClass}`);

    // Check for admin-specific content
    const adminBar = page.locator('#wpadminbar');
    console.log(`Admin bar visible: ${await adminBar.isVisible().catch(() => 'error checking')}`);

    // Final check - we should be on an admin page
    expect(page.url()).toContain('/wp-admin');
    console.log('✓ Login successful!');
  });
});
