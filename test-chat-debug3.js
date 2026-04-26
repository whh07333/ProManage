const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    console.log('1. 打开 ZenTao 登录页面...');
    await page.goto('http://localhost:8080');
    await page.waitForLoadState('networkidle');

    console.log('2. 登录中...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(8000);

    console.log('3. 获取完整 HTML...');
    const html = await page.content();

    const messageBarIndex = html.indexOf('messageBar');
    if (messageBarIndex > 0) {
        console.log('   messageBar 出现在 HTML 中的位置:', messageBarIndex);
        console.log('   周围内容:', html.substring(messageBarIndex - 100, messageBarIndex + 200));
    }

    const testBtnIndex = html.indexOf('test-btn');
    if (testBtnIndex > 0) {
        console.log('   test-btn 出现在 HTML 中的位置:', testBtnIndex);
        console.log('   周围内容:', html.substring(testBtnIndex - 50, testBtnIndex + 150));
    }

    const chatBarIndex = html.indexOf('chatBar');
    if (chatBarIndex > 0) {
        console.log('   chatBar 出现在 HTML 中的位置:', chatBarIndex);
        console.log('   周围内容:', html.substring(chatBarIndex - 100, chatBarIndex + 200));
    } else {
        console.log('   chatBar 没有出现在 HTML 中');
    }

    console.log('4. 查找 #toolbar 下的所有元素:');
    const toolbarContent = await page.evaluate(() => {
        const toolbar = document.querySelector('#toolbar');
        if (!toolbar) return 'NOT FOUND';
        return toolbar.innerHTML;
    });
    console.log('   toolbar 内容:', toolbarContent ? toolbarContent.substring(0, 2000) : 'EMPTY');

    console.log('5. 查找 #pageToolbar 下的所有元素:');
    const pageToolbarContent = await page.evaluate(() => {
        const pt = document.querySelector('#pageToolbar');
        if (!pt) return 'NOT FOUND';
        return pt.innerHTML;
    });
    console.log('   pageToolbar 内容:', pageToolbarContent ? pageToolbarContent.substring(0, 2000) : 'EMPTY');

    await browser.close();
    console.log('诊断完成');
})();