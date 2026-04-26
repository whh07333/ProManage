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

        console.log('3. 检查 modal 是否打开...');
        const modal = await page.$('.modal.show, .modal.in');
        console.log('   modal 打开:', modal !== null);

        if (modal) {
            const modalContent = await modal.evaluate(el => el.innerHTML);
            console.log('   包含 chat-modal-wrapper:', modalContent.includes('chat-modal-wrapper'));
            console.log('   包含 chat-modal-sidebar:', modalContent.includes('chat-modal-sidebar'));
            console.log('   包含 chat-modal-main:', modalContent.includes('chat-modal-main'));
            console.log('   包含 room-list:', modalContent.includes('room-list'));
            console.log('   包含 createRoomBtn:', modalContent.includes('createRoomBtn'));
            console.log('   包含聊天室 tab:', modalContent.includes('聊天室'));
            console.log('   包含联系人 tab:', modalContent.includes('联系人'));
        }

        console.log('4. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-modal-screenshot.png', fullPage: false });
        console.log('   截图已保存');

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
