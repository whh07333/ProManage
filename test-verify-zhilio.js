const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  console.log('1. 打开登录页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });

  console.log('2. 登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(3000);

  console.log('3. 截图保存...');
  await page.screenshot({ path: 'zhilio-verified.png', fullPage: true });
  console.log('   截图已保存: zhilio-verified.png');

  console.log('4. 验证"职聊"在页面中...');
  const pageHTML = await page.content();
  const zhiLiaoIndex = pageHTML.indexOf('职聊');
  console.log('   "职聊"在页面中的位置:', zhiLiaoIndex > 0 ? `找到 (位置:${zhiLiaoIndex})` : '未找到');

  await browser.close();
  console.log('\n验证完成');
})();