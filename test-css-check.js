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
    const chatBtn = document.querySelector('#chat-btn');
    if (!chatBtn) return { error: '#chat-btn NOT FOUND' };

    const rect = chatBtn.getBoundingClientRect();
    const styles = window.getComputedStyle(chatBtn);
    const parentStyles = window.getComputedStyle(chatBtn.parentElement);

    return {
      elementFound: true,
      tagName: chatBtn.tagName,
      id: chatBtn.id,
      className: chatBtn.className,
      textContent: chatBtn.textContent,
      html: chatBtn.outerHTML,
      rect: {
        top: rect.top,
        left: rect.left,
        width: rect.width,
        height: rect.height
      },
      visibility: styles.visibility,
      display: styles.display,
      opacity: styles.opacity,
      width: styles.width,
      height: styles.height,
      parentDisplay: parentStyles.display,
      parentVisibility: parentStyles.visibility
    };
  });

  console.log('\n========== #chat-btn 详细分析 ==========\n');
  console.log(JSON.stringify(result, null, 2));
  console.log('\n========================================\n');

  await browser.close();
})();