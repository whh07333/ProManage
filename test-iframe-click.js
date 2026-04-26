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

    console.log('2. 在 iframe 中点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) {
            chatBar.click();
            console.log('chatBar clicked');
        } else {
            console.log('chatBar not found in iframe');
        }
    });

    await page.waitForTimeout(3000);

    console.log('3. 检查弹出的 modal...');
    const modals = await page.evaluate(() => {
        const mainModals = document.querySelectorAll('.modal, [class*="modal"]');
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const iframeModals = iframeDoc.querySelectorAll('.modal, [class*="modal"]');

        return {
            mainPage: Array.from(mainModals).map(m => ({
                id: m.id,
                className: m.className,
                visible: m.offsetParent !== null
            })),
            iframe: Array.from(iframeModals).map(m => ({
                id: m.id,
                className: m.className,
                visible: m.offsetParent !== null
            }))
        };
    });
    console.log('   主页面 modals:', JSON.stringify(modals.mainPage, null, 2));
    console.log('   iframe modals:', JSON.stringify(modals.iframe, null, 2));

    console.log('4. 搜索关闭按钮...');
    const closeBtn = await page.evaluate(() => {
        const allCloseBtns = document.querySelectorAll('[class*="close"], .modal-close, #chatModalCloseBtn');
        if (allCloseBtns.length > 0) {
            return Array.from(allCloseBtns).map(b => ({
                id: b.id,
                className: b.className,
                html: b.outerHTML.substring(0, 100)
            }));
        }

        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const iframeCloseBtns = iframeDoc.querySelectorAll('[class*="close"], .modal-close, #chatModalCloseBtn');
        return Array.from(iframeCloseBtns).map(b => ({
            id: b.id,
            className: b.className,
            html: b.outerHTML.substring(0, 100)
        }));
    });
    console.log('   关闭按钮:', JSON.stringify(closeBtn, null, 2));

    await browser.close();
})();
