const { chromium } = require('playwright');

async function runTests() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    let fetchResult = null;
    page.on('response', async (response) => {
        const url = response.url();
        if (url.includes('ajaxGetAllUsers')) {
            try {
                const text = await response.text();
                console.log('ajaxGetAllUsers response:', response.status(), text.substring(0, 500));
                fetchResult = text;
            } catch (e) {
                console.log('Failed to read response:', e.message);
            }
        }
    });

    page.on('console', msg => console.log('BROWSER:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    async function openChatModal() {
        await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const chatBar = iframeDoc.getElementById('chatBar');
            if (chatBar) chatBar.click();
        });
        await page.waitForTimeout(3000);
    }

    try {
        console.log('\n========== 登录 ==========\n');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        console.log('✅ 登录成功');

        await openChatModal();

        console.log('\n========== 测试联系人加载 ==========\n');
        const fl = page.frameLocator('#appIframe-my');

        await fl.locator('.tab-item[data-tab="contacts"]').click();
        await page.waitForTimeout(3000);

        const contacts = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            return Array.from(iframeDoc.querySelectorAll('#contactsList .contact-item'))
                .map(el => ({
                    name: el.querySelector('.contact-name')?.textContent?.trim(),
                    dept: el.querySelector('.contact-dept')?.textContent?.trim()
                }));
        });

        console.log(`联系人数量: ${contacts.length}`);
        if (contacts.length > 0) {
            console.log('前5个联系人:');
            contacts.slice(0, 5).forEach(c => console.log(`  - ${c.name} (${c.dept})`));
            console.log('✅ 联系人加载成功');
        } else {
            console.log('❌ 没有加载到联系人');
            console.log('Fetch result:', fetchResult ? fetchResult.substring(0, 200) : 'null');
        }

    } catch (error) {
        console.error('测试异常:', error.message);
    }

    await browser.close();
}

runTests();
