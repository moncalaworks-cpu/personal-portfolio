import { defineConfig, devices } from '@playwright/test';
import * as dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
  testDir: './tests',
  fullyParallel: false, // Disable parallel execution for stability with shared auth
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 1, // Allow 1 retry locally
  workers: 1, // Use single worker to prevent auth state conflicts
  reporter: 'html',
  timeout: 30000, // 30 second timeout per test

  use: {
    baseURL: process.env.WORDPRESS_URL || 'http://personal-portfolio.local',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    storageState: 'tests/auth.json',
    navigationTimeout: 10000, // 10 second page load timeout
    actionTimeout: 5000, // 5 second action timeout
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  globalSetup: require.resolve('./tests/global-setup.ts'),
});
