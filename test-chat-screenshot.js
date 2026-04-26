const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('1. 登录...');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0', timeout: 15000 });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);
        console.log('   登录成功');

        console.log('2. 获取 iframe 并点击 chatBar...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        console.log('   chatBar 找到');

        await chatBar.click();
        await page.waitForTimeout(3000);

        console.log('3. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-dropdown-screenshot.png', fullPage: false });
        console.log('   截图已保存到 /Users/whh073/zentaopms/chat-dropdown-screenshot.png');

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
