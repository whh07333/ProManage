const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(3000);

    console.log('1. 测试不同页面是否有 chatBar...');

    const pages = [
        'http://localhost:8080/index.php?m=index&f=index',
        'http://localhost:8080/index.php?m=product&f=index',
        'http://localhost:8080/index.php?m=project&f=index',
    ];

    for (const url of pages) {
        await page.goto(url, { waitUntil: 'networkidle0' });
        await page.waitForTimeout(2000);
        const html = await page.content();
        console.log(`   ${url}: chatBar=${html.includes('chatBar')}, header=${html.includes('id="header"')}`);
    }

    await browser.close();
})();
