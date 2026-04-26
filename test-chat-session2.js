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

    console.log('3. 登录后的 cookies:');
    const cookies = await context.cookies();
    for (const cookie of cookies) {
        console.log(`   ${cookie.name}: ${cookie.value.substring(0, 30)}...`);
    }

    console.log('4. 在当前标签页打开 chat...');
    await page.goto('http://localhost:8080/index.php?m=chat&f=index', { waitUntil: 'networkidle0' });
    await page.waitForTimeout(3000);

    const content = await page.content();
    console.log('   页面长度:', content.length);
    console.log('   页面包含 chat-container:', content.includes('chat-container'));
    console.log('   页面包含 main-content:', content.includes('main-content'));
    console.log('   页面包含 sidebar:', content.includes('sidebar'));

    const body = await page.$eval('body', el => el.innerHTML);
    console.log('   Body HTML (前500字符):', body.substring(0, 500));

    await browser.close();
    console.log('测试完成');
})();