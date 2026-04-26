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

    console.log('2. 搜索包含"聊"字的元素...');
    const liaoElements = await page.evaluate(() => {
        const walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        const results = [];
        let node;
        while (node = walker.nextNode()) {
            if (node.textContent.includes('聊')) {
                results.push({
                    text: node.textContent.trim(),
                    parent: node.parentElement ? node.parentElement.tagName : 'N/A',
                    parentClass: node.parentElement ? node.parentElement.className : ''
                });
            }
        }
        return results;
    });
    console.log('   包含"聊"的元素:', JSON.stringify(liaoElements, null, 2));

    console.log('3. 搜索所有可见文本...');
    const bodyText = await page.evaluate(() => document.body.innerText);
    const lines = bodyText.split('\n').filter(l => l.trim());
    const chatLines = lines.filter(l => l.includes('聊') || l.includes('chat') || l.includes('Chat'));
    console.log('   包含聊/chat的文本行:', chatLines.slice(0, 10));

    console.log('4. 截图保存...');
    await page.screenshot({ path: '/Users/whh073/zentaopms/chat_screenshot.png' });
    console.log('   截图已保存');

    await browser.close();
})();
