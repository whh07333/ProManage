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
        const zentaosid = cookies.find(c => c.name === 'zentaosid');
        console.log('   登录成功');

        console.log('2. 测试 product 模块...');
        const resp1 = await page.request.get('http://localhost:8080/index.php?m=product&f=index', {
            headers: { 'Cookie': `zentaosid=${zentaosid.value}` }
        });
        console.log('   product 状态:', resp1.status(), '内容长度:', (await resp1.text()).length);

        console.log('3. 测试 chat 模块...');
        const resp2 = await page.request.get('http://localhost:8080/index.php?m=chat&f=index', {
            headers: { 'Cookie': `zentaosid=${zentaosid.value}` }
        });
        console.log('   chat 状态:', resp2.status(), '内容长度:', (await resp2.text()).length);

        console.log('4. 测试 config 模块...');
        const resp3 = await page.request.get('http://localhost:8080/index.php?m=config&f=index', {
            headers: { 'Cookie': `zentaosid=${zentaosid.value}` }
        });
        console.log('   config 状态:', resp3.status(), '内容长度:', (await resp3.text()).length);

        console.log('5. 测试 ui 模块...');
        const resp4 = await page.request.get('http://localhost:8080/index.php?m=ui&f=index', {
            headers: { 'Cookie': `zentaosid=${zentaosid.value}` }
        });
        console.log('   ui 状态:', resp4.status(), '内容长度:', (await resp4.text()).length);

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
