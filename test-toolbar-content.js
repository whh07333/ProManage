const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  await page.goto('http://127.0.0.1:8080/www/index.php', { waitUntil: 'networkidle' });
  await page.fill('#account', 'admin');
  await page.fill('#password', 'Dabai@123456');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(8000);

  const result = await page.evaluate(async () => {
    const toolbar = document.querySelector('#appsToolbar');

    if (!toolbar) return { error: 'appsToolbar NOT FOUND' };

    return {
      found: true,
      childrenCount: toolbar.children.length,
      innerHTML: toolbar.innerHTML
    };
  });

  console.log('\n========== #appsToolbar ==========\n');
  if (result.error) {
    console.log(result.error);
  } else {
    console.log('found:', result.found);
    console.log('children count:', result.childrenCount);
    console.log('\ninnerHTML:\n', result.innerHTML);
  }
  console.log('\n================================\n');

  await browser.close();
})();