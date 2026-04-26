const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    console.log('正在访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000 });

    // 登录
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('#submit');
    await page.waitForTimeout(3000);
    console.log('登录完成！');

    // 直接访问 chat 模块
    console.log('\n直接访问 chat 模块...');
    await page.goto('http://localhost:8080/www/index.php?m=chat&f=index', { timeout: 30000 });
    await page.waitForTimeout(2000);

    const chatContent = await page.content();
    if (chatContent.includes('职聊')) {
      console.log('✓ chat 页面中找到"职聊"文字！');
    } else {
      console.log('✗ chat 页面中未找到"职聊"文字');
    }

    // 检查是否有错误
    if (chatContent.includes('Error') || chatContent.includes('Exception')) {
      console.log('⚠ 页面可能有错误');
    }

    // 保存页面源码
    const fs = require('fs');
    fs.writeFileSync('chat_page.html', chatContent);
    console.log('已保存 chat 页面源码到 chat_page.html');

  } catch (error) {
    console.error('操作失败:', error.message);
  } finally {
    await browser.close();
  }
})();