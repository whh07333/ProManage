const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    try {
        console.log('1. 登录...');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0', timeout: 15000 });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);

        const cookies = await page.context().cookies();
        console.log('   登录成功, 获取 cookie');

        console.log('2. 使用 cookie 访问 chat 模块...');
        const chatPage = await browser.newPage();
        await chatPage.goto('http://localhost:8080/index.php?m=chat&f=index&onlybody=yes', {
            waitUntil: 'networkidle0',
            timeout: 15000
        });
        await chatPage.waitForTimeout(2000);

        const content = await chatPage.content();
        console.log('   页面长度:', content.length);
        console.log('   包含 chat-modal-container:', content.includes('chat-modal-container'));
        console.log('   页面内容前500字符:');
        console.log(content.substring(0, 500));

        await chatPage.close();
    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
