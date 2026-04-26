const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(3000);

  const result = await page.evaluate(async () => {
    const results = [];

    // 搜索 id="chat-btn"
    const chatBtn = document.querySelector('#chat-btn');
    if (chatBtn) {
      results.push('#chat-btn 元素 FOUND');
      results.push('#chat-btn outerHTML: ' + chatBtn.outerHTML);
      results.push('#chat-btn 文本内容: "' + chatBtn.textContent + '"');
    } else {
      results.push('#chat-btn: NOT FOUND');
    }

    // 搜索 icon-chat
    const iconChat = document.querySelector('.icon-chat');
    if (iconChat) {
      results.push('icon-chat FOUND: ' + iconChat.outerHTML);
    } else {
      results.push('icon-chat: NOT FOUND');
    }

    // 搜索"职聊"
    const allElements = document.querySelectorAll('*');
    let found = false;
    for (const el of allElements) {
      if (el.textContent.trim() === '职聊') {
        results.push('"职聊"文本 FOUND in <' + el.tagName.toLowerCase() + '>');
        results.push('父元素: ' + el.parentElement.outerHTML);
        found = true;
        break;
      }
    }
    if (!found) results.push('"职聊"文本: NOT FOUND');

    return results;
  });

  console.log('\n========== DevTools 搜索结果 ==========\n');
  result.forEach(line => console.log(line));
  console.log('\n========================================\n');

  await browser.close();
})();