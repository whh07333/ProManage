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
    // 搜索页面中所有包含"职聊"的元素
    const all = document.querySelectorAll('*');
    const zhiliaoElements = [];
    for (const el of all) {
      if (el.children.length === 0 && el.textContent.trim() === '职聊') {
        zhiliaoElements.push({
          tag: el.tagName,
          html: el.outerHTML,
          parent: el.parentElement.tagName + '.' + el.parentElement.className,
          rect: el.getBoundingClientRect()
        });
      }
    }

    // 搜索"职聊"链接
    const links = Array.from(document.querySelectorAll('a')).filter(a => a.textContent.includes('职聊'));

    return {
      textElements: zhiliaoElements,
      linksWithZhiliao: links.map(l => ({
        text: l.textContent.trim(),
        href: l.href,
        parent: l.parentElement.className,
        rect: l.getBoundingClientRect()
      }))
    };
  });

  console.log('\n========== "职聊"元素 ==========\n');
  console.log('纯文本元素:', JSON.stringify(result.textElements, null, 2));
  console.log('\n链接元素:', JSON.stringify(result.linksWithZhiliao, null, 2));
  console.log('\n================================\n');

  await browser.close();
})();