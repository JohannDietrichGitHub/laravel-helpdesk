import { expect, test } from '@playwright/test';

test('redirects guests to login before showing the dashboard', async ({
    page,
}) => {
    await page.goto('/dashboard');

    await expect(page).toHaveURL(/\/login$/);
    await expect(
        page.getByRole('heading', { name: 'Log in to your account' }),
    ).toBeVisible();
});

test('allows the seeded admin to sign in and view tickets', async ({
    page,
}) => {
    await page.goto('/login');
    await page.getByLabel('Email address').fill('admin@example.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('password');
    await page.locator('[data-test="login-button"]').click();

    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(
        page.getByRole('heading', { name: 'Operations overview' }),
    ).toBeVisible();
    await page.getByRole('link', { name: 'View all' }).click();
    await expect(page).toHaveURL(/\/tickets$/);
    await expect(page.getByRole('heading', { name: 'Tickets' })).toBeVisible();
});
