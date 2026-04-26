const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1920, height: 1080 }
  });

  // 清除 HTTP 缓存
  await context.clearCookies();

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
    await page.waitForTimeout(3000);

    // 清除浏览器缓存
    await context.clearCookies();
    const client = await page.context().newCDPSession(page);
    await client.send('Network.clearBrowserCookies');
    await client.send('Network.clearBrowserCache');

    // 强制刷新
    await page.reload({ waitUntil: 'networkidle' });
    await page.waitForTimeout(3000);

    console.log('\n=== 直接获取 SVG 内容 ===');
    const svgContent = await page.evaluate(async () => {
      const response = await fetch('/static/svg/chat.svg');
      return await response.text();
    });
    console.log('SVG 内容:', svgContent);

    console.log('\n=== 检查 img 标签加载的 SVG 是否包含职聊 ===');
    const imgLoaded = await page.evaluate(() => {
      return new Promise((resolve) => {
        const img = document.querySelector('#chat-btn img');
        if (!img) {
          resolve({ error: '未找到 img' });
          return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const realImg = new Image();
        realImg.crossOrigin = 'Anonymous';
        realImg.onload = function() {
          canvas.width = realImg.width;
          canvas.height = realImg.height;
          ctx.drawImage(realImg, 0, 0);
          const dataUrl = canvas.toDataURL();
          resolve({
            width: realImg.width,
            height: realImg.height,
            dataUrl: dataUrl.substring(0, 100) + '...'
          });
        };
        realImg.onerror = function() {
          resolve({ error: '图片加载失败' });
        };
        realImg.src = img.src;
      });
    });
    console.log('图片信息:', JSON.stringify(imgLoaded));

    console.log('\n=== 再次查找"职聊" ===');
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
          result.push(node.textContent.trim());
        }
      }
      return result;
    });

    if (zhiliElements.length > 0) {
      console.log('✓ 找到"职聊":', zhiliElements);
    } else {
      console.log('✗ 页面上没有"职聊"');
    }

    console.log('\n截图保存为 page-final.png');
    await page.screenshot({ path: 'page-final.png', fullPage: true });

    console.log('\n按 Enter 关闭浏览器...');
    await new Promise(resolve => process.stdin.once('data', resolve));

  } catch (error) {
    console.error('错误:', error.message);
  } finally {
    await browser.close();
  }
})();