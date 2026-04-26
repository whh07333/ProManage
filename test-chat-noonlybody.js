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

        const cookies = await page.context().cookies();
        const zentaosid = cookies.find(c => c.name === 'zentaosid');

        console.log('2. 测试不带 onlybody 参数...');
        const response = await page.request.get('http://localhost:8080/index.php?m=chat&f=index', {
            headers: {
                'Cookie': `zentaosid=${zentaosid.value}`
            }
        });
        const text = await response.text();
        console.log('   状态:', response.status());
        console.log('   内容长度:', text.length);
        console.log('   内容前500字符:');
        console.log(text.substring(0, 500));

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
