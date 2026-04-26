const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();

  const errors = [];
  page.on('console', msg => {
    if (msg.type() === 'error') errors.push(msg.text());
  });

  try {
    console.log('=== 开始测试职聊显示 ===\n');

    console.log('1. 访问禅道系统...');
    await page.goto('http://localhost:8080', { timeout: 30000, waitUntil: 'networkidle' });
    console.log('   页面标题:', await page.title());

    console.log('\n2. 登录...');
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('#submit');
    await page.waitForTimeout(3000);
    console.log('   登录完成，页面标题:', await page.title());

    console.log('\n3. 强制刷新页面（跳过缓存）...');
    await page.reload({ waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(2000);

    console.log('\n4. 检查 SVG 文件内容...');
    const svgResponse = await page.request.get('http://localhost:8080/static/svg/chat.svg');
    const svgContent = await svgResponse.text();
    console.log('   SVG 文件状态:', svgResponse.status());
    console.log('   SVG 内容:', svgContent);

    const hasZhili = svgContent.includes('职聊');
    console.log('   SVG 包含"职聊":', hasZhili ? '✓ 是' : '✗ 否');

    console.log('\n5. 查找 chat-btn 元素...');
    const chatBtn = await page.$('#chat-btn');
    if (chatBtn) {
      console.log('   找到 #chat-btn 元素');

      const innerHTML = await chatBtn.innerHTML();
      console.log('   按钮内部HTML:', innerHTML);

      const img = await chatBtn.$('img');
      if (img) {
        const src = await img.getAttribute('src');
        console.log('   图片 src:', src);
      }

      console.log('\n6. 截取页面截图...');
      await page.screenshot({ path: 'chat-btn-screenshot.png', fullPage: false });
      console.log('   截图已保存: chat-btn-screenshot.png');
    } else {
      console.log('   ✗ 未找到 #chat-btn 元素');
    }

    console.log('\n7. 验证结果:');
    if (hasZhili) {
      console.log('   ✓ SVG 文件已正确更新为"职聊"');
      console.log('   ✓ 用户在浏览器中刷新页面( Cmd+Shift+R )后应能看到"职聊"');
    } else {
      console.log('   ✗ SVG 文件尚未更新');
    }

    if (errors.length > 0) {
      console.log('\n控制台错误:');
      errors.forEach(e => console.log('  -', e));
    } else {
      console.log('\n无控制台错误');
    }

  } catch (error) {
    console.error('测试失败:', error.message);
  } finally {
    await browser.close();
  }
})();