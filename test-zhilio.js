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

  console.log('3. 搜索"职聊"...');
  const pageHTML = await page.content();
  const zhiLiaoIndex = pageHTML.indexOf('职聊');
  console.log('   "职聊"在页面中的位置:', zhiLiaoIndex > 0 ? `找到 (位置:${zhiLiaoIndex})` : '未找到');

  console.log('4. 检查chat-btn元素...');
  const chatBtn = await page.$('#chat-btn');
  if (chatBtn) {
    const html = await chatBtn.innerHTML();
    console.log('   chat-btn HTML:', html);
    const text = await chatBtn.textContent();
    console.log('   chat-btn 文本:', text);
  } else {
    console.log('   chat-btn NOT FOUND');
  }

  console.log('5. 检查icon-chat元素...');
  const iconChat = await page.$('.icon-chat');
  if (iconChat) {
    console.log('   icon-chat 存在');
  } else {
    console.log('   icon-chat NOT FOUND');
  }

  await browser.close();
  console.log('\n测试完成');
})();