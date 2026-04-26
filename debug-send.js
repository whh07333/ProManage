const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);

    console.log('Testing sendMessage directly...');

    const result = await page.evaluate(async () => {
        const fd = new FormData();
        fd.append('roomID', '4');
        fd.append('content', 'Test message');
        fd.append('type', 'text');
        const response = await fetch('/index.php?m=chat&f=sendMessage&t=json', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        return {
            status: response.status,
            text: await response.text()
        };
    });

    console.log('Status:', result.status);
    console.log('Response:', result.text.substring(0, 500));

    await browser.close();
})();
