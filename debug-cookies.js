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

        const cookies = await context.cookies('http://localhost:8080');
        console.log('登录后 Cookies:', cookies.map(c => `${c.name}=${c.value.substring(0, 20)}...`).join('\n'));

        console.log('\n========== 测试 getRooms ==========\n');
        const result = await page.evaluate(async () => {
            const response = await fetch('/index.php?m=chat&f=getRooms&t=json', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            return { status: response.status, text: text.substring(0, 500) };
        });
        console.log('getRooms:', JSON.stringify(result));

        console.log('\n========== 测试带 cookie 的请求 ==========\n');
        const result2 = await page.evaluate(async (cookies) => {
            const response = await fetch('/index.php?m=chat&f=getRooms&t=json', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cookie': cookies.map(c => `${c.name}=${c.value}`).join('; ')
                }
            });
            const text = await response.text();
            return { status: response.status, text: text.substring(0, 500) };
        }, cookies);
        console.log('getRooms (带cookie):', JSON.stringify(result2));

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
