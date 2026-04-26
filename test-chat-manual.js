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

        console.log('2. 获取 iframe 并检查 chatBar data 属性...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        if (chatBar) {
            const dataUrl = await chatBar.getAttribute('data-url');
            const dataToggle = await chatBar.getAttribute('data-toggle');
            const dataSize = await chatBar.getAttribute('data-size');
            const dataTitle = await chatBar.getAttribute('data-title');
            console.log('   data-url:', dataUrl);
            console.log('   data-toggle:', dataToggle);
            console.log('   data-size:', dataSize);
            console.log('   data-title:', dataTitle);
        }

        console.log('3. 手动触发 modal...');
        await page.evaluate(() => {
            const chatBar = document.querySelector('#chatBar');
            if (chatBar) {
                console.log('Found chatBar:', chatBar);
                const event = new MouseEvent('click', { bubbles: true });
                chatBar.dispatchEvent(event);
            }
        });

        await page.waitForTimeout(5000);

        console.log('4. 检查所有 modal...');
        const allModals = await frame.$$('.modal');
        console.log('   iframe 中 modal 数量:', allModals.length);

        for (let i = 0; i < allModals.length; i++) {
            const modal = allModals[i];
            const id = await modal.getAttribute('id');
            const title = await modal.$eval('.modal-title', el => el.textContent).catch(() => 'no title');
            console.log(`   modal ${i}: id=${id}, title=${title}`);
        }

        console.log('5. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-modal-manual.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
