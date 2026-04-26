const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  page.on('console', msg => console.log('Console:', msg.type(), msg.text().substring(0, 200)));
  page.on('pageerror', err => console.log('PageError:', err.message));

  console.log('1. 打开登录页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  console.log('2. 登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/index.php?m=my**', { timeout: 10000 });
  console.log('   登录后URL:', page.url());
  await page.waitForTimeout(5000);

  // Check raw HTML for chatBar
  const html = await page.content();
  console.log('3. HTML 中有 chatBar:', html.includes('id="chatBar"') ? '是' : '否');

  // Wait for ZIN rendering
  console.log('4. 等待 ZIN JavaScript 渲染...');
  await page.waitForTimeout(3000);

  // Try to find any button with icon-chat
  const iconChat = await page.$('[class*="icon-chat"]');
  console.log('   icon-chat 元素:', iconChat ? '存在' : '不存在');

  // Check if zin JS loaded
  const zinLoaded = await page.evaluate(() => typeof window.zin !== 'undefined');
  console.log('   ZIN 已加载:', zinLoaded ? '是' : '否');

  // Check for any chat-related elements
  const chatElements = await page.evaluate(() => {
    const allElements = document.querySelectorAll('*');
    const chatEls = [];
    for (let el of allElements) {
      if (el.id && el.id.toLowerCase().includes('chat')) {
        chatEls.push({id: el.id, tag: el.tagName, html: el.outerHTML.substring(0, 100)});
      }
    }
    return chatEls;
  });
  console.log('   Chat 相关元素:', JSON.stringify(chatElements, null, 2));

  // Check header toolbar area
  const headerToolbar = await page.$('.header-toolbar, .toolbar, [class*="toolbar"]');
  if (headerToolbar) {
    const toolbarHTML = await headerToolbar.evaluate(el => el.outerHTML.substring(0, 500));
    console.log('   工具栏 HTML:', toolbarHTML);
  }

  await page.screenshot({ path: 'chat-debug-final.png', fullPage: false });
  console.log('   截图已保存');

  await browser.close();
})();