const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('1. 登录...');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0', timeout: 15000 });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);
        console.log('   登录成功');

        console.log('2. 获取 iframe 并点击 chatBar...');
        const iframe = await page.$('#appIframe-my');
        const frame = await iframe.contentFrame();

        const chatBar = await frame.$('#chatBar');

        console.log('   点击前 - 现有 modal 数量:', (await frame.$$('.modal')).length);

        await chatBar.click();
        await page.waitForTimeout(5000);

        console.log('   点击后 - 现有 modal 数量:', (await frame.$$('.modal')).length);

        const modals = await frame.$$('.modal');
        for (let i = 0; i < modals.length; i++) {
            const title = await modals[i].$eval('.modal-title', el => el.textContent).catch(() => 'no title');
            const id = await modals[i].getAttribute('id');
            console.log(`   modal ${i}: id=${id}, title=${title}`);
        }

        console.log('3. 尝试使用 page.click 在主页面点击...');
        await page.reload({ waitUntil: 'networkidle0' });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(5000);

        const iframe2 = await page.$('#appIframe-my');
        const frame2 = await iframe2.contentFrame();
        const chatBar2 = await frame2.$('#chatBar');

        const box = await chatBar2.boundingBox();
        console.log('   chatBar 位置:', JSON.stringify(box));

        await page.mouse.click(box.x + box.width/2, box.y + box.height/2);
        await page.waitForTimeout(5000);

        console.log('   鼠标点击后 - modal 数量:', (await frame2.$$('.modal')).length);

        console.log('4. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-click-test.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
