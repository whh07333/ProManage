const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    console.log('1. 登录...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(5000);
    console.log('   登录成功');

    console.log('2. 测试 fetch 请求...');
    const result = await page.evaluate(async () => {
        try {
            const response = await fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            return {
                status: response.status,
                textLength: text.length,
                hasCloseBtn: text.includes('chat-modal-close'),
                textPreview: text.substring(0, 200)
            };
        } catch (e) {
            return { error: e.message };
        }
    });
    console.log('   结果:', JSON.stringify(result, null, 2));

    await browser.close();
})();
