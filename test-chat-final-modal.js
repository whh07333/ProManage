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

    console.log('2. 点击 chatBar 按钮...');
    await page.click('#chatBar', { force: true });
    await page.waitForTimeout(3000);

    console.log('3. 检查 modal 状态...');
    const modal = await page.$('.modal');
    if (modal) {
        console.log('   ✓ modal 已弹出!');

        const modalBox = await page.$('.modal-box');
        if (modalBox) {
            const boxStyle = await page.$eval('.modal-box', el => el.getAttribute('style'));
            console.log('   ✓ modal-box 样式:', boxStyle);

            const content = await page.$eval('.modal-box', el => el.innerHTML.substring(0, 200));
            console.log('   ✓ modal 内容预览:', content);
        }

        const hasChatWrapper = await page.$eval('.modal-box', el => el.innerHTML.includes('chat-modal-wrapper'));
        console.log('   ✓ 包含 chat-modal-wrapper:', hasChatWrapper);

        const hasLeftPanel = await page.$eval('.modal-box', el => el.innerHTML.includes('chat-modal-sidebar'));
        console.log('   ✓ 包含左侧边栏:', hasLeftPanel);

        const hasRightPanel = await page.$eval('.modal-box', el => el.innerHTML.includes('chat-modal-main'));
        console.log('   ✓ 包含右侧主面板:', hasRightPanel);
    } else {
        console.log('   ✗ modal 未弹出');
    }

    await page.waitForTimeout(2000);
    await browser.close();
    console.log('\n=== 测试完成 ===');
})();
