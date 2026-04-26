const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    console.log('1. 直接访问 chat 模块页面...');
    const response = await page.goto('http://localhost:8080/index.php?m=chat&f=index', { waitUntil: 'networkidle0' });
    console.log('   状态码:', response.status());

    await page.waitForTimeout(3000);

    const content = await page.content();
    console.log('2. 检查页面内容:');
    console.log('   包含 chat-container:', content.includes('chat-container'));
    console.log('   包含 消息:', content.includes('消息'));
    console.log('   包含 创建聊天室:', content.includes('创建聊天室'));

    const bodyText = await page.$eval('body', el => el.innerText);
    console.log('   Body 文本 (前500字符):', bodyText.substring(0, 500));

    await browser.close();
    console.log('测试完成');
})();