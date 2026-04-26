const { chromium } = require('playwright');

async function runTests() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('\n========== 登录 ==========\n');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        console.log('\n========== 测试 1: URLSearchParams POST ==========\n');
        const r1 = await page.evaluate(async () => {
            const params = new URLSearchParams();
            params.append('name', 'TestRoom');
            params.append('type', 'private');
            const response = await fetch('/index.php?m=chat&f=createRoom&t=json', {
                method: 'POST',
                body: params,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });
            return { status: response.status, text: await response.text() };
        });
        console.log('Result:', JSON.stringify(r1));

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
