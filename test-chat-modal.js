const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    try {
        console.log('1. 打开首页...');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0', timeout: 15000 });
        console.log('   页面加载完成');

        console.log('2. 登录...');
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);
        console.log('   登录完成');

        console.log('3. 获取 iframe 并查找 chatBar...');
        const iframe = await page.$('#appIframe-my');
        if (!iframe) {
            console.log('   ERROR: iframe#appIframe-my NOT FOUND');
            return;
        }
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        if (!chatBar) {
            console.log('   ERROR: chatBar NOT FOUND in iframe');
            return;
        }
        console.log('   chatBar 按钮已找到');

        const chatBarHTML = await chatBar.evaluate(el => el.outerHTML);
        console.log('   chatBar HTML:', chatBarHTML.substring(0, 200));

        console.log('4. 点击 chatBar...');
        await chatBar.click();
        await page.waitForTimeout(3000);

        console.log('5. 检查是否弹出模态框...');
        const modal = await page.$('.modal.show, .modal.in, [class*="modal"]:visible');
        if (modal) {
            const modalHTML = await modal.evaluate(el => el.outerHTML);
            console.log('   模态框已打开!');
            console.log('   模态框包含 chat-modal-container:', modalHTML.includes('chat-modal-container'));
            console.log('   模态框包含 room-list:', modalHTML.includes('room-list'));
            console.log('   模态框内容 (前500字符):', modalHTML.substring(0, 500));
        } else {
            console.log('   模态框 NOT FOUND');
            const bodyHTML = await page.content();
            const bodyText = await page.$eval('body', el => el.innerText);
            console.log('   页面文本 (前300字符):', bodyText.substring(0, 300));
        }

        console.log('\n测试完成');

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
