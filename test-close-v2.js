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

    console.log('2. 添加 chatBar 按钮并绑定点击事件...');
    await page.evaluate(() => {
        if (document.getElementById('chatBar')) return;

        const chatBtn = document.createElement('button');
        chatBtn.id = 'chatBar';
        chatBtn.className = 'btn btn-primary';
        chatBtn.innerHTML = '<i class="icon icon-chat"></i> 职聊';
        chatBtn.style.cssText = 'position:fixed;top:50px;right:50px;z-index:99999;padding:10px 20px;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;';
        chatBtn.onclick = async function() {
            console.log('点击了 chatBar');
            try {
                const response = await fetch('/index.php?m=chat&f=index&onlybody=yes', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                console.log('fetch 成功, 长度:', html.length);

                const modalDiv = document.createElement('div');
                modalDiv.id = 'chatModal';
                modalDiv.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:99999;display:flex;align-items:center;justify-content:center;';

                const modalBox = document.createElement('div');
                modalBox.className = 'modal-box';
                modalBox.style.cssText = 'background:white;border-radius:8px;max-width:900px;width:90%;max-height:80vh;overflow:hidden;position:relative;';
                modalBox.innerHTML = html;

                window.closeChatModal = function() {
                    console.log('执行关闭');
                    modalDiv.remove();
                };

                modalDiv.appendChild(modalBox);
                modalDiv.onclick = function(e) {
                    if (e.target === modalDiv) {
                        console.log('点击背景关闭');
                        modalDiv.remove();
                    }
                };
                document.body.appendChild(modalDiv);
                console.log('modal 已添加到页面');
            } catch (e) {
                console.error('错误:', e);
            }
        };
        document.body.appendChild(chatBtn);
        console.log('chatBar 已添加');
    });

    await page.waitForTimeout(500);

    console.log('3. 点击 chatBar...');
    await page.click('#chatBar');
    await page.waitForTimeout(3000);

    console.log('4. 检查 modal...');
    const modal = await page.$('#chatModal');
    console.log('   modal 存在:', modal ? '是' : '否');

    console.log('5. 检查关闭按钮...');
    const closeBtn = await page.$('.chat-modal-close');
    console.log('   关闭按钮存在:', closeBtn ? '是' : '否');

    if (closeBtn) {
        console.log('6. 点击关闭按钮...');
        await closeBtn.click({ force: true });
        await page.waitForTimeout(1000);

        const modalAfter = await page.$('#chatModal');
        console.log('   modal 是否还存在:', modalAfter ? '是 (失败)' : '否 (成功)');
    }

    await browser.close();
    console.log('\n完成!');
})();
