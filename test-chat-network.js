const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    const requests = [];
    page.on('request', req => {
        if (req.url().includes('chat') || req.url().includes('header')) {
            requests.push({url: req.url(), method: req.method()});
        }
    });

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(5000);

    console.log('1. Chat/Header 相关请求:');
    requests.forEach(r => console.log('   ', r.method, r.url));

    console.log('\n2. 页面 body class:', await page.$eval('body', el => el.className));
    console.log('3. #header 存在:', !!(await page.$('#header')));
    console.log('4. #heading 存在:', !!(await page.$('#heading')));
    console.log('5. #toolbar 存在:', !!(await page.$('#toolbar')));

    await browser.close();
})();
