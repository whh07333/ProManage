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

    console.log('2. 在 iframe 中搜索 chat 按钮...');
    const iframeChat = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (!iframe) return { error: 'iframe not found' };

        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const walker = document.createTreeWalker(
            iframeDoc.body,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        const results = [];
        let node;
        while (node = walker.nextNode()) {
            if (node.textContent.includes('聊') || node.textContent.includes('chat') || node.textContent.includes('Chat')) {
                results.push({
                    text: node.textContent.trim(),
                    parent: node.parentElement ? node.parentElement.tagName : 'N/A',
                    parentClass: node.parentElement ? node.parentElement.className : ''
                });
            }
        }
        return { found: results, iframeBodyLength: iframeDoc.body.innerHTML.length };
    });
    console.log('   iframe 中的 chat 元素:', JSON.stringify(iframeChat, null, 2));

    console.log('3. 在 iframe 中搜索 icon-chat...');
    const iconChatInIframe = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (!iframe) return [];
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const icons = iframeDoc.querySelectorAll('[class*="icon-chat"], [class*="chat"]');
        return Array.from(icons).map(i => ({
            tag: i.tagName,
            className: i.className,
            html: i.outerHTML.substring(0, 150)
        }));
    });
    console.log('   iframe 中的 icon-chat:', JSON.stringify(iconChatInIframe, null, 2));

    console.log('4. 在 iframe 中查找聊天按钮...');
    const chatBtnInIframe = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (!iframe) return [];
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const btns = iframeDoc.querySelectorAll('button');
        return Array.from(btns).filter(b => {
            const html = b.outerHTML.toLowerCase();
            return html.includes('chat') || html.includes('聊');
        }).map(b => ({
            id: b.id,
            className: b.className,
            html: b.outerHTML.substring(0, 200)
        }));
    });
    console.log('   iframe 中的 chat 按钮:', JSON.stringify(chatBtnInIframe, null, 2));

    console.log('5. 在主页面搜索铃铛按钮...');
    const bellBtns = await page.evaluate(() => {
        const btns = document.querySelectorAll('[class*="bell"], [id*="bell"]');
        return Array.from(btns).map(b => ({
            id: b.id,
            className: b.className,
            html: b.outerHTML.substring(0, 200)
        }));
    });
    console.log('   铃铛按钮:', JSON.stringify(bellBtns, null, 2));

    await browser.close();
})();
