const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    const logs = [];
    page.on('console', msg => {
        const text = msg.text();
        if (text.includes('startPrivateChat') || text.includes('enterRoom') || text.includes('contact')) {
            logs.push(`[${msg.type()}] ${text}`);
            console.log(`PAGE ${msg.type().toUpperCase()}:`, text);
        }
    });

    console.log('1. Navigate to ZenTao...');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle' });
    await page.waitForTimeout(3000);

    console.log('\n2. Check page content...');
    const url = page.url();
    console.log('Current URL:', url);

    const iframes = await page.$$('iframe');
    console.log('Iframes found:', iframes.length);
    for (let i = 0; i < iframes.length; i++) {
        const id = await iframes[i].getAttribute('id');
        const src = await iframes[i].getAttribute('src');
        console.log(`  iframe ${i}: id=${id}, src=${src}`);
    }

    if (iframes.length > 0) {
        const iframe = iframes[0];
        const iframeDoc = await iframe.contentFrame().catch(() => null);
        if (iframeDoc) {
            console.log('\n3. Looking for chatBar in iframe...');
            const chatBar = await iframeDoc.$('#chatBar');
            console.log('chatBar found:', !!chatBar);

            if (chatBar) {
                await chatBar.click();
                console.log('Clicked chatBar');
                await page.waitForTimeout(2000);

                console.log('\n4. Check modal...');
                const modal = await page.$('#chatModalWrapper');
                console.log('Modal found:', !!modal);

                console.log('\n5. Click contacts tab...');
                const contactsTab = await page.$('.chat-modal-tabs .tab-item[data-tab="contacts"]');
                if (contactsTab) {
                    await contactsTab.click();
                    console.log('Clicked contacts tab');
                    await page.waitForTimeout(3000);
                }

                console.log('\n6. Check contacts...');
                const contacts = await page.$$('.contact-item');
                console.log('Contacts found:', contacts.length);

                if (contacts.length > 0) {
                    console.log('\n7. Click first contact...');
                    await contacts[0].click();
                    await page.waitForTimeout(3000);

                    console.log('\n8. Check room name...');
                    const roomName = await page.$eval('#currentRoomName', el => el.textContent).catch(() => 'N/A');
                    console.log('Room name:', roomName);
                }
            }
        }
    }

    console.log('\n=== RELEVANT LOGS ===');
    logs.forEach(log => console.log(log));

    await page.waitForTimeout(1000);
    await browser.close();
})();