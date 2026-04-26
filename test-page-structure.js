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

  const result = await page.evaluate(async () => {
    // 检查 body 结构
    const body = document.body;

    // 检查是否有 header
    const header = document.querySelector('header');
    const mainHeader = document.querySelector('#mainHeader');
    const navbar = document.querySelector('#navbar');
    const userNav = document.querySelector('#userNav');

    return {
      bodyHasChildren: body.children.length,
      hasHeader: !!header,
      hasMainHeader: !!mainHeader,
      hasNavbar: !!navbar,
      hasUserNav: !!userNav,
      bodyFirstChild: body.firstElementChild?.tagName,
      bodyInnerHTMLStart: body.innerHTML.substring(0, 500)
    };
  });

  console.log('\n========== 页面结构 ==========\n');
  console.log('body子元素数量:', result.bodyHasChildren);
  console.log('has header:', result.hasHeader);
  console.log('has #mainHeader:', result.hasMainHeader);
  console.log('has #navbar:', result.hasNavbar);
  console.log('has #userNav:', result.hasUserNav);
  console.log('body第一个子元素:', result.bodyFirstChild);
  console.log('\nbody开头HTML:');
  console.log(result.bodyInnerHTMLStart);
  console.log('\n================================\n');

  await browser.close();
})();