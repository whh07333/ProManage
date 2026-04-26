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

        console.log('2. 获取 iframe 并检查 chatBar...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        if (chatBar) {
            const dataUrl = await chatBar.getAttribute('data-url');
            const dataType = await chatBar.getAttribute('data-type');
            const dataToggle = await chatBar.getAttribute('data-toggle');
            console.log('   data-url:', dataUrl);
            console.log('   data-type:', dataType);
            console.log('   data-toggle:', dataToggle);
        }

        console.log('3. 在主页面检查...');
        const mainChatBar = await page.$('#appIframe-my');
        const mainContent = await mainChatBar.contentFrame();
        const mainBar = await mainContent.$('#chatBar');
        if (mainBar) {
            const dataUrl = await mainBar.getAttribute('data-url');
            const dataType = await mainBar.getAttribute('data-type');
            console.log('   主页面 data-url:', dataUrl);
            console.log('   主页面 data-type:', dataType);
        }

        console.log('4. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-before-click.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
