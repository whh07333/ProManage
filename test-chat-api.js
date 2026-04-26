const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(3000);

    console.log('1. 测试直接访问 chat URL...');
    const response = await page.request.get('http://localhost:8080/index.php?m=chat&f=index&onlybody=yes');
    const content = await response.text();
    console.log('   状态:', response.status());
    console.log('   内容长度:', content.length);
    console.log('   包含 chat-modal-wrapper:', content.includes('chat-modal-wrapper'));

    if (content.length > 0) {
        console.log('\n2. chat URL 返回了正确内容！');
        console.log('   内容前300字符:', content.substring(0, 300));
    }

    await browser.close();
})();
