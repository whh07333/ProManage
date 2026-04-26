const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();
    await page.goto('http://localhost:8080');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(3000);

    const r1 = await page.evaluate(async () => {
        const res = await fetch('/index.php?m=chat&f=getRooms&t=json', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        return { status: res.status, text: text.substring(0, 300) };
    });
    console.log('getRooms:', JSON.stringify(r1));

    const r2 = await page.evaluate(async () => {
        const fd = new FormData();
        fd.append('name', 'TestRoom123');
        fd.append('type', 'private');
        const res = await fetch('/index.php?m=chat&f=createRoom&t=json', {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        return { status: res.status, text: text.substring(0, 300) };
    });
    console.log('createRoom:', JSON.stringify(r2));

    await browser.close();
})();
