import { defineConfig, devices } from '@playwright/test';
import * as dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
  testDir: './tests',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : 4,
  reporter: 'html',
  
  use: {
    baseURL: process.env.WORDPRESS_URL || 'http://personal-portfolio.local',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    storageState: 'tests/auth.json',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  globalSetup: require.resolve('./tests/global-setup.ts'),
});
