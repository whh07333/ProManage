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
    await page.waitForTimeout(5000);

    console.log('\n=== 在页面 HTML 源码中搜索 chat ===');
    const html = await page.content();
    const lines = html.split('\n');
    lines.forEach((line, i) => {
      if (line.includes('chat') || line.includes('Chat') || line.includes('职聊')) {
        console.log(`行 ${i}: ${line.substring(0, 200)}`);
      }
    });

    console.log('\n=== 搜索 toolbar 区域的HTML ===');
    const toolbarHtml = await page.evaluate(() => {
      const toolbar = document.querySelector('#appsToolbar');
      return toolbar ? toolbar.outerHTML : '未找到 #appsToolbar';
    });
    console.log('toolbar HTML:', toolbarHtml.substring(0, 500));

    console.log('\n截图保存...');
    await page.screenshot({ path: 'page-check.png', fullPage: true });

    console.log('\n按 Enter 关闭...');
    await new Promise(resolve => process.stdin.once('data', resolve));

  } catch (error) {
    console.error('错误:', error.message);
  } finally {
    await browser.close();
  }
})();