const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();

  try {
    console.log('访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000, waitUntil: 'networkidle' });

    console.log('登录...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('#submit');
    await page.waitForTimeout(3000);
    console.log('登录后页面标题:', await page.title());

    console.log('\n等待页面加载完成...');
    await page.waitForTimeout(5000);

    console.log('\n=== 查看页面渲染后的 DOM 中 chat-btn 区域 ===');
    const chatArea = await page.evaluate(() => {
      const btn = document.querySelector('#chat-btn');
      if (!btn) return '未找到 #chat-btn';

      const container = document.querySelector('#chat-btn-container');
      if (!container) return '未找到 #chat-btn-container';

      return container.innerHTML;
    });
    console.log('chat-btn-container 内部HTML:', chatArea);

    console.log('\n=== 查找页面中所有包含"职聊"的内容 ===');
    const zhiliElements = await page.evaluate(() => {
      const result = [];
      const walker = document.createTreeWalker(
        document.body,
        NodeFilter.SHOW_TEXT,
        null,
        false
      );
      let node;
      while (node = walker.nextNode()) {
        if (node.textContent.includes('职聊')) {
          result.push({
            text: node.textContent.trim(),
            parent: node.parentElement ? node.parentElement.tagName + (node.parentElement.id ? '#' + node.parentElement.id : '') + (node.parentElement.className ? '.' + node.parentElement.className.replace(/\s+/g, '.') : '') : 'unknown'
          });
        }
      }
      return result;
    });

    if (zhiliElements.length > 0) {
      console.log('✓ 找到"职聊"文字:');
      zhiliElements.forEach(el => console.log(`  - "${el.text}" (父元素: ${el.parent})`));
    } else {
      console.log('✗ 页面上没有"职聊"文字');
    }

    console.log('\n=== 截图保存 ===');
    await page.screenshot({ path: 'page-screenshot.png', fullPage: true });
    console.log('截图已保存: page-screenshot.png');

    console.log('\n按 Enter 关闭浏览器...');
    await new Promise(resolve => process.stdin.once('data', resolve));

  } catch (error) {
    console.error('错误:', error.message);
  } finally {
    await browser.close();
  }
})();