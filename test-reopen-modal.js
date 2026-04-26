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

    console.log('2. 第一次点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(3000);

    const modal1 = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        return !!iframeDoc.querySelector('.chat-modal-wrapper');
    });
    console.log('   第一次点击后 modal 存在:', modal1 ? '是' : '否');

    console.log('3. 点击关闭按钮...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (closeBtn) closeBtn.click();
    });
    await page.waitForTimeout(1000);

    const modal2 = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        return !!iframeDoc.querySelector('.chat-modal-wrapper');
    });
    console.log('   关闭后 modal 存在:', modal2 ? '是' : '否');

    console.log('4. 第二次点击 chatBar...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(3000);

    const modal3 = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        return !!iframeDoc.querySelector('.chat-modal-wrapper');
    });
    console.log('   第二次点击后 modal 存在:', modal3 ? '是' : '否');

    console.log('5. 检查 chatBar 按钮状态...');
    const chatBarState = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (!chatBar) return { found: false };
        return {
            found: true,
            id: chatBar.id,
            dataToggle: chatBar.getAttribute('data-toggle'),
            dataType: chatBar.getAttribute('data-type'),
            dataUrl: chatBar.getAttribute('data-url'),
            disabled: chatBar.disabled
        };
    });
    console.log('   chatBar 状态:', JSON.stringify(chatBarState, null, 2));

    await browser.close();
})();
