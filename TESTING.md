# Automated Testing with Playwright

This guide explains how to set up and run automated tests for the personal-portfolio WordPress site using Playwright.

## Why Test?

When building WordPress plugins and features, testing ensures:
- ✅ Code works as expected
- ✅ Changes don't break existing features
- ✅ Plugin demonstrates core concepts correctly
- ✅ Responsive design works on mobile
- ✅ Frontend and backend integration works

## Setup

### 1. Install Dependencies

```bash
npm install
```

This installs:
- `@playwright/test` - Testing framework
- Browsers (Chromium, Firefox, WebKit)

### 2. Configure Your WordPress URL

Copy `.env.example` to `.env` and update with your local site URL:

```bash
cp .env.example .env
```

Edit `.env`:
```
WORDPRESS_URL=http://personal-portfolio.test
WORDPRESS_ADMIN_USER=admin
WORDPRESS_ADMIN_PASSWORD=password
```

**Find your local WordPress URL:**
- Open Local by Flywheel
- Find your site
- Click the site URL in the top bar
- Use that as `WORDPRESS_URL`

### 3. Ensure Your Site is Running

Make sure your Local by Flywheel site is running before running tests.

## Running Tests

### Run All Tests
```bash
npm test
```

### Run Tests in Headed Mode (See Browser)
```bash
npm run test:headed
```

### Run Tests in UI Mode (Interactive)
```bash
npm run test:ui
```

### Debug Tests (Step through)
```bash
npm run test:debug
```

### View Test Report
After tests run:
```bash
npm run test:report
```

## Test Files

### `tests/hello-world.spec.ts`

Tests for the Hello World plugin. Structure:

```typescript
test.describe('Hello World Plugin', () => {
  test('Plugin is visible in WordPress admin', async ({ page }) => {
    // Test code
  });
});
```

**Test Groups:**

1. **Basic Functionality Tests**
   - Plugin visibility in admin
   - Page creation
   - Frontend display
   - Styling application

2. **Content Tests**
   - Page content displays
   - Custom HTML wrapper works
   - Original content preserved

3. **Responsive Tests**
   - Mobile viewport works
   - No overflow on small screens

4. **Hook Verification Tests**
   - `init` hook creates page
   - `the_content` filter wraps content
   - `wp_enqueue_scripts` loads CSS

5. **Error Handling Tests**
   - Plugin doesn't break other pages
   - Admin area works correctly

6. **Issue #1 Validation Tests**
   - All Issue #1 learning objectives covered

## Understanding the Tests

### Example: Plugin Visibility Test

```typescript
test('Plugin is visible in WordPress admin', async ({ page }) => {
  // 1. Navigate to plugins page
  await page.goto(`${WORDPRESS_URL}/wp-admin/plugins.php`);

  // 2. Wait for page to fully load
  await page.waitForLoadState('networkidle');

  // 3. Look for the plugin
  const helloWorldPlugin = page.locator('text=Hello World');

  // 4. Assert it exists
  await expect(helloWorldPlugin).toBeVisible();
});
```

### Playwright Concepts

**Locators** - Find elements on page:
```typescript
page.locator('text=Hello World')      // By text
page.locator('.hello-world-title')    // By CSS class
page.locator('button')                // By tag
page.locator('[aria-label="Save"]')   // By attribute
```

**Actions** - Interact with page:
```typescript
await page.goto(url)           // Navigate to URL
await page.click('button')     // Click button
await page.fill('input', 'text') // Type in input
await page.waitForLoadState()  // Wait for page load
```

**Assertions** - Verify behavior:
```typescript
await expect(element).toBeVisible()
await expect(element).toContainText('text')
await expect(page).toHaveURL('http://...')
```

## Verifying Your Changes

### Before Committing Code:

1. **Run tests to verify changes work:**
   ```bash
   npm test
   ```

2. **Fix any failing tests:**
   - Read the error message
   - Check what the test expects
   - Update code to match
   - Re-run tests

3. **View detailed results:**
   ```bash
   npm run test:report
   ```

### Example Workflow:

