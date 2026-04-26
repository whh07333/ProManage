const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    console.log('1. 打开 ZenTao 登录页面...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });

    console.log('2. 登录中...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');

    console.log('3. 等待 SPA 初始化...');
    await page.waitForTimeout(8000);

    console.log('4. 获取 iframe 并点击 chatBar...');
    const iframe = await page.$('#appIframe-my');
    const frame = await iframe.contentFrame();

    const chatBar = await frame.$('#chatBar');
    if (chatBar) {
        console.log('   chatBar 按钮已找到');
        const html = await chatBar.evaluate(el => el.outerHTML);
        console.log('   HTML:', html.substring(0, 300));

        const pagesBeforeClick = browser.contexts()[0].pages().length;
        console.log('   点击前标签页数量:', pagesBeforeClick);

        await chatBar.click();
        await page.waitForTimeout(3000);

        const pagesAfterClick = browser.contexts()[0].pages().length;
        console.log('   点击后标签页数量:', pagesAfterClick);

        if (pagesAfterClick > pagesBeforeClick) {
            const newPage = browser.contexts()[0].pages()[pagesAfterClick - 1];
            console.log('   新标签页 URL:', newPage.url());

            await newPage.waitForLoadState('networkidle');
            const newPageContent = await newPage.content();
            console.log('   新页面包含 chat-container:', newPageContent.includes('chat-container'));
            console.log('   新页面包含 消息:', newPageContent.includes('消息'));

            const bodyText = await newPage.$eval('body', el => el.innerText);
            console.log('   新页面内容 (前500字符):', bodyText.substring(0, 500));
        }
    } else {
        console.log('   chatBar NOT FOUND');
    }

    await browser.close();
    console.log('测试完成');
})();