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

    console.log('2. 检查 chatBar 点击事件绑定...');
    const beforeClick = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');

        // 检查 ZIN 框架的事件
        const $chatBar = iframeDoc.defaultView.$(chatBar);
        const events = $chatBar.data('events') || $chatBar._data || {};

        return {
            elementExists: !!chatBar,
            outerHTML: chatBar ? chatBar.outerHTML.substring(0, 200) : null,
            hasJQueryData: !!$chatBar.length,
            jQueryEvents: Object.keys(events)
        };
    });
    console.log('   点击前:', JSON.stringify(beforeClick, null, 2));

    console.log('3. 第一次点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        chatBar.click();
    });
    await page.waitForTimeout(3000);

    console.log('4. 关闭后检查 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (closeBtn) closeBtn.click();
    });
    await page.waitForTimeout(1000);

    const afterClose = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');

        // 检查是否还是同一个元素
        return {
            elementExists: !!chatBar,
            outerHTML: chatBar ? chatBar.outerHTML.substring(0, 200) : null,
            id: chatBar ? chatBar.id : null,
            dataUrl: chatBar ? chatBar.getAttribute('data-url') : null
        };
    });
    console.log('   关闭后:', JSON.stringify(afterClose, null, 2));

    console.log('5. 检查 modal 相关元素...');
    const modalState = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        // 查找所有 modal 相关的元素
        const modals = iframeDoc.querySelectorAll('.modal');
        const modalBackdrops = iframeDoc.querySelectorAll('.modal-backdrop');
        const modalOverlay = iframeDoc.querySelectorAll('[class*="overlay"], [class*="mask"]');

        return {
            modalCount: modals.length,
            modalBackdropCount: modalBackdrops.length,
            modalOverlayCount: modalOverlay.length,
            modalClasses: Array.from(modals).map(m => m.className),
            bodyClassName: iframeDoc.body.className
        };
    });
    console.log('   modal 状态:', JSON.stringify(modalState, null, 2));

    console.log('6. 第二次点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        console.log('chatBar exists:', !!chatBar);
        console.log('chatBar data-url:', chatBar ? chatBar.getAttribute('data-url') : null);
        if (chatBar) {
            chatBar.click();
        }
    });
    await page.waitForTimeout(3000);

    const afterSecondClick = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        return {
            chatModalExists: !!iframeDoc.querySelector('.chat-modal-wrapper'),
            modalCount: iframeDoc.querySelectorAll('.modal').length
        };
    });
    console.log('   第二次点击后:', JSON.stringify(afterSecondClick));

    await browser.close();
})();
