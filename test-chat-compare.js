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

        console.log('2. 获取 iframe 并测试 my profile modal...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const profileLink = await frame.$('a[href*="my"][href*="profile"]');
        if (profileLink) {
            const href = await profileLink.getAttribute('href');
            console.log('   profile link href:', href);
            await profileLink.click();
            await page.waitForTimeout(3000);
            const modal = await page.$('.modal.show, .modal.in, [class*="modal-dialog"]');
            console.log('   modal 打开:', modal !== null);
        }

        console.log('3. 获取 iframe 并点击 chatBar...');
        await page.goto('http://localhost:8080/index.php?m=my&f=index', { waitUntil: 'networkidle0', timeout: 15000 });
        await page.waitForTimeout(2000);

        const iframe2 = await page.$('#appIframe-my');
        const frame2 = await iframe2.contentFrame();
        const chatBar = await frame2.$('#chatBar');
        if (chatBar) {
            console.log('   chatBar 找到');
            const html = await chatBar.evaluate(el => el.outerHTML);
            console.log('   chatBar HTML:', html.substring(0, 300));
            await chatBar.click();
            await page.waitForTimeout(3000);

            const modal = await page.$('.modal.show, .modal.in, [class*="modal-dialog"]');
            console.log('   模态框打开:', modal !== null);
            if (modal) {
                const modalHTML = await modal.evaluate(el => el.outerHTML);
                console.log('   模态框包含 chat-modal:', modalHTML.includes('chat-modal'));
            }
        }

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
