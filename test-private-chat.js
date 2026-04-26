const { chromium } = require('playwright');

async function runTests() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    page.on('console', msg => console.log('BROWSER:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    async function openChatModal() {
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const chatBar = iframeDoc.getElementById('chatBar');
            if (chatBar) chatBar.click();
        });
        await page.waitForTimeout(3000);
    }

    try {
        console.log('\n========== 登录 ==========\n');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        console.log('✅ 登录成功');

        await openChatModal();

        const fl = page.frameLocator('#appIframe-my');

        console.log('\n========== 切换到联系人 ==========\n');
        await fl.locator('.tab-item[data-tab="contacts"]').click();
        await page.waitForTimeout(3000);

        const contactsBefore = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return Array.from(iframeDoc.querySelectorAll('#contactsList .contact-item'))
                .map(el => el.querySelector('.contact-name')?.textContent?.trim());
        });
        console.log(`联系人数量: ${contactsBefore.length}`);

        console.log('\n========== 点击第一个联系人 ==========\n');
        await fl.locator('#contactsList .contact-item').first().click();
        await page.waitForTimeout(3000);

        const currentRoomName = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.getElementById('currentRoomName')?.textContent?.trim();
        });
        console.log(`当前房间名: ${currentRoomName}`);

        const roomsTabVisible = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.getElementById('roomsTab')?.style.display !== 'none';
        });
        console.log(`聊天室标签显示: ${roomsTabVisible ? '✅ 是' : '❌ 否'}`);

        const contactsTabHidden = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.getElementById('contactsTab')?.style.display === 'none';
        });
        console.log(`联系人标签隐藏: ${contactsTabHidden ? '✅ 是' : '❌ 否'}`);

        console.log('\n========== 总结 ==========\n');
        const pass = currentRoomName && currentRoomName !== '<?php echo $lang->chat->selectRoom;?>' && roomsTabVisible && contactsTabHidden;
        console.log(`✅ 私聊功能: ${pass ? '通过' : '失败'}`);

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
