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

    console.log('2. 截取页面可见区域...');
    await page.screenshot({ path: 'screenshot.png', fullPage: false });
    console.log('   截图已保存到 screenshot.png');

    console.log('3. 查看页面上的所有按钮...');
    const allButtons = await page.evaluate(() => {
        const btns = document.querySelectorAll('button');
        return Array.from(btns).map(b => ({
            id: b.id,
            className: b.className,
            ariaLabel: b.getAttribute('aria-label'),
            title: b.getAttribute('title'),
            innerHTML: b.innerHTML.substring(0, 100)
        }));
    });
    console.log('   所有按钮:', JSON.stringify(allButtons, null, 2));

    console.log('4. 搜索 icon 或 svg 元素...');
    const icons = await page.evaluate(() => {
        const icons = document.querySelectorAll('i, svg, [class*="icon"]');
        return Array.from(icons).slice(0, 30).map(i => ({
            tag: i.tagName,
            className: i.className,
            html: i.outerHTML.substring(0, 100)
        }));
    });
    console.log('   图标元素:', JSON.stringify(icons, null, 2));

    console.log('5. 搜索有任何点击事件的元素...');
    const clickableElements = await page.evaluate(() => {
        const all = document.querySelectorAll('*');
        const clickable = [];
        all.forEach(el => {
            if (el.onclick || el.getAttribute('data-toggle') || el.getAttribute('data-url')) {
                clickable.push({
                    tag: el.tagName,
                    id: el.id,
                    className: el.className,
                    onclick: el.onclick ? '有' : '无',
                    dataToggle: el.getAttribute('data-toggle'),
                    dataUrl: el.getAttribute('data-url')
                });
            }
        });
        return clickable;
    });
    console.log('   可点击元素:', JSON.stringify(clickableElements, null, 2));

    await browser.close();
})();
