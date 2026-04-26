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
        await chatBar.click();

        console.log('3. 等待 modal 内容加载...');
        await page.waitForTimeout(8000);

        console.log('4. 检查所有 modal...');
        const modals = await frame.$$('.modal');
        console.log('   modal 数量:', modals.length);

        for (let i = 0; i < modals.length; i++) {
            const modal = modals[i];
            const id = await modal.getAttribute('id');
            const className = await modal.getAttribute('class');
            const modalHTML = await modal.evaluate(el => el.outerHTML);
            console.log(`\n   modal ${i}:`);
            console.log(`   id: ${id}`);
            console.log(`   class: ${className}`);
            console.log(`   HTML 长度: ${modalHTML.length}`);
            console.log(`   HTML (前1000字符): ${modalHTML.substring(0, 1000)}`);
        }

        console.log('\n5. 截图...');
        await page.screenshot({ path: '/Users/whh073/zentaopms/chat-modal-loaded.png', fullPage: false });

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
