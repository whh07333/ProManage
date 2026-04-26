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
    await page.waitForTimeout(3000);

    console.log('3. 检查主页面中的 modal...');
    const mainPageModals = await page.evaluate(() => {
        const modals = document.querySelectorAll('.modal, #chatModal, [id*=chatModal]');
        return Array.from(modals).map(m => ({
            id: m.id,
            className: m.className,
            visible: m.offsetParent !== null || m.style.display !== 'none'
        }));
    });
    console.log('   主页面 modals:', JSON.stringify(mainPageModals, null, 2));

    console.log('4. 检查 iframe 中的关闭按钮...');
    const closeBtn = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (!closeBtn) return { found: false };
        return {
            found: true,
            id: closeBtn.id,
            className: closeBtn.className,
            onclick: closeBtn.getAttribute('onclick'),
            outerHTML: closeBtn.outerHTML
        };
    });
    console.log('   关闭按钮:', JSON.stringify(closeBtn, null, 2));

    console.log('5. 点击关闭按钮...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (closeBtn) closeBtn.click();
    });
    await page.waitForTimeout(1000);

    console.log('6. 检查是否关闭...');
    const modalAfter = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatWrapper = iframeDoc.querySelector('.chat-modal-wrapper');
        return { exists: chatWrapper ? true : false };
    });
    console.log('   chat-modal-wrapper:', modalAfter.exists ? '存在' : '已关闭');

    await browser.close();
})();
