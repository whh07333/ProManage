const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    console.log('正在访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000 });

    console.log('页面标题:', await page.title());

    // 检查是否需要登录
    const loginForm = await page.$('#loginForm');
    if (loginForm) {
      console.log('需要登录，正在填写登录信息...');
      await page.fill('#account', 'admin');
      await page.fill('#password', 'Dabai@123456');
      await page.click('#submit');
      await page.waitForTimeout(3000);
      console.log('登录完成！');
    }

    console.log('页面标题:', await page.title());

    // 搜索职聊
    console.log('\n正在搜索"职聊"...');
    const pageContent = await page.content();
    if (pageContent.includes('职聊')) {
      console.log('✓ 找到"职聊"文字！');
    } else {
      console.log('✗ 未找到"职聊"文字');
    }

    // 搜索chat相关
    if (pageContent.includes('chat')) {
      console.log('✓ 找到"chat"相关代码！');
    }

    // 保存页面源码
    const fs = require('fs');
    fs.writeFileSync('page.html', await page.content());
    console.log('已保存页面源码到 page.html');

  } catch (error) {
    console.error('操作失败:', error.message);
  } finally {
    await browser.close();
  }
})();