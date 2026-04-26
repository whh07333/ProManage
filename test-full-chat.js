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

    async function checkChatWindow() {
        return await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return {
                headerVisible: iframeDoc.getElementById('chatHeader')?.style.display !== 'none',
                footerVisible: iframeDoc.getElementById('chatFooter')?.style.display !== 'none',
                roomName: iframeDoc.getElementById('currentRoomName')?.textContent?.trim(),
                messageCount: iframeDoc.querySelectorAll('#chatMessages .message-item').length
            };
        });
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

        console.log('\n========== 测试1：默认打开聊天室 ==========\n');
        let state = await checkChatWindow();
        console.log(`聊天窗口头部显示: ${state.headerVisible ? '✅' : '❌'}`);
        console.log(`聊天窗口底部(输入框)显示: ${state.footerVisible ? '✅' : '❌'}`);
        console.log(`当前房间名: ${state.roomName}`);
        console.log(`消息数量: ${state.messageCount}`);

        const fl = page.frameLocator('#appIframe-my');

        console.log('\n========== 测试2：切换聊天室 ==========\n');
        const roomCount = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#roomList .room-item').length;
        });
        console.log(`聊天室数量: ${roomCount}`);

        if (roomCount >= 2) {
            await fl.locator('#roomList .room-item').nth(1).click();
            await page.waitForTimeout(2000);
            state = await checkChatWindow();
            console.log(`切换后房间名: ${state.roomName}`);
            console.log(`消息数量: ${state.messageCount}`);
        }

        console.log('\n========== 测试3：切换到联系人 ==========\n');
        await fl.locator('.tab-item[data-tab="contacts"]').click();
        await page.waitForTimeout(2000);

        await fl.locator('#contactsList .contact-item').first().click();
        await page.waitForTimeout(2000);

        state = await checkChatWindow();
        console.log(`聊天窗口头部显示: ${state.headerVisible ? '✅' : '❌'}`);
        console.log(`聊天窗口底部(输入框)显示: ${state.footerVisible ? '✅' : '❌'}`);
        console.log(`当前房间名: ${state.roomName}`);
        console.log(`消息数量: ${state.messageCount}`);

        console.log('\n========== 测试4：切换联系人 ==========\n');
        const contactCount = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#contactsList .contact-item').length;
        });

        if (contactCount >= 2) {
            await fl.locator('#contactsList .contact-item').nth(1).click();
            await page.waitForTimeout(2000);
            state = await checkChatWindow();
            console.log(`切换后房间名: ${state.roomName}`);
            console.log(`消息数量: ${state.messageCount}`);
        }

        console.log('\n========== 测试5：发一条消息 ==========\n');
        const msgBefore = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#chatMessages .message-item').length;
        });

        await fl.locator('#messageContent').fill('自动化测试消息' + Date.now());
        await fl.locator('#sendBtn').click();
        await page.waitForTimeout(2000);

        const msgAfter = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#chatMessages .message-item').length;
        });
        console.log(`发消息前数量: ${msgBefore}, 发消息后数量: ${msgAfter}`);
        console.log(`发消息功能: ${msgAfter > msgBefore ? '✅ 成功' : '❌ 失败'}`);

        console.log('\n========== 总结 ==========\n');

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
