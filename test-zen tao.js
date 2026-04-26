const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  try {
    console.log('正在访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000 });
    
    console.log('页面标题:', await page.title());
    
    // 检查页面内容
    const content = await page.content();
    if (content.includes('禅道')) {
      console.log('✓ 禅道系统访问成功！');
    } else if (content.includes('安装')) {
      console.log('✓ 禅道系统已部署，需要完成安装向导');
    } else {
      console.log('✗ 页面内容不符合预期');
    }
    
  } catch (error) {
    console.error('访问失败:', error.message);
  } finally {
    await browser.close();
  }
})();
