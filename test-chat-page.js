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

        const toolbarMatch = html.match(/<nav[^>]*toolbar[^>]*>[\s\S]{0,3000}/i);
        if (toolbarMatch) {
            console.log('   Toolbar HTML (前1000字符):', toolbarMatch[0].substring(0, 1000));
        } else {
            console.log('   没有找到 toolbar');
            const bodyMatch = html.match(/<body[^>]*>[\s\S]{0,5000}/i);
            if (bodyMatch) {
                console.log('   Body 前1000字符:', bodyMatch[0].substring(0, 1000));
            }
        }

        const scriptMatch = html.match(/<script[^>]*>[\s\S]{0,500}/i);
        if (scriptMatch) {
            console.log('   第一个 script 标签:', scriptMatch[0].substring(0, 200));
        }

        await page.waitForTimeout(2000);

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
