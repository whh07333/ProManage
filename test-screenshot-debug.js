const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  console.log('打开登录页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });

  console.log('登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(5000);

  // 滚动到顶部
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(1000);

  // 截图
  console.log('截图...');
  await page.screenshot({ path: 'full-page-test.png', fullPage: false });
  console.log('截图已保存: full-page-test.png');

  // 检查页面中"职聊"的位置坐标
  const result = await page.evaluate(async () => {
    const chatBtn = document.querySelector('#chat-btn');
    if (!chatBtn) return null;
    const rect = chatBtn.getBoundingClientRect();
    return {
      text: chatBtn.textContent,
      rect: { top: rect.top, left: rect.left, width: rect.width, height: rect.height }
    };
  });
  console.log('chat-btn 位置:', JSON.stringify(result));

  // 检查是否有其他元素遮挡
  const hiddenCheck = await page.evaluate(async () => {
    const chatBtn = document.querySelector('#chat-btn');
    if (!chatBtn) return 'NOT FOUND';

    const styles = window.getComputedStyle(chatBtn);
    const parentStyles = window.getComputedStyle(chatBtn.parentElement);

    // 检查是否有color透明等样式
    const spanEl = chatBtn.querySelector('span.text');
    const spanStyles = spanEl ? window.getComputedStyle(spanEl) : null;

    return {
      btnDisplay: styles.display,
      btnVisibility: styles.visibility,
      btnOpacity: styles.opacity,
      btnColor: styles.color,
      parentDisplay: parentStyles.display,
      spanColor: spanStyles ? spanStyles.color : 'no span',
      spanDisplay: spanStyles ? spanStyles.display : 'no span'
    };
  });
  console.log('样式检查:', JSON.stringify(hiddenCheck));

  await browser.close();
})();