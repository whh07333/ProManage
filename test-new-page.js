const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  console.log('1. 打开登录页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });

  console.log('2. 登录...');
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(5000);

  console.log('3. 强制刷新页面...');
  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle', force:true });
  await page.waitForTimeout(5000);

  const result = await page.evaluate(async () => {
    // 搜索所有包含 id 的元素
    const allIds = [];
    const allElements = document.querySelectorAll('*');
    for (const el of allElements) {
      if (el.id && (el.id.includes('chat') || el.id.includes('toolbar') || el.id.includes('header'))) {
        allIds.push({id: el.id, tag: el.tagName, html: el.outerHTML.substring(0, 200)});
      }
    }

    // 搜索 icon 类
    const icons = [];
    const iconElements = document.querySelectorAll('[class*="icon-"]');
    iconElements.forEach(el => {
      if (el.className.includes('icon-')) {
        icons.push(el.className);
      }
    });

    // 获取 body 结构
    const bodyClasses = document.body.className;
    const bodyChildren = Array.from(document.body.children).map(c => c.tagName);

    return { allIds, icons: icons.slice(0, 20), bodyClasses, bodyChildren };
  });

  console.log('\n========== 页面元素 ==========\n');
  console.log('body classes:', result.bodyClasses);
  console.log('body children:', result.bodyChildren);
  console.log('\n包含chat/toolbar/header的元素:');
  result.allIds.forEach(item => console.log(`  ${item.tag}#${item.id}`));
  console.log('\n图标类 (前20个):');
  result.icons.forEach(c => console.log(`  ${c}`));
  console.log('\n================================\n');

  await page.screenshot({ path: 'full-page-new.png' });
  console.log('截图已保存: full-page-new.png');

  await browser.close();
})();