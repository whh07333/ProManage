const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    page.on('console', msg => console.log('PAGE LOG:', msg.text()));

    console.log('1. Login to ZenTao...');
    await page.goto('http://localhost');
    await page.waitForTimeout(2000);

    console.log('2. Find and click chatBar...');
    const iframe = await page.$('#appIframe-my');
    if (!iframe) {
        console.log('ERROR: iframe #appIframe-my not found');
        await browser.close();
        return;
    }

    const iframeDoc = await iframe.contentFrame();
    const chatBar = await iframeDoc.$('#chatBar');
    if (chatBar) {
        console.log('Found chatBar, clicking...');
        await chatBar.click();
        await page.waitForTimeout(1000);
    } else {
        console.log('chatBar not found');
        await browser.close();
        return;
    }

    console.log('3. Check modal is open...');
    const modal = await page.$('#chatModalWrapper');
    console.log('Modal found:', !!modal);

    console.log('4. Check current room in header...');
    const roomName = await page.$eval('#currentRoomName', el => el.textContent).catch(() => 'not found');
    console.log('Current room name:', roomName);

    console.log('5. Click contacts tab...');
    const contactsTab = await page.$('.chat-modal-tabs .tab-item[data-tab="contacts"]');
    if (contactsTab) {
        await contactsTab.click();
        await page.waitForTimeout(2000);
        console.log('Clicked contacts tab');
    }

    console.log('6. Check contacts list...');
    const contacts = await page.$$('.contact-item');
    console.log('Number of contacts:', contacts.length);

    if (contacts.length > 0) {
        console.log('7. Click first contact...');
        await contacts[0].click();
        await page.waitForTimeout(2000);

        console.log('8. Check room name after clicking contact...');
        const newRoomName = await page.$eval('#currentRoomName', el => el.textContent).catch(() => 'not found');
        console.log('Room name after click:', newRoomName);

        console.log('9. Check chatMessages...');
        const messages = await page.$$('.message-item');
        console.log('Number of messages:', messages.length);
    }

    await page.waitForTimeout(1000);
    await browser.close();
    console.log('Test completed');
})();