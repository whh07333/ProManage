const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    const logs = [];
    page.on('console', msg => {
        const text = msg.text();
        logs.push(`[${msg.type()}] ${text}`);
    });

    page.on('response', async response => {
        const url = response.url();
        if (url.includes('chat') || url.includes('user')) {
            const status = response.status();
            const body = await response.text().catch(() => 'N/A');
            console.log(`\n=== HTTP ${status}: ${url} ===`);
            console.log('Body:', body.substring(0, 500));
        }
    });

    console.log('1. Login to ZenTao...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle' });
    await page.waitForTimeout(2000);

    const currentUrl = page.url();
    console.log('Current URL:', currentUrl);

    if (currentUrl.includes('login')) {
        console.log('Need to login...');
        await page.fill('#account', 'admin');
        await page.fill('#password', '123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
    }

    console.log('\n2. Navigate to my page...');
    await page.goto('http://localhost:8080/index.php?m=my', { waitUntil: 'networkidle' });
    await page.waitForTimeout(2000);

    console.log('\n3. Open chat modal...');
    const iframe = await page.$('#appIframe-my');
    if (iframe) {
        const iframeDoc = await iframe.contentFrame();
        const chatBar = await iframeDoc.$('#chatBar');
        if (chatBar) {
            await chatBar.click();
            console.log('Clicked chatBar');
            await page.waitForTimeout(2000);
        }
    }

    console.log('\n4. Switch to contacts tab...');
    const contactsTab = await page.$('.chat-modal-tabs .tab-item[data-tab="contacts"]');
    if (contactsTab) {
        await contactsTab.click();
        console.log('Clicked contacts tab');
        await page.waitForTimeout(3000);
    }

    console.log('\n5. Click first contact...');
    const contacts = await page.$$('.contact-item');
    console.log('Contacts found:', contacts.length);
    if (contacts.length > 0) {
        await contacts[0].click();
        console.log('Clicked first contact');
        await page.waitForTimeout(3000);
    }

    console.log('\n=== ALL CONSOLE LOGS ===');
    logs.forEach(log => console.log(log));

    await browser.close();
})();