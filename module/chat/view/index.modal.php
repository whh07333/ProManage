<?php
/**
 * The index view file of chat module - modal version.
 */
?>
<style>
.chat-modal-wrapper {display: flex; height: 75vh; max-height: 85vh; min-width: 900px; overflow: hidden; position: relative; background: #fff; border-radius: 12px;}
.chat-modal-dialog {width: 900px !important; max-width: 95vw !important;}
.chat-modal-close {position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(0,0,0,0.06); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #666; z-index: 10; transition: all 0.2s;}
.chat-modal-close:hover {background: rgba(0,0,0,0.12); color: #333; transform: rotate(90deg);}
.chat-modal-sidebar {width: 280px; min-width: 280px; border-right: 1px solid #e8e8e8; display: flex; flex-direction: column; background: #f8f9fa;}
.chat-modal-sidebar-header {padding: 20px; border-bottom: 1px solid #e8e8e8; background: #fff;}
.chat-modal-sidebar-header h3 {margin: 0 0 14px 0; font-size: 18px; font-weight: 600; color: #1a1a1a;}
.chat-modal-sidebar-body {flex: 1; overflow-y: auto; background: #fff;}
.chat-modal-tabs {display: flex; border-bottom: 1px solid #e8e8e8; background: #f8f9fa;}
.chat-modal-tabs .tab-item {flex: 1; padding: 14px; text-align: center; cursor: pointer; font-size: 14px; color: #666; border-bottom: 2px solid transparent; transition: all 0.2s;}
.chat-modal-tabs .tab-item:hover {color: #333; background: #f0f0f0;}
.chat-modal-tabs .tab-item.active {color: #1890ff; border-bottom-color: #1890ff; font-weight: 500;}
.room-list, .contact-list {list-style: none; padding: 8px; margin: 0;}
.room-item, .contact-item {padding: 14px 16px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s;}
.room-item:hover, .contact-item:hover {background: #f0f5ff;}
.room-item.active, .contact-item.active {background: #e6f0ff; border-left: 3px solid #1890ff;}
.room-item .room-name {font-weight: 600; font-size: 14px; color: #1a1a1a; margin-bottom: 6px;}
.room-item .room-info {font-size: 12px; color: #999;}
.room-item .room-type {display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; background: #f0f0f0; color: #666;}
.room-item .room-last-msg {font-size: 12px; color: #999; margin-top: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
.room-item .unread-badge, .contact-item .unread-badge {display: inline-block; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px; background: #ff4d4f; color: #fff; font-size: 11px; font-weight: 600; text-align: center; line-height: 18px; position: absolute; top: 8px; right: 8px;}
.room-item .room-left {padding-left: 24px; position: relative;}
.contact-item .contact-avatar {width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #1890ff, #70b8ff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; float: left; margin-right: 12px; font-size: 16px;}
.contact-item .contact-info {overflow: hidden;}
.contact-item .contact-name {font-weight: 600; font-size: 14px; color: #1a1a1a;}
.contact-item .contact-dept {font-size: 12px; color: #999; margin-top: 2px;}
.contact-item .contact-status {font-size: 12px; color: #52c41a;}
.chat-modal-main {flex: 1; display: flex; flex-direction: column; background: #fff;}
.chat-modal-header {padding: 16px 50px 16px 20px; border-bottom: 1px solid #e8e8e8; display: flex; align-items: center; background: #fff;}
.chat-modal-header h3 {margin: 0; font-size: 16px; font-weight: 600; color: #1a1a1a;}
.chat-modal-header .room-actions {display: flex; gap: 8px;}
.chat-modal-body {flex: 1; overflow-y: auto; padding: 20px; background: #f5f7fa;}
.message-list {list-style: none; padding: 0; margin: 0;}
.message-item {margin-bottom: 16px; display: flex; animation: fadeIn 0.3s ease;}
@keyframes fadeIn {from {opacity: 0; transform: translateY(10px);} to {opacity: 1; transform: translateY(0);}}
.message-item.own {flex-direction: row-reverse;}
.message-item.own .message-bubble {background: linear-gradient(135deg, #1890ff, #70b8ff); color: #fff; border-radius: 18px 4px 18px 18px;}
.message-item.own .message-sender {color: rgba(255,255,255,0.8);}
.message-item.own .message-time {color: rgba(255,255,255,0.6);}
.message-bubble {max-width: 65%; padding: 12px 16px; border-radius: 4px 18px 18px 18px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);}
.message-sender {font-size: 12px; font-weight: 600; color: #1890ff; margin-bottom: 4px;}
.message-time {font-size: 11px; color: #999; margin-top: 4px;}
.message-content {font-size: 14px; line-height: 1.5; color: #333;}
.chat-modal-footer {padding: 16px 20px; border-top: 1px solid #e8e8e8; background: #fff;}
.message-form {display: flex; gap: 12px; align-items: center;}
.message-form .form-group {flex: 1;}
.message-input {width: 100%; padding: 12px 18px; border: 1px solid #e8e8e8; border-radius: 24px; font-size: 14px; outline: none; transition: all 0.2s;}
.message-input:focus {border-color: #1890ff; box-shadow: 0 0 0 2px rgba(24,144,255,0.1);}
.empty-room {text-align: center; color: #999; padding: 80px 20px;}
.empty-room i {font-size: 56px; margin-bottom: 16px; color: #d9d9d9;}
.empty-room p {font-size: 14px; color: #999;}
.empty-room .empty-hint {font-size: 12px; color: #bfbfbf; margin-top: 8px;}
#sendBtn {width: 48px; height: 48px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center;}
#sendBtn i {font-size: 18px;}
.member-list {list-style: none; padding: 0; margin: 0;}
.member-item {padding: 12px 16px; border-radius: 8px; margin-bottom: 4px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s;}
.member-item:hover {background: #f0f5ff;}
.member-item .member-info {display: flex; align-items: center; gap: 12px;}
.member-item .member-avatar {width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #1890ff, #70b8ff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;}
.member-item .member-name {font-weight: 600; font-size: 14px; color: #1a1a1a;}
.member-item .member-account {font-size: 12px; color: #999;}
.member-item .remove-btn {padding: 4px 12px; border-radius: 4px; border: 1px solid #ff4d4f; color: #ff4d4f; background: transparent; cursor: pointer; font-size: 12px; transition: all 0.2s;}
.member-item .remove-btn:hover {background: #ff4d4f; color: #fff;}
</style>

<div class="chat-modal-wrapper" id="chatModalWrapper">
  <button type="button" class="chat-modal-close" data-dismiss="modal" aria-label="Close">&times;</button>
  <div class="chat-modal-sidebar">
    <div class="chat-modal-sidebar-header">
      <h3><?php echo $lang->chat->common;?></h3>
      <button class="btn btn-primary btn-sm btn-block" id="createRoomBtn">
        <i class="icon icon-plus"></i> <?php echo $lang->chat->createRoom;?>
      </button>
    </div>
    <div class="chat-modal-tabs">
      <div class="tab-item active" data-tab="rooms"><?php echo $lang->chat->rooms;?></div>
      <div class="tab-item" data-tab="contacts"><?php echo $lang->chat->contacts;?></div>
    </div>
    <div class="chat-modal-sidebar-body">
      <div id="roomsTab">
        <?php if(empty($rooms)):?>
        <div style="padding:20px; color:#999; font-size:13px; text-align:center;">
          <?php echo $lang->chat->noRooms;?>
        </div>
        <?php else:?>
        <ul class="room-list" id="roomList">
          <?php foreach($rooms as $room):?>
          <li class="room-item" data-room-id="<?php echo $room->id;?>">
            <div class="room-left">
              <div class="room-name"><?php echo $room->name;?></div>
              <div class="room-info">
                <span class="room-type"><?php echo isset($lang->chat->roomTypes[$room->type]) ? $lang->chat->roomTypes[$room->type] : $room->type;?></span>
              </div>
            </div>
          </li>
          <?php endforeach;?>
        </ul>
        <?php endif;?>
      </div>
      <div id="contactsTab" style="display:none;">
        <ul class="contact-list" id="contactsList" style="list-style:none; margin:0; padding:0;">
        </ul>
      </div>
    </div>
  </div>

  <div class="chat-modal-main">
    <div class="chat-modal-header" id="chatHeader" style="display:none;">
      <h3 id="currentRoomName" style="margin:0; flex:1;"><?php echo $lang->chat->selectRoom;?></h3>
      <button class="btn btn-sm btn-default" id="addMemberBtn" style="margin-left:8px;"><i class="icon icon-plus"></i> <?php echo $lang->chat->addMember;?></button>
    </div>
    <div class="chat-modal-body" id="chatMessages">
      <div class="empty-room">
        <i class="icon icon-chat"></i>
        <p><?php echo $lang->chat->selectRoom;?></p>
      </div>
    </div>
    <div class="chat-modal-footer" id="chatFooter" style="display:none;">
      <form id="messageForm" class="message-form">
        <input type="hidden" id="roomID" name="roomID" value="">
        <div class="form-group">
          <input type="text" id="messageContent" name="content" class="message-input" placeholder="<?php echo $lang->chat->sendMessage;?>" disabled>
        </div>
        <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
          <i class="icon icon-send"></i>
        </button>
      </form>
    </div>
  </div>
</div>

<div id="memberSelectModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; width:480px; max-width:90vw; max-height:80vh; display:flex; flex-direction:column; overflow:hidden;">
    <div style="padding:20px; border-bottom:1px solid #e8e8e8; display:flex; align-items:center; justify-content:space-between;">
      <h3 style="margin:0; font-size:16px; font-weight:600;"><?php echo $lang->chat->addMember;?></h3>
      <button id="closeMemberModal" style="background:none; border:none; font-size:24px; cursor:pointer; color:#999;">&times;</button>
    </div>
    <div style="padding:16px; overflow-y:auto; flex:1;">
      <div id="memberSelectList" style="margin-bottom:16px;"></div>
      <div id="memberSearchArea" style="margin-top:12px;">
        <input type="text" id="memberSearchInput" placeholder="<?php echo $lang->chat->searchUser;?>" style="width:100%; padding:10px 14px; border:1px solid #e8e8e8; border-radius:8px; font-size:14px; box-sizing:border-box;">
      </div>
    </div>
    <div style="padding:16px; border-top:1px solid #e8e8e8; display:flex; justify-content:flex-end; gap:8px;">
      <button class="btn btn-default" id="cancelMemberBtn"><?php echo $lang->chat->cancel;?></button>
      <button class="btn btn-primary" id="confirmAddMemberBtn"><?php echo $lang->chat->confirm;?></button>
    </div>
  </div>
</div>

  <div class="create-room-modal" id="createRoomModal" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div class="create-room-dialog" style="background:#fff; border-radius:12px; padding:28px 28px 24px; width:380px; box-shadow:0 8px 32px rgba(0,0,0,0.15);">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <h3 style="margin:0; font-size:17px; font-weight:600; color:#1a1a1a;"><?php echo $lang->chat->createRoom;?></h3>
        <button type="button" class="create-room-modal-close" style="width:28px; height:28px; border-radius:50%; border:none; background:rgba(0,0,0,0.06); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; color:#666;">&times;</button>
      </div>
      <div style="margin-bottom:18px;">
        <label style="display:block; font-size:13px; color:#666; margin-bottom:8px; font-weight:500;"><?php echo $lang->chat->roomName;?></label>
        <input type="text" id="newRoomName" class="form-control" placeholder="<?php echo $lang->chat->enterRoomName;?>" maxlength="50" style="width:100%; padding:10px 14px; border:1px solid #e8e8e8; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;" />
        <div id="roomNameError" style="color:#ff4d4f; font-size:12px; margin-top:6px; display:none;"></div>
      </div>
      <div style="margin-bottom:22px;">
        <label style="display:block; font-size:13px; color:#666; margin-bottom:8px; font-weight:500;"><?php echo $lang->chat->roomType;?></label>
        <select id="newRoomType" class="form-control" style="width:100%; padding:10px 14px; border:1px solid #e8e8e8; border-radius:8px; font-size:14px; outline:none; background:#fff;">
          <?php foreach($lang->chat->roomTypes as $key => $value):?>
          <option value="<?php echo $key;?>"><?php echo $value;?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" class="btn btn-sm" id="cancelCreateRoom" style="padding:8px 20px; border-radius:6px; border:1px solid #e8e8e8; background:#fff; cursor:pointer; font-size:14px; color:#666;"><?php echo $lang->chat->cancel;?></button>
        <button type="button" class="btn btn-sm btn-primary" id="confirmCreateRoom" style="padding:8px 20px; border-radius:6px; border:none; background:#1890ff; cursor:pointer; font-size:14px; color:#fff;"><?php echo $lang->chat->confirm;?></button>
      </div>
    </div>
  </div>

<script>
(function() {
    var currentUser = '<?php echo $app->user->account;?>';
    var $modal = document.getElementById('chatModalWrapper');
    var pollingInterval = null;
    var pollingRoomID = null;
    var lastMessageId = {};

    function startPolling() {
        stopPolling();
        pollingInterval = setInterval(function() {
            pollAllRooms();
        }, 3000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    function pollAllRooms() {
        var roomItems = document.querySelectorAll('.room-item');
        var contactItems = document.querySelectorAll('.contact-item');

        roomItems.forEach(function(item) {
            var roomId = parseInt(item.dataset.roomId);
            if (roomId) pollRoom(roomId);
        });

        contactItems.forEach(function(item) {
            var account = item.dataset.account;
            if (account && account !== currentUser) {
                pollContact(account);
            }
        });
    }

    function pollRoom(roomID) {
        if (!roomID) return;
        var formData = new FormData();
        formData.append('roomID', roomID);
        fetch('/index.php?m=chat&f=getMessages', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.result === 'success' && response) {
                var messages = response.data || response || [];
                if (!Array.isArray(messages)) {
                    var arr = [];
                    for (var key in messages) {
                        if (key !== 'result' && key !== 'message' && typeof messages[key] === 'object') {
                            arr.push(messages[key]);
                        }
                    }
                    messages = arr;
                }
                processNewMessages(messages, roomID);
            }
        });
    }

    function pollContact(account) {
        var formData = new FormData();
        formData.append('account', account);
        fetch('/index.php?m=chat&f=startPrivateChat', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.result === 'success' && response.roomID) {
                pollRoom(response.roomID);
            }
        });
    }

    function processNewMessages(messages, polledRoomID) {
        var currentRoomID = parseInt(document.getElementById('roomID').value);

        for (var i = 0; i < messages.length; i++) {
            var msg = messages[i];
            var msgRoomID = parseInt(msg.roomID);
            var lastId = lastMessageId[msgRoomID] || 0;

            if (parseInt(msg.id) > lastId) {
                if (msgRoomID === currentRoomID && msg.sender !== currentUser) {
                    addMessageToUI(msg, false);
                } else if (msgRoomID !== currentRoomID && msg.sender !== currentUser) {
                    unreadCounts[msgRoomID] = (unreadCounts[msgRoomID] || 0) + 1;
                    var targetItem = document.querySelector('.room-item[data-room-id="' + msgRoomID + '"]');
                    if (targetItem) updateUnreadBadge(targetItem, unreadCounts[msgRoomID]);
                    var contactItem = document.querySelector('.contact-item[data-account="' + msg.sender + '"]');
                    if (contactItem) updateUnreadBadge(contactItem, unreadCounts[msgRoomID]);
                    updateChatBarBadge();
                }
            }
        }

        if (messages.length > 0) {
            lastMessageId[msgRoomID] = parseInt(messages[messages.length - 1].id);
        }
    }

    function updateChatBarBadge() {
        var totalUnread = 0;
        for (var roomId in unreadCounts) {
            totalUnread += unreadCounts[roomId];
        }
        var chatBar = window.parent.document.getElementById('chatBar');
        if (chatBar) {
            var existingBadge = chatBar.querySelector('.unread-badge');
            if (totalUnread > 0) {
                if (!existingBadge) {
                    existingBadge = document.createElement('span');
                    existingBadge.className = 'unread-badge';
                    existingBadge.style.cssText = 'position: absolute; top: -3px; right: -3px; min-width: 18px; height: 18px; border-radius: 9px; background: #ff4d4f; color: #fff; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; padding: 0 4px;';
                    chatBar.style.position = 'relative';
                    chatBar.appendChild(existingBadge);
                }
                existingBadge.textContent = totalUnread > 99 ? '99+' : totalUnread;
            } else if (existingBadge) {
                existingBadge.remove();
            }
        }
    }

    function refreshChatBarBadge() {
        var formData = new FormData();
        formData.append('dummy', '1');
        console.log('[refreshChatBarBadge] Making request');
        fetch('/index.php?m=chat&f=getUnreadCount', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) {
            console.log('[refreshChatBarBadge] Response status:', res.status, res.statusText);
            console.log('[refreshChatBarBadge] Response headers:', res.headers.get('content-type'));
            return res.text();
        })
        .then(function(text) {
            console.log('[refreshChatBarBadge] Response text:', text);
            var response = JSON.parse(text);
            if (response.result === 'success' && response.data && response.data.unreadCount !== undefined) {
                var totalUnread = response.data.unreadCount;
                var chatBar = window.parent.document.getElementById('chatBar');
                if (chatBar) {
                    var existingBadge = chatBar.querySelector('.unread-badge');
                    if (totalUnread > 0) {
                        if (!existingBadge) {
                            existingBadge = document.createElement('span');
                            existingBadge.className = 'unread-badge';
                            existingBadge.style.cssText = 'position: absolute; top: -3px; right: -3px; min-width: 18px; height: 18px; border-radius: 9px; background: #ff4d4f; color: #fff; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; padding: 0 4px;';
                            chatBar.style.position = 'relative';
                            chatBar.appendChild(existingBadge);
                        }
                        existingBadge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                    } else if (existingBadge) {
                        existingBadge.remove();
                    }
                }
            }
        })
        .catch(function(err) { console.error('refreshChatBarBadge error:', err); });
    }

    document.querySelector('.chat-modal-tabs').addEventListener('click', function(e) {
        var tabItem = e.target.closest('.tab-item');
        if (!tabItem) return;
        var oldTab = document.querySelector('.chat-modal-tabs .tab-item.active');
        var oldTabName = oldTab ? oldTab.dataset.tab : '';
        var newTab = tabItem.dataset.tab;

        document.querySelectorAll('.chat-modal-tabs .tab-item').forEach(function(el) { el.classList.remove('active'); });
        tabItem.classList.add('active');
        document.getElementById('roomsTab').style.display = newTab === 'rooms' ? '' : 'none';
        document.getElementById('contactsTab').style.display = newTab === 'contacts' ? '' : 'none';

        if (newTab === 'contacts') {
            loadContacts(true);
        } else if (newTab === 'rooms' && oldTabName === 'contacts') {
            var firstRoom = document.querySelector('#roomList .room-item');
            if (firstRoom) enterRoom(parseInt(firstRoom.dataset.roomId));
        }
    });

    var contactsLoaded = false;
    var unreadCounts = {};
    function updateUnreadBadge(target, count) {
        var badge = target.querySelector('.unread-badge');
        if (count <= 0) {
            if (badge) badge.remove();
        } else {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'unread-badge';
                target.appendChild(badge);
            }
            badge.textContent = count > 99 ? '99+' : count;
        }
    }
    function loadContacts(autoEnterFirst) {
        if (contactsLoaded && !autoEnterFirst) return;
        contactsLoaded = true;
        fetch('/index.php?m=user&f=ajaxGetAllUsers', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(users) {
            var list = document.getElementById('contactsList');
            if (!list) return;
            var userArray = [];
            for (var key in users) {
                if (key !== 'result' && key !== 'message' && users[key] && users[key].account) {
                    userArray.push(users[key]);
                }
            }
            if (userArray.length === 0) {
                list.innerHTML = '<div style="padding:30px; text-align:center; color:#999;">暂无联系人</div>';
                return;
            }
            var html = '';
            userArray.forEach(function(user) {
                if (user.account === currentUser) return;
                html += '<li class="contact-item" data-account="' + user.account + '" style="position:relative;">';
                html += '<div class="contact-avatar">' + (user.realname ? user.realname.charAt(0).toUpperCase() : '?') + '</div>';
                html += '<div class="contact-info"><div class="contact-name">' + (user.realname || user.account) + '</div>';
                html += '<div class="contact-dept">' + (user.dept || '') + '</div></div>';
                html += '</li>';
            });
            list.innerHTML = html || '<div style="padding:30px; text-align:center; color:#999;">暂无联系人</div>';

            list.querySelectorAll('.contact-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    var account = this.dataset.account;
                    startPrivateChat(account);
                });
            });

            if (autoEnterFirst) {
                var firstContact = list.querySelector('.contact-item');
                if (firstContact) firstContact.click();
            }
        })
        .catch(function() {
            var list = document.getElementById('contactsList');
            if (list) list.innerHTML = '<div style="padding:30px; text-align:center; color:#999;">加载失败</div>';
        });
    }

    function startPrivateChat(account) {
        var formData = new FormData();
        formData.append('account', account);
        console.log('startPrivateChat called with account:', account);
        fetch('/index.php?m=chat&f=startPrivateChat', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            console.log('startPrivateChat response:', response);
            if (response.result === 'success') {
                var contactItem = document.querySelector('.contact-item[data-account="' + account + '"]');
                var privateName = contactItem ? contactItem.querySelector('.contact-name').textContent : account;
                console.log('Calling enterRoom with roomID:', response.roomID, 'privateName:', privateName);
                enterRoom(response.roomID, privateName, contactItem);
            } else {
                console.error('startPrivateChat failed:', response.message);
            }
        })
        .catch(function(err) {
            console.error('startPrivateChat error:', err);
        });
    }

    document.getElementById('roomList').addEventListener('click', function(e) {
        var roomItem = e.target.closest('.room-item');
        if (roomItem) enterRoom(parseInt(roomItem.dataset.roomId));
    });

    document.getElementById('createRoomBtn').addEventListener('click', function() {
        document.getElementById('newRoomName').value = '';
        document.getElementById('roomNameError').style.display = 'none';
        document.getElementById('createRoomModal').style.display = 'flex';
        document.getElementById('newRoomName').focus();
    });

    document.querySelector('.create-room-modal-close').addEventListener('click', function() {
        document.getElementById('createRoomModal').style.display = 'none';
    });

    document.getElementById('cancelCreateRoom').addEventListener('click', function() {
        document.getElementById('createRoomModal').style.display = 'none';
    });

    document.getElementById('createRoomModal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });

    document.getElementById('newRoomName').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('confirmCreateRoom').click();
        }
    });

    document.getElementById('confirmCreateRoom').addEventListener('click', function() {
        var name = document.getElementById('newRoomName').value.trim();
        var type = document.getElementById('newRoomType').value;
        var errorEl = document.getElementById('roomNameError');

        if (!name) {
            errorEl.textContent = '<?php echo $lang->chat->nameRequired;?>';
            errorEl.style.display = 'block';
            return;
        }
        if (name.length < 2) {
            errorEl.textContent = '<?php echo $lang->chat->nameTooShort;?>';
            errorEl.style.display = 'block';
            return;
        }

        errorEl.style.display = 'none';
        this.disabled = true;
        this.textContent = '<?php echo $lang->chat->creating;?>...';

        var formData = new FormData();
        formData.append('name', name);
        formData.append('type', type);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/index.php?m=chat&f=createRoom', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.withCredentials = true;
        xhr.onload = function() {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.result === 'success') {
                    document.getElementById('createRoomModal').style.display = 'none';
                    var roomList = document.getElementById('roomList');
                    var emptyMsg = roomList.parentElement.querySelector('.empty-room');
                    if (emptyMsg) emptyMsg.style.display = 'none';
                    var newRoom = document.createElement('li');
                    newRoom.className = 'room-item active';
                    newRoom.dataset.roomId = response.roomID;
                    newRoom.innerHTML = '<div class="room-left"><div class="room-name">' + name + '</div><div class="room-info"><span class="room-type">' + type + '</span></div></div>';
                    roomList.appendChild(newRoom);
                    enterRoom(response.roomID);
                    document.querySelectorAll('.room-item').forEach(function(item) { item.classList.remove('active'); });
                    newRoom.classList.add('active');
                } else {
                    errorEl.textContent = response.message || '<?php echo $lang->chat->createFailed;?>';
                    errorEl.style.display = 'block';
                }
            } catch(e) {
                errorEl.textContent = '<?php echo $lang->chat->createFailed;?>';
                errorEl.style.display = 'block';
            }
            document.getElementById('confirmCreateRoom').disabled = false;
            document.getElementById('confirmCreateRoom').textContent = '<?php echo $lang->chat->confirm;?>';
        };
        xhr.onerror = function() {
            errorEl.textContent = '<?php echo $lang->chat->createFailed;?>';
            errorEl.style.display = 'block';
            document.getElementById('confirmCreateRoom').disabled = false;
            document.getElementById('confirmCreateRoom').textContent = '<?php echo $lang->chat->confirm;?>';
        };
        xhr.send(formData);
    });

    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var roomID = document.getElementById('roomID').value;
        var content = document.getElementById('messageContent').value;
        if (!roomID || !content) return;

        var formData = new FormData();
        formData.append('roomID', roomID);
        formData.append('content', content);
        formData.append('type', 'text');

        var sendBtn = document.getElementById('sendBtn');
        var originalText = sendBtn.innerHTML;
        sendBtn.innerHTML = '<i class="icon icon-loading"></i>';
        sendBtn.disabled = true;

        fetch('/index.php?m=chat&f=sendMessage', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            sendBtn.innerHTML = originalText;
            sendBtn.disabled = false;
            if (response.result === 'success') {
                var messageData = response.data || response;
                addMessageToUI(messageData, true);
                document.getElementById('messageContent').value = '';
                lastMessageId[messageData.roomID] = parseInt(messageData.id);
            }
        })
        .catch(function(err) {
            sendBtn.innerHTML = originalText;
            sendBtn.disabled = false;
            console.error('Send message error:', err);
        });
    });

    window.enterRoom = function(roomID, privateName, contactItem) {
        console.log('[enterRoom] START - roomID:', roomID, 'privateName:', privateName);
        document.querySelectorAll('.room-item').forEach(function(el) { el.classList.remove('active'); });
        document.querySelectorAll('.contact-item').forEach(function(el) { el.classList.remove('active'); });
        var activeItem = document.querySelector('.room-item[data-room-id="' + roomID + '"]');
        if (activeItem) activeItem.classList.add('active');
        if (contactItem) contactItem.classList.add('active');

        unreadCounts[roomID] = 0;
        if (activeItem) updateUnreadBadge(activeItem, 0);
        if (contactItem) updateUnreadBadge(contactItem, 0);
        refreshChatBarBadge();

        var formData = new FormData();
        formData.append('roomID', roomID);
        fetch('/index.php?m=chat&f=markAsRead', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        }).catch(function(err) { console.error('markAsRead error:', err); });

        document.getElementById('roomID').value = roomID;
        document.getElementById('messageContent').disabled = false;
        document.getElementById('sendBtn').disabled = false;
        document.getElementById('chatHeader').style.display = '';
        document.getElementById('chatFooter').style.display = '';

        var roomName = privateName || (activeItem ? activeItem.querySelector('.room-name').textContent : '');
        document.getElementById('currentRoomName').textContent = roomName;

        console.log('Getting messages for roomID:', roomID);
        fetch('/index.php?m=chat&f=getMessages', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            console.log('getMessages response:', response);
            var messages = response.data || response || [];
            if (!Array.isArray(messages)) {
                var arr = [];
                for (var key in messages) {
                    if (key !== 'result' && key !== 'message' && typeof messages[key] === 'object') {
                        arr.push(messages[key]);
                    }
                }
                messages = arr;
            }
            console.log('[DEBUG] messages length:', messages.length, 'messages:', messages);
            var chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            if (messages.length > 0) {
                messages.forEach(function(msg) {
                    addMessageToUI(msg);
                });
                lastMessageId[roomID] = messages[messages.length - 1].id;
            } else {
                chatMessages.innerHTML = '<div class="empty-room"><i class="icon icon-chat"></i><p><?php echo $lang->chat->noMessages;?></p></div>';
            }
            scrollToBottom();
            startPolling();
        })
        .catch(function(err) {
            console.error('getMessages error:', err);
        });
    };

    var firstRoom = document.querySelector('#roomList .room-item');
    if (firstRoom) {
        var roomID = parseInt(firstRoom.dataset.roomId);
        if (roomID) enterRoom(roomID);
    }

    document.querySelector('.chat-modal-close').addEventListener('click', function() {
        stopPolling();
    });

    function addMessageToUI(message, isOwnMessage) {
        var isOwn = isOwnMessage || message.sender === currentUser;
        var html = '<li class="message-item' + (isOwn ? ' own' : '') + '" data-message-id="' + message.id + '">' +
            '<div class="message-bubble">' +
            '<div class="message-sender">' + (isOwn ? '<?php echo $lang->chat->you;?>' : (message.senderName || message.sender)) + '</div>' +
            '<div class="message-content">' + message.content + '</div>' +
            '<div class="message-time">' + (message.createdDate || '') + '</div>' +
            '</div>' +
            '</li>';

        var emptyRoom = document.querySelector('#chatMessages .empty-room');
        if (emptyRoom) emptyRoom.remove();
        document.getElementById('chatMessages').insertAdjacentHTML('beforeend', html);
        scrollToBottom();

        if (!isOwn) {
            var currentRoomID = parseInt(document.getElementById('roomID').value);
            var msgRoomID = parseInt(message.roomID);
            if (currentRoomID !== msgRoomID) {
                unreadCounts[msgRoomID] = (unreadCounts[msgRoomID] || 0) + 1;
                var targetItem = document.querySelector('.room-item[data-room-id="' + msgRoomID + '"]');
                if (targetItem) updateUnreadBadge(targetItem, unreadCounts[msgRoomID]);
                var contactItem = document.querySelector('.contact-item[data-account="' + message.sender + '"]');
                if (contactItem) updateUnreadBadge(contactItem, unreadCounts[msgRoomID]);
            }
        }
    }

    function scrollToBottom() {
        var el = document.getElementById('chatMessages');
        el.scrollTop = el.scrollHeight;
    }

    var memberSelectModal = document.getElementById('memberSelectModal');
    var selectedMemberAccount = null;

    document.getElementById('addMemberBtn').addEventListener('click', function() {
        var roomID = document.getElementById('roomID').value;
        if (!roomID) return;
        selectedMemberAccount = null;
        document.getElementById('memberSearchInput').value = '';
        document.getElementById('memberSelectList').innerHTML = '<div style="text-align:center; color:#999; padding:20px;">Loading...</div>';
        memberSelectModal.style.display = 'flex';

        fetch('/index.php?m=user&f=ajaxGetAllUsers', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(users) {
            renderMemberList(users.data || []);
        })
        .catch(function() {
            document.getElementById('memberSelectList').innerHTML = '<div style="text-align:center; color:#999; padding:20px;">Failed to load users</div>';
        });
    });

    document.getElementById('closeMemberModal').addEventListener('click', function() {
        memberSelectModal.style.display = 'none';
    });

    document.getElementById('cancelMemberBtn').addEventListener('click', function() {
        memberSelectModal.style.display = 'none';
    });

    document.getElementById('memberSearchInput').addEventListener('input', function(e) {
        var keyword = e.target.value.toLowerCase();
        var items = document.querySelectorAll('#memberSelectList .member-item');
        items.forEach(function(item) {
            var name = (item.dataset.name || '').toLowerCase();
            var account = (item.dataset.account || '').toLowerCase();
            item.style.display = (name.indexOf(keyword) !== -1 || account.indexOf(keyword) !== -1) ? '' : 'none';
        });
    });

    document.getElementById('confirmAddMemberBtn').addEventListener('click', function() {
        if (!selectedMemberAccount) {
            alert('<?php echo $lang->chat->selectUser;?>');
            return;
        }
        var roomID = document.getElementById('roomID').value;
        var formData = new FormData();
        formData.append('roomID', roomID);
        formData.append('account', selectedMemberAccount);
        fetch('/index.php?m=chat&f=addMember', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.result === 'success') {
                memberSelectModal.style.display = 'none';
                loadRoomMembers(roomID);
            } else {
                alert(response.message);
            }
        });
    });

    function renderMemberList(users) {
        var list = document.getElementById('memberSelectList');
        if (!users || users.length === 0) {
            list.innerHTML = '<div style="text-align:center; color:#999; padding:20px;">No users found</div>';
            return;
        }
        list.innerHTML = '';
        users.forEach(function(user) {
            if (user.account === currentUser) return;
            var div = document.createElement('div');
            div.className = 'member-item';
            div.dataset.account = user.account;
            div.dataset.name = user.realname || user.account;
            div.innerHTML = '<div class="member-info"><div class="member-avatar">' + (user.realname ? user.realname.charAt(0).toUpperCase() : '?') + '</div><div><div class="member-name">' + (user.realname || user.account) + '</div><div class="member-account">@' + user.account + '</div></div></div>';
            div.addEventListener('click', function() {
                document.querySelectorAll('#memberSelectList .member-item').forEach(function(el) { el.style.background = ''; });
                div.style.background = '#e6f0ff';
                selectedMemberAccount = user.account;
            });
            list.appendChild(div);
        });
    }

    function loadRoomMembers(roomID) {
        var formData = new FormData();
        formData.append('roomID', roomID);
        fetch('/index.php?m=chat&f=getMembers', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(function(res) { return res.json(); })
        .then(function(response) {
            if (response.result === 'success') {
                console.log('Members loaded:', response.data);
            }
        });
    }

    memberSelectModal.addEventListener('click', function(e) {
        if (e.target === memberSelectModal) memberSelectModal.style.display = 'none';
    });
})();
</script>
