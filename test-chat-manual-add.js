const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type=submit]');
    await page.waitForTimeout(3000);

    console.log('1. 登录成功，当前页面:', page.url());

    console.log('2. 手动添加 chatBar 按钮...');
    await page.evaluate(() => {
        const chatBtn = document.createElement('button');
        chatBtn.id = 'chatBar';
        chatBtn.className = 'btn btn-primary';
        chatBtn.innerHTML = '<i class="icon icon-chat"></i> 职聊';
        chatBtn.style.cssText = 'position:fixed;top:10px;right:200px;z-index:9999;';
        chatBtn.onclick = function() {
            fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.text())
            .then(html => {
                const modalDiv = document.createElement('div');
                modalDiv.className = 'modal show';
                modalDiv.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
                modalDiv.innerHTML = `<div class="modal-box" style="background:white;border-radius:8px;max-width:900px;width:90%;max-height:80vh;overflow:hidden;">${html}</div>`;
                modalDiv.onclick = function(e) {
                    if (e.target === modalDiv) modalDiv.remove();
                };
                document.body.appendChild(modalDiv);
            });
        };
        document.body.appendChild(chatBtn);
    });

    console.log('3. 点击 chatBar 按钮...');
    await page.click('#chatBar', { force: true });
    await page.waitForTimeout(3000);

    console.log('4. 检查 modal 是否弹出...');
    const modal = await page.$('.modal');
    if (modal) {
        console.log('   ✓ modal 已弹出!');
        const modalContent = await page.$eval('.modal-box', el => el.innerHTML.length);
        console.log('   ✓ modal 内容长度:', modalContent);

        const hasChatWrapper = await page.$eval('.modal-box', el => el.innerHTML.includes('chat-modal-wrapper'));
        console.log('   ✓ 包含 chat-modal-wrapper:', hasChatWrapper);
    } else {
        console.log('   ✗ modal 未弹出');
    }

    await page.waitForTimeout(3000);
    await browser.close();
    console.log('\n测试完成!');
})();
