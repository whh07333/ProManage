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

    let successCount = 0;
    let failCount = 0;

    for (let i = 1; i <= 10; i++) {
        console.log(`\n=== 第 ${i} 次测试 ===`);

        // 点击 chatBar
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const chatBar = iframeDoc.getElementById('chatBar');
            if (chatBar) chatBar.click();
        });
        await page.waitForTimeout(2000);

        const openResult = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return !!iframeDoc.querySelector('.chat-modal-wrapper');
        });

        if (!openResult) {
            console.log(`   ❌ 第 ${i} 次：打开失败`);
            failCount++;
            continue;
        }
        console.log(`   ✓ 第 ${i} 次：打开成功`);

        // 点击关闭按钮
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const closeBtn = iframeDoc.querySelector('.chat-modal-close');
            if (closeBtn) closeBtn.click();
        });
        await page.waitForTimeout(1000);

        const closeResult = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return !iframeDoc.querySelector('.chat-modal-wrapper');
        });

        if (!closeResult) {
            console.log(`   ❌ 第 ${i} 次：关闭失败`);
            failCount++;
        } else {
            console.log(`   ✓ 第 ${i} 次：关闭成功`);
            successCount++;
        }
    }

    console.log(`\n========== 测试结果 ==========`);
    console.log(`成功: ${successCount} 次`);
    console.log(`失败: ${failCount} 次`);
    console.log(`总计: ${successCount + failCount} 次`);

    await browser.close();
})();
