const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);

    console.log('Testing getMessages with POST...');

    const result = await page.evaluate(async () => {
        const fd = new FormData();
        fd.append('roomID', '5');
        const response = await fetch('/index.php?m=chat&f=getMessages&t=json', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await response.text();
        return { status: response.status, text };
    });

    console.log('Result:', result);

    await browser.close();
})();
