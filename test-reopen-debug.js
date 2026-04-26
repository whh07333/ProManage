const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    const networkLogs = [];
    page.on('response', response => {
        if (response.url().includes('chat')) {
            networkLogs.push({
                url: response.url(),
                status: response.status()
            });
        }
    });

    console.log('1. 登录...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(5000);

    console.log('2. 第一次点击 chatBar...');
    networkLogs.length = 0;
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(3000);
    console.log('   网络请求:', JSON.stringify(networkLogs));

    console.log('3. 点击关闭按钮...');
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
        if (closeBtn) closeBtn.click();
    });
    await page.waitForTimeout(1000);

    console.log('4. 检查 ZIN modal 状态...');
    const zinState = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        return {
            modalCount: iframeDoc.querySelectorAll('.modal').length,
            bodyClasses: iframeDoc.body.className,
            hasModalOpen: iframeDoc.body.classList.contains('modal-open'),
            zinModal: typeof window.ZIN_MODAL !== 'undefined' ? window.ZIN_MODAL : 'undefined'
        };
    });
    console.log('   ZIN 状态:', JSON.stringify(zinState));

    console.log('5. 第二次点击 chatBar...');
    networkLogs.length = 0;
    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatBar = iframeDoc.getElementById('chatBar');
        if (chatBar) chatBar.click();
    });
    await page.waitForTimeout(3000);
    console.log('   网络请求:', JSON.stringify(networkLogs));

    console.log('6. 检查第二次点击后的状态...');
    const modalAfter2 = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const chatModal = iframeDoc.querySelector('.chat-modal-wrapper');
        return {
            modalExists: !!chatModal,
            modalCount: iframeDoc.querySelectorAll('.modal').length,
            bodyClasses: iframeDoc.body.className
        };
    });
    console.log('   第二次后状态:', JSON.stringify(modalAfter2));

    await browser.close();
})();
