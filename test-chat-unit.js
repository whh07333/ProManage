const { chromium } = require('playwright');

async function runTests() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    const results = [];

    function addTest(name, passed, message) {
        results.push({ name, status: passed ? 'PASS' : 'FAIL', message });
        console.log(`${passed ? '✅' : '❌'} ${name}: ${message}`);
    }

    async function getIframeDoc(page) {
        return await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            if (!iframe) return null;
            return iframe.contentDocument || iframe.contentWindow.document;
        });
    }

    async function apiGet(page, url) {
        return await page.evaluate(async (apiUrl) => {
            const response = await fetch(apiUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            try {
                return { ok: response.ok, status: response.status, data: JSON.parse(text) };
            } catch (e) {
                return { ok: response.ok, status: response.status, error: text };
            }
        }, url);
    }

    async function apiPost(page, url, body) {
        return await page.evaluate(async ({apiUrl, formData}) => {
            const fd = new FormData();
            for (const key in formData) {
                fd.append(key, formData[key]);
            }
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            try {
                return { ok: response.ok, status: response.status, data: JSON.parse(text) };
            } catch (e) {
                return { ok: response.ok, status: response.status, error: text };
            }
        }, {apiUrl: url, formData: body});
    }

    try {
        console.log('\n========== 第1步：登录 ==========\n');
        await page.goto('http://localhost:8080', { waitUntil: 'networkidle0' });
        await page.fill('#account', 'admin');
        await page.fill('#password', 'Dabai@123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        const loggedIn = await page.evaluate(() => document.body.innerText.includes('地盘'));
        addTest('用户登录', loggedIn, loggedIn ? '登录成功' : '登录失败');

        console.log('\n========== 第2步：测试 Chat UI 组件 ==========\n');
        const chatUI = await page.evaluate(async () => {
            const response = await fetch('/index.php?m=chat&f=index&onlybody=yes', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await response.text();
            return {
                ok: response.ok,
                status: response.status,
                length: text.length,
                hasWrapper: text.includes('chat-modal-wrapper'),
                hasSidebar: text.includes('chat-modal-sidebar'),
                hasMessageForm: text.includes('messageForm'),
                hasTabs: text.includes('chat-modal-tabs')
            };
        });

        addTest('Chat URL 访问', chatUI.ok, `HTTP ${chatUI.status}, ${chatUI.length} 字节`);
        addTest('Chat UI - 窗口布局', chatUI.hasWrapper, chatUI.hasWrapper ? '存在' : '未找到');
        addTest('Chat UI - 侧边栏', chatUI.hasSidebar, chatUI.hasSidebar ? '存在' : '未找到');
        addTest('Chat UI - 消息表单', chatUI.hasMessageForm, chatUI.hasMessageForm ? '存在' : '未找到');
        addTest('Chat UI - 标签页', chatUI.hasTabs, chatUI.hasTabs ? '存在' : '未找到');

        console.log('\n========== 第3步：测试 Model 数据库方法 ==========\n');

        const getRoomsResult = await apiGet(page, '/index.php?m=chat&f=getRooms&t=json');
        if (getRoomsResult.data && getRoomsResult.data.result !== false) {
            const rooms = getRoomsResult.data.data || [];
            addTest('Model - getRooms()', true, `获取到 ${rooms.length} 个聊天室`);
        } else {
            addTest('Model - getRooms()', false, getRoomsResult.data?.message || '返回空数据');
        }

        console.log('\n========== 第4步：测试创建聊天室 ==========\n');

        const createResult = await apiPost(page, '/index.php?m=chat&f=createRoom&t=json', {
            name: 'TestRoom' + Date.now(),
            type: 'private'
        });
        console.log('createRoom:', JSON.stringify(createResult));

        let roomID = createResult.data?.roomID;
        addTest('Model - createRoom()', !!roomID && roomID !== false, `roomID: ${roomID}`);

        console.log('\n========== 第5步：测试发送消息 ==========\n');

        if (roomID && roomID !== false) {
            const sendResult = await apiPost(page, '/index.php?m=chat&f=sendMessage&t=json', {
                roomID: roomID,
                content: '测试消息 ' + Date.now(),
                type: 'text'
            });
            console.log('sendMessage:', JSON.stringify(sendResult));
            addTest('Model - sendMessage()', sendResult.data?.result === 'success', JSON.stringify(sendResult.data));

            const getMsgResult = await apiPost(page, '/index.php?m=chat&f=getMessages&t=json', {
                roomID: roomID
            });
            console.log('getMessages:', JSON.stringify(getMsgResult));
            if (getMsgResult.data && getMsgResult.data.result !== false) {
                const msgs = getMsgResult.data.data || [];
                addTest('Model - getMessages()', true, `获取到 ${msgs.length} 条消息`);
            } else {
                addTest('Model - getMessages()', false, getMsgResult.data?.message || '返回空数据');
            }
        } else {
            addTest('Model - sendMessage()', false, '跳过（无法创建聊天室）');
            addTest('Model - getMessages()', false, '跳过（无法创建聊天室）');
        }

        console.log('\n========== 第6步：测试 UI 打开/关闭 (iframe 上下文) ==========\n');

        const chatBtnFound = await page.evaluate(() => {
            const iframe = document.getElementById('appIframe-my');
            if (!iframe) return { found: false };
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const chatBar = iframeDoc.getElementById('chatBar');
            return { found: !!chatBar, iframeDoc: !!iframeDoc };
        });
        addTest('UI - 查找 chatBar', chatBtnFound.found, chatBtnFound.found ? '在 iframe 中找到' : '未找到');

        if (chatBtnFound.found) {
            await page.evaluate(async () => {
                const iframe = document.getElementById('appIframe-my');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const chatBar = iframeDoc.getElementById('chatBar');
                if (chatBar) chatBar.click();
            });
            await page.waitForTimeout(2000);

            const modalOpened = await page.evaluate(async () => {
                const iframe = document.getElementById('appIframe-my');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const modal = iframeDoc.querySelector('.modal.show') || iframeDoc.querySelector('.chat-modal-wrapper');
                return !!modal;
            });
            addTest('UI - 打开聊天窗口', modalOpened, modalOpened ? '窗口已弹出' : '窗口未弹出');

            const closeBtnClicked = await page.evaluate(async () => {
                const iframe = document.getElementById('appIframe-my');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const closeBtn = iframeDoc.querySelector('.chat-modal-close');
                if (closeBtn) {
                    closeBtn.click();
                    return true;
                }
                return false;
            });
            await page.waitForTimeout(1000);

            const modalClosed = await page.evaluate(async () => {
                const iframe = document.getElementById('appIframe-my');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const modal = iframeDoc.querySelector('.modal.show') || iframeDoc.querySelector('.chat-modal-wrapper');
                return !modal;
            });
            addTest('UI - 关闭聊天窗口', modalClosed, modalClosed ? '窗口已关闭' : '窗口未关闭');
        } else {
            addTest('UI - 打开聊天窗口', false, '未找到 chatBar 按钮');
            addTest('UI - 关闭聊天窗口', false, '未找到 chatBar 按钮');
        }

        console.log('\n========== 第7步：UI 连续稳定性测试 (iframe 上下文) ==========\n');
        let successCount = 0;
        for (let i = 0; i < 5; i++) {
            const result = await page.evaluate(async () => {
                const iframe = document.getElementById('appIframe-my');
                if (!iframe) return { found: false };
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const chatBar = iframeDoc.getElementById('chatBar');
                if (!chatBar) return { found: false };

                chatBar.click();
                return new Promise(resolve => {
                    setTimeout(() => {
                        const modal = iframeDoc.querySelector('.modal.show') || iframeDoc.querySelector('.chat-modal-wrapper');
                        if (!modal) {
                            resolve({ opened: false });
                            return;
                        }
                        const closeBtn = iframeDoc.querySelector('.chat-modal-close');
                        if (closeBtn) closeBtn.click();
                        setTimeout(() => {
                            const closedModal = iframeDoc.querySelector('.modal.show') || iframeDoc.querySelector('.chat-modal-wrapper');
                            resolve({ opened: true, closed: !closedModal });
                        }, 500);
                    }, 1000);
                });
            });
            if (result.found && result.opened && result.closed) successCount++;
        }
        addTest('UI - 连续打开/关闭 5 次', successCount === 5, `成功 ${successCount}/5 次`);

        console.log('\n========== 测试报告总结 ==========\n');
        const passed = results.filter(r => r.status === 'PASS').length;
        const failed = results.filter(r => r.status === 'FAIL').length;
        console.log(`总测试数: ${results.length}`);
        console.log(`通过: ${passed} ✅`);
        console.log(`失败: ${failed} ❌`);
        console.log(`成功率: ${Math.round(passed / results.length * 100)}%`);

        const report = {
            date: new Date().toISOString(),
            summary: { total: results.length, passed, failed, rate: `${Math.round(passed / results.length * 100)}%` },
            results
        };

        const fs = require('fs');
        fs.writeFileSync('docs/chat模块测试报告.json', JSON.stringify(report, null, 2));

        const mdReport = `# Chat 模块单元测试报告

**测试日期**: ${report.date}

## 测试摘要

| 指标 | 数值 |
|------|------|
| 总测试数 | ${report.summary.total} |
| 通过 | ${report.summary.passed} |
| 失败 | ${report.summary.failed} |
| 成功率 | ${report.summary.rate} |

## 详细结果

| 测试项 | 状态 | 说明 |
|--------|------|------|
${results.map(r => `| ${r.name} | ${r.status === 'PASS' ? '✅ PASS' : '❌ FAIL'} | ${r.message} |`).join('\n')}

## 测试环境

- 平台: ZenTao PMS (Docker)
- 测试用户: admin
- 测试范围: Chat 模块 Model/View/Controller
- 特殊说明: chatBar 在 iframe (#appIframe-my) 内部，需要从 iframe 上下文查找
`;
        fs.writeFileSync('docs/chat模块测试报告.md', mdReport);
        console.log('\n报告已保存到 docs/chat模块测试报告.md');

    } catch (error) {
        console.error('测试异常:', error.message);
        results.push({ name: '测试执行', status: 'FAIL', message: error.message });
    }

    await browser.close();
}

runTests();
