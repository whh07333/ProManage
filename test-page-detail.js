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
  await page.waitForTimeout(3000);

  const result = await page.evaluate(async () => {
    const bodyDirectChildren = Array.from(document.body.children).map((c) => ({
      tag: c.tagName,
      id: c.id || 'no-id',
      className: c.className ? c.className.substring(0, 50) : 'no-class'
    }));

    const divsWithId = [];
    document.querySelectorAll('div[id]').forEach(el => {
      divsWithId.push(el.id);
    });

    return { bodyDirectChildren, divIds: divsWithId.slice(0, 30) };
  });

  console.log('\n========== 页面结构详细 ==========\n');
  console.log('body直接子元素:');
  result.bodyDirectChildren.forEach(c => console.log(`  ${c.tag}#${c.id} class="${c.className}"`));
  console.log('\ndiv id列表(前30个):');
  result.divIds.forEach(id => console.log(`  ${id}`));
  console.log('\n================================\n');

  await page.screenshot({ path: 'page-structure.png' });
  console.log('截图已保存');

  await browser.close();
})();