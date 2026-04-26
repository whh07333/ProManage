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

    console.log('2. 点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(5000);

    console.log('3. 搜索所有包含 chat-modal-wrapper 的元素...');
    const searchResult = await page.evaluate(() => {
        const mainDoc = document;
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe ? iframe.contentDocument || iframe.contentWindow.document : null;

        const mainResult = mainDoc.querySelector('.chat-modal-wrapper');
        const iframeResult = iframeDoc ? iframeDoc.querySelector('.chat-modal-wrapper') : null;

        return {
            mainPage: mainResult ? {
                found: true,
                parent: mainResult.parentElement.tagName,
                parentId: mainResult.parentElement.id,
                parentClass: mainResult.parentElement.className
            } : { found: false },
            iframe: iframeResult ? {
                found: true,
                parent: iframeResult.parentElement.tagName,
                parentId: iframeResult.parentElement.id,
                parentClass: iframeResult.parentElement.className
            } : { found: false }
        };
    });
    console.log('   主页面 chat-modal-wrapper:', JSON.stringify(searchResult.mainPage));
    console.log('   iframe chat-modal-wrapper:', JSON.stringify(searchResult.iframe));

    console.log('4. 检查关闭按钮的父元素链...');
    const parentChain = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (!closeBtn) return null;

        let el = closeBtn;
        const chain = [];
        for (let i = 0; i < 5 && el; i++) {
            chain.push({
                tag: el.tagName,
                id: el.id,
                className: el.className.substring(0, 50)
            });
            el = el.parentElement;
        }
        return chain;
    });
    console.log('   父元素链:', JSON.stringify(parentChain, null, 2));

    await browser.close();
})();
