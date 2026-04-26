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

        console.log('2. 查找 chatBar...');
        const chatBar = await page.$('#chatBar');
        if (chatBar) {
            console.log('   找到 #chatBar');
        } else {
            console.log('   没有找到 #chatBar，搜索页面上的聊天按钮...');
            const allButtons = await page.$$('button');
            console.log('   页面上的按钮数量:', allButtons.length);

            const icons = await page.$$('[class*="icon-chat"]');
            console.log('   聊天图标数量:', icons.length);

            const pageContent = await page.content();
            const hasChatBar = pageContent.includes('chatBar');
            console.log('   页面包含 chatBar:', hasChatBar);

            if (!hasChatBar) {
                console.log('   检查 page source 中的 zin header...');
                const headerMatch = pageContent.match(/<header[^>]*>[\s\S]{0,2000}/i);
                if (headerMatch) {
                    console.log('   Header 内容前500字符:', headerMatch[0].substring(0, 500));
                }
            }
        }

        await page.waitForTimeout(2000);

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
