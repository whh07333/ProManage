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

        console.log('2. 点击聊天按钮...');
        const chatBtn = await page.$('#chatBar');
        if (chatBtn) {
            await chatBtn.click();
            console.log('   已点击 chatBar 按钮');
            await page.waitForTimeout(3000);

            console.log('3. 检查弹出的 modal...');
            const modal = await page.$('.modal');
            if (modal) {
                console.log('   找到 modal 元素');
                const modalBox = await page.$('.modal-box');
                if (modalBox) {
                    const boxContent = await modalBox.textContent();
                    console.log('   modal-box 内容长度:', boxContent.length);
                    console.log('   包含 chat-modal-wrapper:', boxContent.includes('chat-modal-wrapper'));
                } else {
                    console.log('   没有找到 modal-box');
                }

                const iframe = await page.$('.modal iframe');
                if (iframe) {
                    console.log('   找到 iframe');
                    const iframeContent = await iframe.contentFrame();
                    if (iframeContent) {
                        const bodyText = await iframeContent.textContent();
                        console.log('   iframe 内容长度:', bodyText.length);
                        console.log('   iframe 包含 chat-modal:', bodyText.includes('chat-modal'));
                    }
                }
            } else {
                console.log('   没有找到 modal');
            }
        } else {
            console.log('   没有找到 chatBar 按钮');
        }

        await page.waitForTimeout(2000);

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
