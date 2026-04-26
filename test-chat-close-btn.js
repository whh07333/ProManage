const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(3000);

    console.log('1. 在页面上添加 chatBar 按钮...');
    await page.evaluate(() => {
        const chatBtn = document.createElement('button');
        chatBtn.id = 'chatBar';
        chatBtn.className = 'btn btn-primary';
        chatBtn.innerHTML = '<i class="icon icon-chat"></i> 职聊';
        chatBtn.style.cssText = 'position:fixed;top:10px;right:200px;z-index:9999;';
        chatBtn.onclick = function() {
            fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                const modalDiv = document.createElement('div');
                modalDiv.className = 'modal show';
                modalDiv.id = 'chatModal';
                modalDiv.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';

                const modalBox = document.createElement('div');
                modalBox.className = 'modal-box';
                modalBox.style.cssText = 'background:white;border-radius:8px;max-width:900px;width:90%;max-height:80vh;overflow:hidden;position:relative;';
                modalBox.innerHTML = html;

                modalDiv.appendChild(modalBox);

                window.closeChatModal = function() {
                    modalDiv.remove();
                };

                modalDiv.onclick = function(e) {
                    if (e.target === modalDiv) modalDiv.remove();
                };
                document.body.appendChild(modalDiv);
            });
        };
        document.body.appendChild(chatBtn);
    });

    console.log('2. 点击 chatBar 按钮...');
    await page.click('#chatBar', { force: true });
    await page.waitForTimeout(3000);

    console.log('3. 检查关闭按钮...');
    const closeBtn = await page.$('.chat-modal-close');
    if (closeBtn) {
        console.log('   ✓ 找到关闭按钮!');
        const btnHtml = await closeBtn.evaluate(el => el.outerHTML);
        console.log('   按钮 HTML:', btnHtml);
    } else {
        console.log('   ✗ 没有找到关闭按钮');
    }

    const modal = await page.$('#chatModal');
    if (modal) {
        console.log('   ✓ modal 已弹出');
    }

    console.log('4. 测试点击关闭按钮...');
    if (closeBtn) {
        await closeBtn.click();
        await page.waitForTimeout(1000);
        const modalAfter = await page.$('#chatModal');
        console.log('   点击后 modal 存在:', modalAfter ? '是' : '否');
    }

    await page.waitForTimeout(2000);
    await browser.close();
    console.log('\n=== 测试完成 ===');
})();
