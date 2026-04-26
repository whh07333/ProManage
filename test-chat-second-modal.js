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
        await chatBar.click();
        await page.waitForTimeout(5000);

        console.log('3. 检查第二个 modal...');
        const modals = await frame.$$('.modal');
        console.log('   modal 数量:', modals.length);

        if (modals.length >= 2) {
            const modal1 = modals[0];
            const modal2 = modals[1];

            const title1 = await modal1.$eval('.modal-title', el => el.textContent).catch(() => 'no title');
            console.log('   modal 1 title:', title1);

            const modal2HTML = await modal2.evaluate(el => el.outerHTML);
            console.log('   modal 2 HTML 长度:', modal2HTML.length);
            console.log('   modal 2 包含 chat-modal-wrapper:', modal2HTML.includes('chat-modal-wrapper'));
            console.log('   modal 2 包含 room-list:', modal2HTML.includes('room-list'));
            console.log('   modal 2 包含 createRoomBtn:', modal2HTML.includes('createRoomBtn'));
            console.log('   modal 2 包含 iframe:', modal2HTML.includes('iframe'));

            console.log('   modal 2 HTML (前1500字符):');
            console.log(modal2HTML.substring(0, 1500));
        }

        console.log('4. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-modal-new.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