```bash
# 1. Make code changes to plugin
# (edit hello-world.php, CSS, etc.)

# 2. Run tests
npm test

# 3. If tests fail, read error
# Example: "Error: hello-world-container not visible"
# Means CSS class or element is missing

# 4. Fix code
# (add missing element or class)

# 5. Re-run tests
npm test

# 6. Once all pass, commit
git add app/public/wp-content/plugins/hello-world/
git commit -m "fix: Update Hello World plugin styling"
```

## Test Coverage Map

### Issue #1 - Core Codebase Overview

Tests validate these learning objectives:

| Objective | Test | What It Checks |
|-----------|------|----------------|
| **Core File Structure** | Plugin visibility | Plugin is in correct directory and readable |
| **Hook System** | Hook Verification Tests | init, wp_enqueue_scripts, the_content all work |
| **Plugin API** | Plugin visibility, Page creation | wp_insert_post(), add_action(), add_filter() work |
| **Bootstrap Process** | init hook test | init hook fires during WordPress bootstrap |
| **Content Filtering** | the_content filter test | the_content filter properly modifies output |
| **Resource Enqueueing** | CSS load test | wp_enqueue_style() properly loads stylesheet |

## Troubleshooting Tests

### "Connection refused" error
- **Problem:** Can't connect to WordPress site
- **Solution:** Make sure your site is running in Local by Flywheel
- **Solution:** Verify `WORDPRESS_URL` in `.env` is correct

### "Timeout waiting for element"
- **Problem:** Test can't find element it's looking for
- **Solution:** Run with `--headed` to see what's happening: `npm run test:headed`
- **Solution:** Check if element exists with correct class/text

### "Navigation timeout"
- **Problem:** Page takes too long to load
- **Solution:** Page might have heavy queries - check WordPress debug log
- **Solution:** Increase timeout: `page.waitForLoadState('networkidle', { timeout: 60000 })`

### Tests pass locally but fail in CI
- **Problem:** Environment differences
- **Solution:** Make sure WordPress URL is configured
- **Solution:** Check that plugin is actually activated in WordPress

## Next Steps

### When to Add Tests

When you complete each Issue, add tests for it:

- **Issue #2 (Database):** Add tests for post queries, meta data
- **Issue #3 (Editing):** Add tests for admin pages, editing workflows
- **Issue #4 (Plugins):** Add tests for plugin features
- **Issue #5 (Maintenance):** Add tests for updates, migrations

### Continuous Testing

Make testing part of your workflow:

1. **Before committing:** Run tests to verify nothing broke
2. **Before pushing:** Run tests in headed mode to see actual behavior
3. **In code review:** Tests document what the code should do

## Advanced: Creating Custom Tests

### Template for New Test

```typescript
test('Your test name', async ({ page }) => {
  // 1. Navigate
  await page.goto(`${WORDPRESS_URL}/your-page/`);

  // 2. Wait for load
  await page.waitForLoadState('networkidle');

  // 3. Find elements
  const element = page.locator('.your-class');

  // 4. Assert
  await expect(element).toBeVisible();
});
```

### Debugging Tips

1. **Take screenshot:**
   ```typescript
   await page.screenshot({ path: 'debug.png' });
   ```

2. **Print element text:**
   ```typescript
   console.log(await element.textContent());
   ```

3. **Check all text on page:**
   ```typescript
   console.log(await page.content());
   ```

4. **Pause and inspect:**
   ```typescript
   await page.pause(); // Pauses execution, opens DevTools
   ```

## Resources

- [Playwright Documentation](https://playwright.dev/docs/intro)
- [Playwright Test API](https://playwright.dev/docs/test-assertions)
- [Locators Guide](https://playwright.dev/docs/locators)
- [Debugging Guide](https://playwright.dev/docs/debug)

---

## Quick Reference

```bash
# Install dependencies
npm install

# Run all tests
npm test

# Run with browser visible
npm run test:headed

# Interactive UI mode
npm run test:ui

# View test report
npm run test:report

# Debug mode (step through)
npm run test:debug
```

---

**Testing is part of learning WordPress!** Use these tests to verify your code works and to understand how WordPress components interact.
