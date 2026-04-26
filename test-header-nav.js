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

    // 检查顶部导航栏的"职聊"
    const navLinks = document.querySelectorAll('#userNav a');
    navLinks.forEach((link, i) => {
      if (link.textContent.includes('职聊') || link.getAttribute('title') === 'chat') {
        results.zhiLiaoInNav = {
          text: link.textContent.trim(),
          href: link.href,
          parent: link.parentElement.className,
          rect: link.getBoundingClientRect()
        };
      }
    });

    // 检查铃铛
    const bell = document.querySelector('.icon-bell');
    if (bell) {
      results.bellIcon = {
        found: true,
        parent: bell.closest('li')?.className,
        rect: bell.getBoundingClientRect()
      };
    }

    // 检查底部工具栏的测试按钮
    const testBtn = document.querySelector('#test-btn');
    if (testBtn) {
      results.testBtn = {
        text: testBtn.textContent,
        rect: testBtn.getBoundingClientRect()
      };
    }

    return results;
  });

  console.log('\n========== 位置验证 ==========\n');
  console.log('顶部导航栏"职聊":', JSON.stringify(result.zhiLiaoInNav, null, 2));
  console.log('\n铃铛:', JSON.stringify(result.bellIcon, null, 2));
  console.log('\n底部测试按钮:', JSON.stringify(result.testBtn, null, 2));
  console.log('\n================================\n');

  await page.screenshot({ path: 'header-nav.png' });
  console.log('截图已保存: header-nav.png');

  await browser.close();
})();