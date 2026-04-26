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

    console.log('2. 当前 URL:', page.url());

    console.log('3. 检查 body 是否有内容...');
    const bodyContent = await page.evaluate(() => ({
        bodyLength: document.body.innerHTML.length,
        bodyText: document.body.innerText.substring(0, 500)
    }));
    console.log('   body 长度:', bodyContent.bodyLength);
    console.log('   body 文本:', bodyContent.bodyText);

    console.log('4. 检查 HTML title...');
    const title = await page.title();
    console.log('   页面标题:', title);

    console.log('5. 检查是否有 iframe...');
    const iframes = await page.evaluate(() => {
        const iframes = document.querySelectorAll('iframe');
        return Array.from(iframes).map(f => ({ id: f.id, src: f.src }));
    });
    console.log('   iframes:', JSON.stringify(iframes));

    console.log('6. 截图...');
    await page.screenshot({ path: '/Users/whh073/zentaopms/page_screenshot.png', fullPage: true });
    console.log('   截图已保存到 page_screenshot.png');

    await browser.close();
})();
