const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  console.log('1. 打开登录页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });

  console.log('2. 登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(3000);

  console.log('\n3. 使用 DevTools 协议搜索 DOM...\n');

  const result = await page.evaluate(async () => {
    const results = [];

    // 搜索 id="chat-btn"
    const chatBtn = document.querySelector('#chat-btn');
    if (chatBtn) {
      results.push(`#chat-btn 元素: ${chatBtn.outerHTML}`);
      results.push(`#chat-btn 文本内容: "${chatBtn.textContent}"`);
    } else {
      results.push('#chat-btn: NOT FOUND');
    }

    // 搜索包含"职聊"的元素
    const allElements = document.querySelectorAll('*');
    for (const el of allElements) {
      if (el.textContent === '职聊' && el.children.length === 0) {
        results.push(`包含"职聊"的元素: <${el.tagName.toLowerCase()}> ${el.outerHTML}`);
        results.push(`父元素: <${el.parentElement.tagName.toLowerCase()}> ${el.parentElement.outerHTML}`);
      }
    }

    // 搜索 icon-chat
    const iconChat = document.querySelector('.icon-chat');
    if (iconChat) {
      results.push(`icon-chat: ${iconChat.outerHTML}`);
    } else {
      results.push('icon-chat: NOT FOUND');
    }

    // 获取 toolbar 区域 HTML
    const toolbar = document.querySelector('#appsToolbar');
    if (toolbar) {
      results.push(`\n#appsToolbar HTML:\n${toolbar.outerHTML}`);
    }

    return results;
  });

  console.log('搜索结果:');
  result.forEach(line => console.log('  ' + line));

  // 截图标记位置
  await page.screenshot({ path: 'zhilio-devtools.png', fullPage: true });
  console.log('\n截图已保存: zhilio-devtools.png');

  await browser.close();
})();