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

    async function closeCreateModal() {
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const modal = iframeDoc.getElementById('createRoomModal');
            if (modal) modal.style.display = 'none';
        });
        await page.waitForTimeout(300);
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

        const beforeCount = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#roomList .room-item').length;
        });
        console.log(`\n现有房间数: ${beforeCount}`);

        console.log('\n========== 测试1：创建新房间 ==========\n');
        const newName = '唯一性测试Room' + Date.now();
        const fl = page.frameLocator('#appIframe-my');
        await fl.locator('#createRoomBtn').click();
        await page.waitForTimeout(500);
        await fl.locator('#newRoomName').fill(newName);
        await fl.locator('#confirmCreateRoom').click();
        await page.waitForTimeout(3000);

        const after1Count = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#roomList .room-item').length;
        });
        const names1 = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return Array.from(iframeDoc.querySelectorAll('#roomList .room-item .room-name'))
                .map(el => el.textContent.trim());
        });
        console.log(`创建后数量: ${after1Count}`);
        console.log(`房间列表包含新名字: ${names1.includes(newName) ? '✅ 是' : '❌ 否'}`);

        console.log('\n========== 测试2：创建同名房间（应报错）==========\n');
        await fl.locator('#createRoomBtn').click();
        await page.waitForTimeout(500);
        await fl.locator('#newRoomName').fill(newName);
        await fl.locator('#confirmCreateRoom').click();
        await page.waitForTimeout(3000);

        const after2Count = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return iframeDoc.querySelectorAll('#roomList .room-item').length;
        });
        const errorVisible = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const err = iframeDoc.getElementById('roomNameError');
            return err ? { text: err.textContent, visible: err.style.display } : null;
        });
        console.log(`重复创建后数量: ${after2Count} (${after2Count === after1Count ? '✅ 未增加' : '❌ 异常增加'})`);
        console.log(`错误提示: ${errorVisible ? `${errorVisible.visible === 'block' ? '✅' : '❌'} ${errorVisible.text}` : '❌ 无提示'}`);

        console.log('\n========== 测试3：创建空白名字 ==========\n');
        await closeCreateModal();
        await fl.locator('#createRoomBtn').click();
        await page.waitForTimeout(500);
        await fl.locator('#newRoomName').fill('');
        await fl.locator('#confirmCreateRoom').click();
        await page.waitForTimeout(500);

        const errorBlank = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const err = iframeDoc.getElementById('roomNameError');
            return err ? { text: err.textContent, visible: err.style.display } : null;
        });
        console.log(`空白输入错误提示: ${errorBlank ? `${errorBlank.visible === 'block' ? '✅' : '❌'} ${errorBlank.text}` : '❌ 无提示'}`);

        console.log('\n========== 总结 ==========\n');
        const test1Pass = names1.includes(newName) && after1Count === beforeCount + 1;
        const test2Pass = after2Count === after1Count && errorVisible && errorVisible.visible === 'block';
        const test3Pass = errorBlank && errorBlank.visible === 'block';
        console.log(`✅ 创建新房间: ${test1Pass ? '通过' : '失败'}`);
        console.log(`✅ 唯一性校验: ${test2Pass ? '通过' : '失败'}`);
        console.log(`✅ 空白校验: ${test3Pass ? '通过' : '失败'}`);

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
