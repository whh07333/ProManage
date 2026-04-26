const { chromium } = require('playwright');

async function runTests() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    let xhrFromIframe = null;

    page.on('response', async (response) => {
        const url = response.url();
        if (url.includes('m=chat') && url.includes('f=createRoom')) {
            const text = await response.text();
            console.log('INTERCEPTED XHR:', response.status(), url.substring(0, 100));
            console.log('Response:', text.substring(0, 200));
            xhrFromIframe = text;
        }
    });

    page.on('console', msg => {
        console.log('BROWSER:', msg.text());
    });

    async function openChatModal() {
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            if (!iframe) return;
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

        console.log('\n========== 打开聊天窗口 ==========\n');
        await openChatModal();

        const fl = page.frameLocator('#appIframe-my');
        await fl.locator('#createRoomBtn').click();
        await page.waitForTimeout(500);

        const newName = 'FrameTest' + Date.now();
        await fl.locator('#newRoomName').fill(newName);
        await page.waitForTimeout(200);
        console.log('输入:', newName);

        await fl.locator('#confirmCreateRoom').click();
        console.log('已点击确认');
        await page.waitForTimeout(3000);

        const namesAfter = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return Array.from(iframeDoc.querySelectorAll('#roomList .room-item .room-name'))
                .map(el => el.textContent.trim());
        });
        console.log('列表:', JSON.stringify(namesAfter));
        console.log(xhrFromIframe ? '✅ XHR intercepted' : '❌ XHR not intercepted');

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
