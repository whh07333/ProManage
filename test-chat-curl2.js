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
        console.log('   zentaosid:', zentaosid.value);

        console.log('2. 用有效 session 测试...');
        const { execSync } = require('child_process');
        const curlCmd = `curl -s -b "zentaosid=${zentaosid.value}" "http://localhost:8080/index.php?m=chat&f=index&onlybody=yes"`;
        const curlResult = execSync(curlCmd, { encoding: 'utf-8' });
        console.log('   curl 结果长度:', curlResult.length);
        console.log('   curl 结果 (前500字符):', curlResult.substring(0, 500));

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
