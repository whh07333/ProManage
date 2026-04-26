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

        console.log('2. 检查页面内容...');
        const html = await page.content();

        const headerMatch = html.match(/<header[^>]*>[\s\S]{0,5000}/i);
        if (headerMatch) {
            console.log('   Header HTML (前2000字符):', headerMatch[0].substring(0, 2000));
        }

        console.log('\n3. 检查页面上的所有元素...');
        const elements = await page.$$('header, nav, div[class*="header"], div[class*="toolbar"]');
        console.log('   找到 header/nav/toolbar 元素数量:', elements.length);

        const btnCount = await page.$$eval('button', btns => btns.length);
        console.log('   按钮数量:', btnCount);

        const btnHtml = await page.$$eval('button', btns => btns.map(b => b.outerHTML).join('\n').substring(0, 1000));
        console.log('   按钮 HTML:', btnHtml);

        await page.waitForTimeout(2000);

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
