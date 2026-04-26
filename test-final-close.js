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
    console.log('   登录成功');

    console.log('2. 添加 chatBar 按钮...');
    await page.evaluate(() => {
        if (document.getElementById('chatBar')) return;
        const chatBtn = document.createElement('button');
        chatBtn.id = 'chatBar';
        chatBtn.className = 'btn btn-primary';
        chatBtn.innerHTML = '<i class="icon icon-chat"></i> 职聊';
        chatBtn.style.cssText = 'position:fixed;top:50px;right:50px;z-index:99999;padding:10px 20px;';
        document.body.appendChild(chatBtn);
    });

    console.log('3. 等待按钮添加...');
    await page.waitForTimeout(1000);

    console.log('4. 点击 chatBar...');
    await page.click('#chatBar');
    await page.waitForTimeout(5000);

    console.log('5. 检查 modal 是否弹出...');
    const modal = await page.$('#chatModal');
    if (!modal) {
        console.log('   ✗ modal 没有弹出');
        await browser.close();
        return;
    }
    console.log('   ✓ modal 已弹出');

    console.log('6. 检查关闭按钮...');
    const closeBtn = await page.$('.chat-modal-close');
    if (!closeBtn) {
        console.log('   ✗ 关闭按钮不存在');
        await browser.close();
        return;
    }
    console.log('   ✓ 关闭按钮存在');

    const closeBtnText = await closeBtn.textContent();
    console.log('   关闭按钮文字: "' + closeBtnText + '"');

    const closeBtnStyles = await closeBtn.evaluate(el => {
        const style = window.getComputedStyle(el);
        return `position:${style.position}, top:${style.top}, right:${style.right}, z-index:${style.zIndex}`;
    });
    console.log('   关闭按钮样式:', closeBtnStyles);

    console.log('7. 点击关闭按钮...');
    await closeBtn.click();
    await page.waitForTimeout(2000);

    const modalAfterClick = await page.$('#chatModal');
    console.log('   点击后 modal 是否还存在:', modalAfterClick ? '✗ 是 (关闭失败)' : '✓ 否 (关闭成功)');

    await browser.close();
    console.log('\n测试完成!');
})();
