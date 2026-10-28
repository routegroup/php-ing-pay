// @ts-check
const { test, expect } = require('@playwright/test');

/** Published sandbox test card, no 3DS. */
const CARD = {
    number: '4111111111111111',
    month: '12',
    year: '2029',
    cvv: '123',
    firstName: 'Anna',
    lastName: 'Kowalska',
};

test('registers a card profile through the widget', async ({ page }) => {
    await page.goto('/widget');

    const widget = page.frameLocator('#imoje-widget__iframe');
    const cardNumber = widget.getByLabel('Numer karty');

    await expect(cardNumber, 'the widget did not render its card form — the gateway may have changed it').toBeVisible({ timeout: 30_000 });

    await cardNumber.fill(CARD.number);

    // The expiry selects carry no accessible name; position is all there is.
    const selects = widget.locator('select');

    await expect(selects, 'the expiry fields are not the two selects this expects').toHaveCount(2);

    await selects.nth(0).selectOption({ label: CARD.month });
    await selects.nth(1).selectOption({ label: CARD.year });

    await widget.getByLabel('CVV/CVC').fill(CARD.cvv);
    await widget.getByLabel('Imię (posiadacza karty)').fill(CARD.firstName);
    await widget.getByLabel('Nazwisko (posiadacza karty)').fill(CARD.lastName);

    await widget.getByRole('button', { name: 'Zapłać' }).click();

    await Promise.race([
        page.waitForURL(/r=success/, { timeout: 90_000 }),
        widget.getByText('Płatność została przetworzona pomyślnie').waitFor({ state: 'visible', timeout: 90_000 }),
    ]);
});
