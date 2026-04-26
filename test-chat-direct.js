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

        const cookies = await page.context().cookies();
        const zentaosid = cookies.find(c => c.name === 'zentaosid');

        console.log('2. 直接访问 chat URL...');
        const response = await page.request.get('http://localhost:8080/index.php?m=chat&f=index&onlybody=yes', {
            headers: {
                'Cookie': `zentaosid=${zentaosid.value}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const content = await response.text();
        console.log('   状态:', response.status());
        console.log('   内容长度:', content.length);
        console.log('   包含 chat-modal-wrapper:', content.includes('chat-modal-wrapper'));
        console.log('   包含 room-list:', content.includes('room-list'));
        console.log('   内容前1000字符:');
        console.log(content.substring(0, 1000));

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
