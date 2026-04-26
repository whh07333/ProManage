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
        iframeDoc.getElementById('chatBar').click();
    });
    await page.waitForTimeout(3000);

    console.log('3. 检查所有 .modal 元素...');
    const modalsBefore = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const modals = iframeDoc.querySelectorAll('.modal');
        return Array.from(modals).map(m => ({
            id: m.id,
            className: m.className,
            visible: m.offsetParent !== null,
            childCount: m.children.length
        }));
    });
    console.log('   打开前 modals:', JSON.stringify(modalsBefore, null, 2));

    console.log('4. 点击关闭按钮...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (closeBtn) closeBtn.click();
    });
    await page.waitForTimeout(1000);

    console.log('5. 检查关闭后的 .modal 元素...');
    const modalsAfter = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const modals = iframeDoc.querySelectorAll('.modal');
        return Array.from(modals).map(m => ({
            id: m.id,
            className: m.className,
            visible: m.offsetParent !== null,
            childCount: m.children.length,
            HTML: m.outerHTML.substring(0, 300)
        }));
    });
    console.log('   关闭后 modals:', JSON.stringify(modalsAfter, null, 2));

    console.log('6. 检查 chatBar 的 data 属性...');
    const chatBarData = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        return {
            dataToggle: chatBar.getAttribute('data-toggle'),
            dataType: chatBar.getAttribute('data-type'),
            dataUrl: chatBar.getAttribute('data-url'),
            disabled: chatBar.disabled
        };
    });
    console.log('   chatBar data:', JSON.stringify(chatBarData));

    await browser.close();
})();
