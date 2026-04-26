const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    console.log('1. 登录...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(5000);
    console.log('   登录成功');

    console.log('2. 检查页面上所有的 chatBar 按钮...');
    const chatBars = await page.evaluate(() => {
        const buttons = document.querySelectorAll('button, [class*=btn], [id*=chat]');
        const results = [];
        buttons.forEach(btn => {
            if (btn.id === 'chatBar' || btn.id === 'chatbar' ||
                btn.className.includes('chatBar') || btn.className.includes('chatbar') ||
                btn.innerHTML.includes('chat') || btn.innerText.includes('职聊')) {
                results.push({
                    tag: btn.tagName,
                    id: btn.id,
                    className: btn.className,
                    innerHTML: btn.innerHTML.substring(0, 100),
                    style: btn.style.cssText,
                    visible: btn.offsetParent !== null
                });
            }
        });
        return results;
    });
    console.log('   找到的聊天按钮:', JSON.stringify(chatBars, null, 2));

    console.log('3. 检查页面上所有的 modal 相关元素...');
    const modals = await page.evaluate(() => {
        const modals = document.querySelectorAll('[class*=modal], [id*=modal]');
        return Array.from(modals).map(m => ({
            tag: m.tagName,
            id: m.id,
            className: m.className,
            visible: m.offsetParent !== null || m.style.display !== 'none'
        }));
    });
    console.log('   找到的 modal:', JSON.stringify(modals, null, 2));

    console.log('4. 检查 ZIN header...');
    const headerInfo = await page.evaluate(() => {
        const header = document.querySelector('.header, [class*=header], nav');
        return header ? {
            exists: true,
            innerHTML: header.innerHTML.substring(0, 300)
        } : { exists: false };
    });
    console.log('   header:', JSON.stringify(headerInfo, null, 2));

    await browser.close();
})();
