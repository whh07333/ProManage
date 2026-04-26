const { chromium } = require('playwright');

async function debug() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    console.log('\n========== 第1步：登录 ==========\n');
    await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
    await page.fill('#account', 'admin');
    await page.fill('#password', 'Dabai@123456');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(3000);

    console.log('\n========== 第2步：检查数据库中的聊天室 ==========\n');

    const dbgResult = await page.evaluate(async () => {
        const response = await fetch('/index.php?m=chat&f=createRoom&t=json', {
            method: 'POST',
            body: new URLSearchParams({ name: 'DebugRoom' + Date.now(), type: 'private' }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        return data;
    });
    console.log('createRoom result:', JSON.stringify(dbgResult));

    const roomID = dbgResult.roomID;
    if (roomID) {
        const getRoomsResult = await page.evaluate(async (rid) => {
            const response = await fetch('/index.php?m=chat&f=getRooms&t=json', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            return data;
        }, roomID);
        console.log('getRooms result:', JSON.stringify(getRoomsResult));

        const getRoomResult = await page.evaluate(async (rid) => {
            const response = await fetch('/index.php?m=chat&f=getRoom&t=json', {
                method: 'POST',
                body: new URLSearchParams({ roomID: rid }),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            return data;
        }, roomID);
        console.log('getRoom result:', JSON.stringify(getRoomResult));

        const getMembersResult = await page.evaluate(async (rid) => {
            const response = await fetch('/index.php?m=chat&f=getMembers&t=json', {
                method: 'POST',
                body: new URLSearchParams({ roomID: rid }),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            return data;
        }, roomID);
        console.log('getMembers result:', JSON.stringify(getMembersResult));
    }

    console.log('\n========== 第3步：检查 modal 中渲染的列表 ==========\n');

    await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (iframe) {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const chatBar = iframeDoc.getElementById('chatBar');
            if (chatBar) chatBar.click();
        }
    });
    await page.waitForTimeout(2000);

    const listInfo = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (!iframe) return 'no iframe';
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const roomList = iframeDoc.getElementById('roomList');
        if (!roomList) return 'no roomList';
        const items = roomList.querySelectorAll('.room-item');
        const rooms = [];
        items.forEach(item => {
            rooms.push({
                roomId: item.dataset.roomId,
                name: item.querySelector('.room-name')?.textContent
            });
        });
        return { count: items.length, rooms };
    });
    console.log('roomList in modal:', JSON.stringify(listInfo, null, 2));

    const sidebarHeader = await page.evaluate(() => {
        const iframe = document.getElementById('appIframe-my');
        if (!iframe) return 'no iframe';
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        const header = iframeDoc.querySelector('.chat-modal-sidebar-header');
        return header ? header.innerHTML : 'not found';
    });
    console.log('sidebar header:', sidebarHeader);

    await browser.close();
}

debug();