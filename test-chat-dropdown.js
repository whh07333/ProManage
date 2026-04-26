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
        if (chatBar) {
            console.log('   chatBar 找到');
            const html = await chatBar.evaluate(el => el.outerHTML);
            console.log('   chatBar HTML:', html.substring(0, 300));

            console.log('   点击 chatBar...');
            await chatBar.click();
            await page.waitForTimeout(3000);

            console.log('3. 检查 dropdown 是否打开...');
            const dropdown = await frame.$('.dropdown-menu.show');
            console.log('   dropdown show:', dropdown !== null);

            const dropdownChatMenu = await frame.$('#dropdownChatMenu');
            console.log('   dropdownChatMenu:', dropdownChatMenu !== null);

            const chatDropdownFrame = await frame.$('#chatDropdownFrame');
            console.log('   chatDropdownFrame:', chatDropdownFrame !== null);

            if (dropdownChatMenu) {
                const menuHTML = await dropdownChatMenu.evaluate(el => el.innerHTML);
                console.log('   dropdown 内容 (前500字符):', menuHTML.substring(0, 500));
            }

            const allDropdowns = await frame.$$('[class*="dropdown"]');
            console.log('   所有 dropdown 元素数量:', allDropdowns.length);
        }

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
