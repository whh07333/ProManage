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
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(3000);

    console.log('3. 检查 chatModalCloseBtn...');
    const closeBtnInfo = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.getElementById('chatModalCloseBtn');
        if (!closeBtn) return { found: false };

        return {
            found: true,
            id: closeBtn.id,
            className: closeBtn.className,
            onclick: closeBtn.getAttribute('onclick'),
            outerHTML: closeBtn.outerHTML
        };
    });
    console.log('   关闭按钮:', JSON.stringify(closeBtnInfo, null, 2));

    console.log('4. 点击关闭按钮...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.getElementById('chatModalCloseBtn');
        if (closeBtn) {
            closeBtn.click();
            console.log('关闭按钮已点击');
        }
    });
    await page.waitForTimeout(1000);

    console.log('5. 检查 modal 是否关闭...');
    const modalAfter = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatWrapper = iframeDoc.querySelector('.chat-modal-wrapper');
        return { exists: chatWrapper ? true : false };
    });
    console.log('   chat-modal-wrapper 是否还存在:', modalAfter.exists ? '是 (失败)' : '否 (成功)');

    await browser.close();
})();
