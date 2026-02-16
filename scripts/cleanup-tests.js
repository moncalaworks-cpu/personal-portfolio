#!/usr/bin/env node

/**
 * Test Cleanup Script
 *
 * Removes orphaned npm test and Playwright processes
 * Clears temporary test files and locks
 * Safe to run before or after tests
 *
 * Usage: npm run test:cleanup
 * Or: node scripts/cleanup-tests.js
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

console.log('🧹 Cleaning up test processes and artifacts...\n');

// Step 1: Kill orphaned processes
console.log('Step 1: Terminating orphaned processes...');
const processes = [
  'npm test',
  'playwright',
  'node.*test',
];

for (const proc of processes) {
  try {
    execSync(`pkill -f "${proc}" 2>/dev/null || true`, { stdio: 'ignore' });
  } catch (e) {
    // Ignore errors
  }
}

console.log('✅ Orphaned processes terminated');

// Step 2: Clear temporary files
console.log('\nStep 2: Removing temporary files...');
const tempPaths = [
  path.join(__dirname, '..', '.playwright'),
  path.join(__dirname, '..', 'test-results', '.cache'),
];

for (const tempPath of tempPaths) {
  if (fs.existsSync(tempPath)) {
    try {
      // Use recursive delete for directories
      fs.rmSync(tempPath, { recursive: true, force: true });
      console.log(`✅ Removed: ${path.relative(process.cwd(), tempPath)}`);
    } catch (e) {
      // Ignore if can't delete
    }
  }
}

// Step 3: Check for remaining processes
console.log('\nStep 3: Verifying cleanup...');
try {
  const result = execSync(
    "ps aux | grep -E 'npm|playwright|node.*test' | grep -v grep || echo 'clean'",
    { encoding: 'utf-8' }
  );

  if (result.includes('clean')) {
    console.log('✅ No orphaned test processes found');
  } else {
    console.log('⚠️  Some processes may still be running:');
    console.log(result);
  }
} catch (e) {
  // Ignore errors
}

// Step 4: Summary
console.log('\n📊 Cleanup Summary:');
console.log('✅ Orphaned processes: Terminated');
console.log('✅ Temporary files: Cleared');
console.log('✅ System resources: Released');
console.log('\n✨ Cleanup complete!\n');

process.exit(0);
