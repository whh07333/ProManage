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

    console.log('2. 测试 fetch...');
    const fetchResult = await page.evaluate(async () => {
        try {
            const response = await fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            return {
                ok: response.ok,
                status: response.status,
                length: text.length,
                hasCloseBtn: text.includes('chatModalCloseBtn')
            };
        } catch (e) {
            return { error: e.message };
        }
    });
    console.log('   fetch 结果:', JSON.stringify(fetchResult));

    console.log('3. 添加 chatBar...');
    await page.evaluate(() => {
        if (document.getElementById('chatBar')) return;
        const chatBtn = document.createElement('button');
        chatBtn.id = 'chatBar';
        chatBtn.innerHTML = '职聊';
        chatBtn.style.cssText = 'position:fixed;top:50px;right:50px;z-index:99999;padding:10px 20px;background:#007bff;color:white;border:none;';
        document.body.appendChild(chatBtn);
    });

    console.log('4. 绑定点击事件...');
    await page.evaluate(() => {
        document.getElementById('chatBar').onclick = async function() {
            console.log('chatBar clicked');
            const response = await fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            console.log('fetch status:', response.status);
            const html = await response.text();
            console.log('html length:', html.length);

            const modalDiv = document.createElement('div');
            modalDiv.id = 'chatModal';
            modalDiv.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:99999;display:flex;align-items:center;justify-content:center;';

            const modalBox = document.createElement('div');
            modalBox.className = 'modal-box';
            modalBox.style.cssText = 'background:white;border-radius:8px;max-width:900px;width:90%;max-height:80vh;overflow:hidden;position:relative;';
            modalBox.innerHTML = html;

            modalDiv.appendChild(modalBox);
            document.body.appendChild(modalDiv);
            console.log('modal added');
        };
    });

    console.log('5. 点击 chatBar...');
    await page.click('#chatBar');
    await page.waitForTimeout(5000);

    console.log('6. 检查 modal...');
    const modal = await page.$('#chatModal');
    console.log('   modal:', modal ? '存在' : '不存在');

    if (modal) {
        const closeBtn = await page.$('#chatModalCloseBtn');
        console.log('   关闭按钮:', closeBtn ? '存在' : '不存在');

        if (closeBtn) {
            console.log('7. 点击关闭按钮...');
            await closeBtn.click();
            await page.waitForTimeout(1000);

            const modalAfter = await page.$('#chatModal');
            console.log('   结果:', modalAfter ? '失败' : '成功');
        }
    }

    await browser.close();
})();
