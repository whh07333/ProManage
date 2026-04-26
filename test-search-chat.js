const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    console.log('1. 登录...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(5000);
    console.log('   登录成功');

    console.log('2. 获取页面完整源码并搜索 chat...');
    const html = await page.content();

    const lines = html.split('\n');
    const chatLines = [];
    for (let i = 0; i < lines.length; i++) {
        if (lines[i].toLowerCase().includes('chat')) {
            chatLines.push({ line: i + 1, content: lines[i].substring(0, 200) });
        }
    }
    console.log('   找到包含 chat 的行数:', chatLines.length);
    chatLines.forEach(l => console.log(`   行 ${l.line}:`, l.content));

    await browser.close();
})();
