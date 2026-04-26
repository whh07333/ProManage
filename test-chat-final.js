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
        console.log('   登录成功');

        console.log('2. 获取 iframe 并点击 chatBar...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        console.log('   chatBar 找到');

        await chatBar.click();
        await page.waitForTimeout(5000);

        console.log('3. 检查 dropdown 是否打开...');
        const dropdown = await frame.$('.dropdown-menu.show');
        console.log('   dropdown show:', dropdown !== null);

        console.log('4. 获取 iframe 内容...');
        const chatFrame = await frame.$('#chatDropdownFrame');
        if (chatFrame) {
            console.log('   chatDropdownFrame 找到');
            const frameEl = await chatFrame.contentFrame();
            if (frameEl) {
                const frameContent = await frameEl.content();
                console.log('   iframe 内容长度:', frameContent.length);
                console.log('   iframe 包含 chat-modal-container:', frameContent.includes('chat-modal-container'));
                console.log('   iframe 包含 room-list:', frameContent.includes('room-list'));
                console.log('   iframe 包含 chat-container:', frameContent.includes('chat-container'));
                console.log('   iframe 内容前800字符:');
                console.log(frameContent.substring(0, 800));
            } else {
                console.log('   无法获取 iframe contentFrame');
            }
        }

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
