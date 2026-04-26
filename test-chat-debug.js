const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    const errors = [];
    page.on('console', msg => {
        if (msg.type() === 'error') errors.push(msg.text());
    });
    page.on('pageerror', err => errors.push(err.message));

    try {
        console.log('1. 打开首页并登录...');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0', timeout: 15000 });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);
        console.log('   登录完成');

        console.log('2. 获取 iframe 并查找 chatBar...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');
        if (!chatBar) {
            console.log('   ERROR: chatBar NOT FOUND');
            return;
        }
        console.log('   chatBar 找到');

        console.log('3. 直接测试 chat 模块 URL...');
        const chatURL = 'http://localhost:8080/index.php?m=chat&f=index&onlybody=yes';
        const chatPage = await browser.newPage();
        const chatErrors = [];
        chatPage.on('console', msg => {
            if (msg.type() === 'error') chatErrors.push(msg.text());
        });

        await chatPage.goto(chatURL, { waitUntil: 'networkidle0', timeout: 15000 });
        await chatPage.waitForTimeout(2000);

        const chatContent = await chatPage.content();
        console.log('   chat 模块包含 chat-modal-container:', chatContent.includes('chat-modal-container'));
        console.log('   chat 模块页面长度:', chatContent.length);
        if (chatErrors.length > 0) {
            console.log('   chat 模块错误:', chatErrors);
        }
        await chatPage.close();

        console.log('4. 点击 chatBar 并监听网络请求...');
        const [popup] = await Promise.all([
            page.waitForEvent('popup', { timeout: 5000 }).catch(() => null),
            chatBar.click()
        ]);
        await page.waitForTimeout(3000);

        if (popup) {
            console.log('   弹出了 popup!');
            console.log('   popup URL:', popup.url());
            const popupContent = await popup.content();
            console.log('   popup 包含 chat-modal:', popupContent.includes('chat-modal'));
        } else {
            console.log('   没有 popup');
            const allPages = browser.contexts()[0].pages();
            console.log('   当前所有页面数量:', allPages.length);
            for (let i = 0; i < allPages.length; i++) {
                console.log(`   页面 ${i}: ${allPages[i].url()}`);
            }
        }

        if (errors.length > 0) {
            console.log('\n   JavaScript 错误:');
            errors.forEach(e => console.log('   - ' + e));
        }

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
