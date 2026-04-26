const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();

  console.log('1. 访问禅道安装页面...');
  await page.goto('http://localhost:8080/install.php', {
    timeout: 30000,
    waitUntil: 'networkidle'
  });
  await page.waitForTimeout(1000);

  console.log('2. 点击"开始安装"...');
  await page.click('a:has-text("开始安装")');
  await page.waitForTimeout(2000);

  console.log('3. 接受许可协议...');
  await page.check('#zin_install_license_checkbox');
  await page.click('button:has-text("接受")');
  await page.waitForTimeout(2000);

  console.log('   当前URL:', page.url());

  console.log('4. 获取数据库配置页面上的所有input...');
  const inputs = await page.$$eval('input', inputs => inputs.map(i => ({id: i.id, name: i.name, type: i.type, value: i.value})));
  console.log('   inputs:', JSON.stringify(inputs, null, 2));

  const buttons = await page.$$eval('button', buttons => buttons.map(b => ({id: b.id, text: b.textContent.trim()})));
  console.log('   buttons:', JSON.stringify(buttons, null, 2));

  await browser.close();
})();