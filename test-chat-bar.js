const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(5000);

  const result = await page.evaluate(async () => {
    const results = {};

    // 检查 #toolbar 区域
    const toolbar = document.querySelector('#toolbar');
    if (toolbar) {
      results.hasToolbar = true;
      results.toolbarHTML = toolbar.innerHTML.substring(0, 500);
    }

    // 检查 chatBar
    const chatBar = document.querySelector('#chatBar');
    if (chatBar) {
      results.chatBar = {
        found: true,
        html: chatBar.outerHTML,
        rect: chatBar.getBoundingClientRect()
      };
    } else {
      results.chatBar = { found: false };
    }

    // 检查 messageBar (铃铛)
    const messageBar = document.querySelector('#messageBar');
    if (messageBar) {
      results.messageBar = {
        found: true,
        rect: messageBar.getBoundingClientRect()
      };
    } else {
      results.messageBar = { found: false };
    }

    // 搜索 icon-chat
    const iconChat = document.querySelector('.icon-chat');
    if (iconChat) {
      results.iconChat = { found: true };
    }

    return results;
  });

  console.log('\n========== 验证结果 ==========\n');
  console.log('hasToolbar:', result.hasToolbar);
  console.log('chatBar:', JSON.stringify(result.chatBar, null, 2));
  console.log('messageBar:', JSON.stringify(result.messageBar, null, 2));
  console.log('iconChat:', result.iconChat);
  console.log('\n================================\n');

  await page.screenshot({ path: 'chat-bar-test.png' });
  console.log('截图已保存: chat-bar-test.png');

  await browser.close();
})();