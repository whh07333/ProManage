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

        console.log('2. 搜索 chat 相关元素...');
        const html = await page.content();

        const chatMatches = html.match(/chat[^<]{0,100}/gi);
        console.log('   包含 chat 的内容:', chatMatches ? chatMatches.slice(0, 5) : '无');

        const chatbarMatches = html.match(/chatbar[^<]{0,100}/gi);
        console.log('   包含 chatbar 的内容:', chatbarMatches ? chatbarMatches.slice(0, 5) : '无');

        const iconChatMatches = html.match(/icon-chat[^<]{0,100}/gi);
        console.log('   包含 icon-chat 的内容:', iconChatMatches ? iconChatMatches.slice(0, 5) : '无');

        console.log('\n3. 等待更长时间后检查...');
        await page.waitForTimeout(5000);

        const html2 = await page.content();
        const chatbarMatches2 = html2.match(/chatbar[^<]{0,100}/gi);
        console.log('   5秒后包含 chatbar 的内容:', chatbarMatches2 ? chatbarMatches2.slice(0, 5) : '无');

        const iconChatMatches2 = html2.match(/icon-chat[^<]{0,100}/gi);
        console.log('   5秒后包含 icon-chat 的内容:', iconChatMatches2 ? iconChatMatches2.slice(0, 5) : '无');

    } catch (error) {
        console.log('ERROR:', error.message);
    } finally {
        await browser.close();
    }
})();
