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
        console.log('   chatBar 找到');

        await chatBar.click();
        await page.waitForTimeout(3000);

        console.log('3. 检查 iframe src URL...');
        const chatFrame = await frame.$('#chatDropdownFrame');
        if (chatFrame) {
            const src = await chatFrame.getAttribute('src');
            console.log('   iframe src:', src);
        }

        console.log('4. 直接测试这个 URL...');
        const cookies = await page.context().cookies();
        const zentaosid = cookies.find(c => c.name === 'zentaosid');
        console.log('   zentaosid:', zentaosid ? zentaosid.value : 'not found');

        if (zentaosid) {
            const response = await page.request.get('http://localhost:8080/index.php?m=chat&f=index&onlybody=yes', {
                headers: {
                    'Cookie': `zentaosid=${zentaosid.value}`
                }
            });
            const text = await response.text();
            console.log('   直接请求状态:', response.status());
            console.log('   直接请求内容长度:', text.length);
            console.log('   直接请求包含 chat-modal:', text.includes('chat-modal'));
        }

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
