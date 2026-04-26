const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();

    console.log('1. 打开 ZenTao 登录页面...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });

    console.log('2. 登录中...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(5000);

    console.log('3. 获取 cookies:');
    const cookies = await context.cookies();
    const cookieStr = cookies.map(c => `${c.name}=${c.value}`).join('; ');
    console.log('   Cookie 字符串:', cookieStr);

    console.log('4. 导航到 chat 页面...');
    await page.goto('http://localhost:8080/index.php?m=chat&f=index', { waitUntil: 'networkidle0' });
    await page.waitForTimeout(5000);

    const content = await page.content();
    console.log('   页面长度:', content.length);

    if (content.length < 100) {
        console.log('   页面内容:', content);
    } else {
        console.log('   页面包含 chat-container:', content.includes('chat-container'));
        console.log('   页面包含 main-content:', content.includes('main-content'));
    }

    await browser.close();
    console.log('测试完成');
})();