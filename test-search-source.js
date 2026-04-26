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

    console.log('2. 获取页面完整 HTML 源码...');
    const html = await page.content();
    console.log('   HTML 长度:', html.length);

    console.log('3. 在源码中搜索 chat 相关内容...');
    const chatMatches = [];
    const regex = /<[^>]*chat[^>]*>/gi;
    let match;
    while ((match = regex.exec(html)) !== null) {
        chatMatches.push(match[0]);
    }
    console.log('   找到的 chat 标签:', chatMatches.slice(0, 20));

    console.log('4. 搜索包含 icon-chat 的元素...');
    const iconChat = await page.$$('[class*="icon-chat"], [class*="chat"]');
    for (const el of iconChat) {
        const tag = await el.evaluate(e => e.tagName);
        const cls = await el.evaluate(e => e.className);
        const id = await el.evaluate(e => e.id);
        const html2 = await el.evaluate(e => e.outerHTML);
        console.log('   标签:', tag, 'ID:', id, 'Class:', cls);
        console.log('   HTML:', html2.substring(0, 200));
    }

    console.log('5. 搜索 data-type=ajax 或 data-toggle=modal...');
    const ajaxBtns = await page.$$('[data-toggle="modal"], [data-type="ajax"]');
    for (const btn of ajaxBtns) {
        const html2 = await btn.evaluate(e => e.outerHTML);
        if (html2.toLowerCase().includes('chat')) {
            console.log('   找到 chat 按钮:', html2.substring(0, 300));
        }
    }

    await browser.close();
})();
