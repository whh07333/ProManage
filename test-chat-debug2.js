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
            const html = await chatBar.evaluate(el => el.outerHTML);
            console.log('   chatBar HTML:', html);
        } else {
            console.log('   chatBar NOT FOUND');
        }

        console.log('3. 点击 chatBar...');
        await chatBar.click();
        await page.waitForTimeout(3000);

        console.log('4. 检查页面结构...');
        const bodyHTML = await page.content();
        console.log('   页面包含 .modal:', bodyHTML.includes('class="modal'));
        console.log('   页面包含 data-toggle:', bodyHTML.includes('data-toggle="modal"'));

        const modal = await page.$('.modal');
        console.log('   modal 元素数量:', modal ? 'found' : 'not found');

        const allModals = await page.$$('[class*="modal"]');
        console.log('   所有 modal 类元素数量:', allModals.length);

        console.log('5. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-debug-screenshot.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
