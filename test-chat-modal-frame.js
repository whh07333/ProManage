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
        await page.waitForTimeout(5000);

        console.log('3. 检查 iframe 中的 modal...');
        const modalInFrame = await frame.$('.modal');
        console.log('   iframe 中 modal:', modalInFrame !== null);

        if (modalInFrame) {
            const modalHTML = await modalInFrame.evaluate(el => el.outerHTML);
            console.log('   modal HTML 长度:', modalHTML.length);
            console.log('   包含 chat-modal-wrapper:', modalHTML.includes('chat-modal-wrapper'));
            console.log('   包含 room-list:', modalHTML.includes('room-list'));
            console.log('   包含 createRoomBtn:', modalHTML.includes('createRoomBtn'));
        }

        console.log('4. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-modal-test.png', fullPage: false });
        console.log('   截图已保存到 chat-modal-test.png');

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
