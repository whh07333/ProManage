const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    // 清除缓存
    ignoreHTTPSErrors: true
  });
  const page = await context.newPage();

  try {
    // 强制清除所有缓存
    await context.clearCookies();
    
    console.log('正在访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000, waitUntil: 'networkidle' });

    console.log('页面标题:', await page.title());

    // 登录
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('#submit');
    await page.waitForTimeout(3000);
    console.log('登录完成！');

    // 强制刷新
    await page.reload({ waitUntil: 'networkidle' });
    await page.waitForTimeout(2000);

    console.log('刷新后页面标题:', await page.title());

    // 搜索职聊
    console.log('\n正在搜索"职聊"...');
    const pageContent = await page.content();
    if (pageContent.includes('职聊')) {
      console.log('✓ 找到"职聊"文字！');
    } else {
      console.log('✗ 未找到"职聊"文字');
    }

    // 保存页面源码
    const fs = require('fs');
    fs.writeFileSync('page_new.html', pageContent);
    console.log('已保存页面源码到 page_new.html');

  } catch (error) {
    console.error('操作失败:', error.message);
  } finally {
    await browser.close();
  }
})();