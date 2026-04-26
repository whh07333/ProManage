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

        const cookies = await page.context().cookies();
        console.log('   登录成功');

        console.log('2. 在同一页面导航到 chat 模块...');
        await page.goto('http://localhost:8080/index.php?m=chat&f=index&onlybody=yes', {
            waitUntil: 'networkidle0',
            timeout: 15000
        });
        await page.waitForTimeout(2000);

        const content = await page.content();
        console.log('   页面长度:', content.length);
        console.log('   包含 chat-modal-container:', content.includes('chat-modal-container'));
        console.log('   包含 room-list:', content.includes('room-list'));

        const bodyText = await page.$eval('body', el => el.innerText);
        console.log('   body 文本:', bodyText.substring(0, 200));

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
