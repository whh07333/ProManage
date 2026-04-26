const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();

  console.log('1. 访问禅道首页...');
  await page.goto('http://localhost:8080/www/index.php', {
    timeout: 30000,
    waitUntil: 'networkidle'
  });

  console.log('2. 登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('#submit');
  await page.waitForTimeout(5000);

  console.log('3. 搜索"职聊"...');
  const pageHTML = await page.content();
  const zhiLiaoIndex = pageHTML.indexOf('职聊');
  console.log('   "职聊"在页面中的位置:', zhiLiaoIndex > 0 ? '找到' : '未找到');

  console.log('4. 检查chat-btn...');
  const chatBtn = await page.$('#chat-btn');
  if (chatBtn) {
    const html = await chatBtn.innerHTML();
    console.log('   chat-btn HTML:', html);
  } else {
    console.log('   chat-btn NOT FOUND');
  }

  await browser.close();
})();