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

    console.log('3. 检查关闭按钮...');
    const closeBtn = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const btn = iframeDoc.querySelector('.chat-modal-close');
        if (!btn) return null;
        return {
            onclick: btn.getAttribute('onclick'),
            outerHTML: btn.outerHTML
        };
    });
    console.log('   关闭按钮:', JSON.stringify(closeBtn));

    console.log('4. 直接执行关闭逻辑...');
    const closeResult = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const btn = iframeDoc.querySelector('.chat-modal-close');
        if (!btn) return '按钮不存在';

        const modal = btn.closest('.modal');
        if (!modal) return '找不到 modal';

        modal.remove();
        return '已删除 modal';
    });
    console.log('   结果:', closeResult);

    await page.waitForTimeout(1000);

    console.log('5. 检查是否关闭...');
    const stillExists = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const wrapper = iframeDoc.querySelector('.chat-modal-wrapper');
        return wrapper ? '还存在' : '已关闭';
    });
    console.log('   状态:', stillExists);

    await browser.close();
})();
