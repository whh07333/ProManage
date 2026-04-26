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

    console.log('4. 获取 iframe...');
    const iframe = await page.$('#appIframe-my');
    const frame = await iframe.contentFrame();

    console.log('5. 查找并点击 chatBar...');
    const chatBar = await frame.$('#chatBar');
    if (chatBar) {
        console.log('   chatBar 按钮已找到');
        const html = await chatBar.evaluate(el => el.outerHTML);
        console.log('   HTML:', html);

        console.log('   点击 chatBar...');
        await chatBar.click();
        await page.waitForTimeout(3000);

        console.log('6. 检查是否有 modal/dropdown 打开:');
        const dropdown = await frame.$('.dropdown.show');
        const dropdownMenu = await frame.$('.dropdown-menu.show');
        const dropdownMenu2 = await frame.$('[class*="dropdown-menu"]');

        console.log('   .dropdown.show:', dropdown ? '存在' : '不存在');
        console.log('   .dropdown-menu.show:', dropdownMenu ? '存在' : '不存在');
        console.log('   任何 dropdown-menu:', dropdownMenu2 ? '存在' : '不存在');

        if (dropdownMenu2) {
            const menuHTML = await dropdownMenu2.evaluate(el => el.outerHTML);
            console.log('   dropdown-menu HTML:', menuHTML.substring(0, 500));
        }

        console.log('7. 检查是否有 modal 打开:');
        const modal = await frame.$('.modal.show');
        const modalDialog = await frame.$('.modal-dialog');
        console.log('   .modal.show:', modal ? '存在' : '不存在');
        console.log('   .modal-dialog:', modalDialog ? '存在' : '不存在');

        if (modal) {
            const modalContent = await modal.evaluate(el => el.innerHTML.substring(0, 1000));
            console.log('   modal 内容:', modalContent);
        }

    } else {
        console.log('   chatBar NOT FOUND');
    }

    await browser.close();
    console.log('测试完成');
})();