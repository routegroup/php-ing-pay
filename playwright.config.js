// @ts-check
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
    testDir: './tests/Browser',
    timeout: 120_000,
    retries: 0,
    forbidOnly: true,
    reporter: [['list']],
    use: {
        baseURL: `http://127.0.0.1:${process.env.SANDBOX_PORT ?? 8000}`,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
