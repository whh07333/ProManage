const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    const consoleMessages = [];
    page.on('console', msg => {
        if (msg.type() === 'error') {
            consoleMessages.push(`ERROR: ${msg.text()}`);
        }
    });

    page.on('pageerror', error => {
        consoleMessages.push(`PAGE ERROR: ${error.message}`);
    });

    console.log('1. 打开 ZenTao 登录页面...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });

    console.log('2. 登录中...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');

    console.log('3. 等待 10 秒让 SPA 初始化...');
    await page.waitForTimeout(10000);

    console.log('4. 检查页面标题:', await page.title());
    console.log('5. 检查 body 长度:', (await page.content()).length);

    console.log('6. 检查 DOM 中的主要元素:');
    const elements = ['#header', '#toolbar', '#pageToolbar', '#messageBar', '#chatBar', '#test-btn', '.btn', '.dropdown'];
    for (const sel of elements) {
        const count = await page.$$eval(sel, els => els.length);
        console.log(`   ${sel}: ${count} 个`);
    }

    console.log('7. 检查是否在 iframe 中:');
    const iframes = await page.$$('iframe');
    console.log(`   找到 ${iframes.length} 个 iframe`);
    for (const iframe of iframes) {
        const src = await iframe.evaluate(el => el.src || el.getAttribute('src'));
        const id = await iframe.evaluate(el => el.id);
        console.log(`   iframe: id=${id}, src=${src}`);
    }

    console.log('8. Console 错误:');
    if (consoleMessages.length === 0) {
        console.log('   没有错误');
    } else {
        for (const msg of consoleMessages.slice(0, 10)) {
            console.log(`   ${msg.substring(0, 200)}`);
        }
    }

    await browser.close();
    console.log('诊断完成');
})();